var takeSnapshotUI = createClickFeedbackUI();

var facemashModel = null;
var video;
var gvtryon_take_photo_button;
var gvtryon_toggle_full_screen_button;
var gvtryon_switch_camera_button;
var amountOfCameras = 0;
var currentFacingMode = 'user';//'environment';
var mode = 'video';
var cameraFrame = null;
var breakintervalfunc = false;
let faceEyesStartToEnd = [124,168,353,197];

let newCloneElement = jQuery('#frameimage').clone();
jQuery(newCloneElement).attr('id','newimge1');
// this function counts the amount of video inputs
// it replaces DetectRTC that was previously implemented.
function deviceCount() {
  return new Promise(function (resolve) {
    var videoInCount = 0;

    navigator.mediaDevices
      .enumerateDevices()
      .then(function (devices) {
        devices.forEach(function (device) {
          if (device.kind === 'video') {
            device.kind = 'videoinput';
          }

          if (device.kind === 'videoinput') {
            videoInCount++;
            console.log('videocam: ' + device.label);
          }
        });

        resolve(videoInCount);
      })
      .catch(function (err) {
        addErrorMessagePopup(err.name + ': ' + err.message);
        resolve(0);
      });
  });
}
function checkbrowser(){
    // Opera 8.0+
    var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    
    // Firefox 1.0+
    var isFirefox = typeof InstallTrigger !== 'undefined';
    
    // Safari 3.0+ "[object HTMLElementConstructor]" 
    var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && window['safari'].pushNotification));
    
    // Internet Explorer 6-11
    var isIE = /*@cc_on!@*/false || !!document.documentMode;
    
    // Edge 20+
    var isEdge = !isIE && !!window.StyleMedia;
    
    // Chrome 1 - 79
    var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
    
    // Edge (based on chromium) detection
    var isEdgeChromium = isChrome && (navigator.userAgent.indexOf("Edg") != -1);
    
    // Blink engine detection
    var isBlink = (isChrome || isOpera) && !!window.CSS;
    var isMobile = false;
    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
      // true for mobile device
      isMobile = true;
    }else{
      // false for not mobile device
      isMobile = false;
    }
    
    var output = 'Detecting browsers by ducktyping:<hr>';
    output += 'ismobile:'+isMobile + '<br>';
    output += 'isFirefox: ' + isFirefox + '<br>';
    output += 'isChrome: ' + isChrome + '<br>';
    output += 'isSafari: ' + isSafari + '<br>';
    output += 'isOpera: ' + isOpera + '<br>';
    output += 'isIE: ' + isIE + '<br>';
    output += 'isEdge: ' + isEdge + '<br>';
    output += 'isEdgeChromium: ' + isEdgeChromium + '<br>';
    output += 'isBlink: ' + isBlink + '<br>';
}
jQuery(document).ready(function(){
   
  gvtryon_start_camera_button = document.getElementById('gvtryon_start_camera_button');
  gvtryon_start_camera_button.addEventListener('click', function () {
    startCamera();
  });
  gvtryon_stop_camera_button = document.getElementById('gvtryon_stop_camera_button');
  gvtryon_stop_camera_button.addEventListener('click', function () {
    stopCamera();
  });
  gvtryon_set_frame_photo_button = document.getElementById('gvtryon_set_frame_photo_button');
  gvtryon_set_frame_photo_button.addEventListener('click', function () {
    setFrameOnImage();
  });
  gvtryon_clear_frame_button = document.getElementById('gvtryon_clear_frame_button');
  gvtryon_clear_frame_button.addEventListener('click', function (){
    clearOverlay();
  });
  gvtryon_download_snap = document.getElementById('gvtryon_download_snap');
  gvtryon_download_snap.addEventListener('click', function (){
    getanddownloadsnap();
  })

  jQuery('.gvtryon_content_wrap').attr('mode',mode);
  if(jQuery('#gvt_mode_check').length > 0){
    document.getElementById('gvt_mode_check').addEventListener('change',function(e) {
        mode = e.target.checked ? 'video' : 'image';
        modeChange(mode);
    });
  }
  
  jQuery('#gvtryon_button').click(function() {
      jQuery('#gvtryon_modal').toggleClass('show');
      jQuery('#gvtryon_start_camera_button').trigger('click');
      jQuery('body').toggleClass('scrollof');
  });
  jQuery('#gvtryon_close_button').click(function() {
    stopCamera();
    jQuery('body').removeClass('scrollof');
    jQuery('#gvtryon_modal').removeClass('show');
  });
});

function modeChange(mode){
  jQuery('.gvtryon_content_wrap').attr('mode',mode);
  breakintervalfunc = true;
    setTimeout(function(){clearOverlay()},200);
}
function stopCamera(event) {
   // stop any active streams in the window
  if (window.stream) {
    window.stream.getTracks().forEach(function (track) {
      // console.log(track);
      track.stop();
      jQuery('.gvtryon_content_wrap').removeClass('cameraStarted')
     
    });
  } 
  breakintervalfunc = true;
  if(mode == 'video'){
    setTimeout(function(){clearOverlay()},200);
  }
 
}
function closePopup(){
    stopCamera();
    jQuery('body').removeClass('scrollof');
    jQuery('#gvtryon_modal').removeClass('show');
}
function removeErrorMessagePopup(){
    jQuery('#gvtryon_gui_controls').find('.errorPopup').remove();
}
function addErrorMessagePopup(errormessage) {
    let strHtml = '<div class="errorPopup">';
    strHtml += '<p>'+errormessage+'</p>';
    strHtml += '<p><button class="gvt_plainbutton" type="button" onClick="closePopup();">Close</button></p>';
    strHtml += '</div>';
    jQuery(strHtml).appendTo('#gvtryon_gui_controls')
}
async function startCamera(event) {
    removeErrorMessagePopup();
   
  // check if mediaDevices is supported
  if (
    navigator.mediaDevices &&
    navigator.mediaDevices.getUserMedia &&
    navigator.mediaDevices.enumerateDevices
  ) {
      
    // first we call getUserMedia to trigger permissions
    // we need this before deviceCount, otherwise Safari doesn't return all the cameras
    // we need to have the number in order to display the switch front/back button
    try {
        var constraints = { audio: false, video: true };
        var cameraon = false;
        let mediaDevicesObj = await navigator.mediaDevices
          .getUserMedia(constraints)
          .then(function (stream) {
            cameraon = true;
            stream.getTracks().forEach(function (track) {
              track.stop();
            });
            deviceCount().then(function (deviceCount) {
                amountOfCameras = deviceCount;
                // init the UI and the camera stream
                initCameraUI();
                initCameraStream();
                //jQuery('#gvtryon_set_frame_photo_button').trigger('click');
                setfacemashModel();
                clearOverlay();
            });
            return stream;
          })
          .catch(function (error) {
              cameraon = false;
            //https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
            if (error === 'PermissionDeniedError' || error.message === 'Permission denied') {
                addErrorMessagePopup('Permission denied. Please refresh and give permission.');
                navigator.permissions.query({name: 'camera'})
                 .then((permissionObj) => {
                  addErrorMessagePopup(permissionObj.state);
                 })
                 .catch((error) => {
                  addErrorMessagePopup(error);
                 })
            }
          });
          setTimeout(function(){
              if(cameraon == false){
                   //addErrorMessagePopup('Virtual Try on only works on Chrome and Opera browsers');
              }
            else if(typeof mediaDevicesObj != 'undefined' && typeof mediaDevicesObj.id !== 'undefined' && mediaDevicesObj.id !== ''){
                removeErrorMessagePopup();
            }else{
                // addErrorMessagePopup('Virtual Try on only works on Chrome and Opera browsers');
                // addErrorMessagePopup('Media Device is may not supported to this browser please try with other browser');
            }
          },2000);
      } catch(err) {
          addErrorMessagePopup(err);
          
      }
  
    
  } else {
    addErrorMessagePopup(
      'Mobile camera is not supported by browser, or there is no camera detected/connected',
    );
  }
}

async function setfacemashModel() {
  facemashModel = await facemesh.load();
}

function initCameraUI() {
  video = document.getElementById('video');

  gvtryon_take_photo_button = document.getElementById('gvtryon_take_photo_button');
  gvtryon_toggle_full_screen_button = document.getElementById('gvtryon_toggle_full_screen_button');
  gvtryon_switch_camera_button = document.getElementById('gvtryon_switch_camera_button');

  // https://developer.mozilla.org/nl/docs/Web/HTML/Element/button
  // https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/ARIA_Techniques/Using_the_button_role
  gvtryon_take_photo_button.addEventListener('click', function () {
    takeSnapshotUI();
    takeSnapshot();
  });
  var canvas = document.getElementById('video_canvas');
  

  context = canvas.getContext('2d');
  
  video.addEventListener('play', function () {
    var width = video.videoWidth;
    var height = video.videoHeight;

    canvas.width = width;
    canvas.height = height;
    var $this = this; //cache
    (function loop() {
        if (!$this.paused && !$this.ended) {
            context.drawImage($this, 0, 0, width, height);
            setTimeout(loop, 1000 / 250); // drawing at 250fps // drawing at 10fps
        }
    })();
  }, 0);

  // -- fullscreen part

  function fullScreenChange() {
    if (screenfull.isFullscreen) {
      gvtryon_toggle_full_screen_button.setAttribute('aria-pressed', true);
    } else {
      gvtryon_toggle_full_screen_button.setAttribute('aria-pressed', false);
    }
  }

  if (screenfull.isEnabled) {
    screenfull.on('change', fullScreenChange);

    gvtryon_toggle_full_screen_button.style.display = 'block';

    // set init values
    fullScreenChange();

    gvtryon_toggle_full_screen_button.addEventListener('click', function () {
      screenfull.toggle(document.getElementById('container')).then(function () {
        console.log(
          'Fullscreen mode: ' +
            (screenfull.isFullscreen ? 'enabled' : 'disabled'),
        );
      });
    });
  } else {
    console.log("iOS doesn't support fullscreen (yet)");
  }

  // -- switch camera part
  if (amountOfCameras > 1) {
    gvtryon_switch_camera_button.style.display = 'block';

    gvtryon_switch_camera_button.addEventListener('click', function () {
      if (currentFacingMode === 'environment') currentFacingMode = 'user';
      else currentFacingMode = 'environment';

      initCameraStream();
    });
  }

  // Listen for orientation changes to make sure buttons stay at the side of the
  // physical (and virtual) buttons (opposite of camera) most of the layout change is done by CSS media queries
  // https://www.sitepoint.com/introducing-screen-orientation-api/
  // https://developer.mozilla.org/en-US/docs/Web/API/Screen/orientation
  window.addEventListener(
    'orientationchange',
    function () {
      // iOS doesn't have screen.orientation, so fallback to window.orientation.
      // screen.orientation will
      if (screen.orientation) angle = screen.orientation.angle;
      else angle = window.orientation;

      var guiControls = document.getElementById('gvtryon_gui_controls').classList;
      var vidContainer = document.getElementById('gvtryon_vid_container').classList;

      if (angle == 270 || angle == -90) {
        guiControls.add('left');
        vidContainer.add('left');
      } else {
        if (guiControls.contains('left')) guiControls.remove('left');
        if (vidContainer.contains('left')) vidContainer.remove('left');
      }

      //0   portrait-primary
      //180 portrait-secondary device is down under
      //90  landscape-primary  buttons at the right
      //270 landscape-secondary buttons at the left
    },
    false,
  );
}

// https://github.com/webrtc/samples/blob/gh-pages/src/content/devices/input-output/js/main.js
function initCameraStream() {
  // stop any active streams in the window
  if (window.stream) {
    window.stream.getTracks().forEach(function (track) {
      console.log(track);
      track.stop();
      jQuery('.gvtryon_content_wrap').removeClass('cameraStarted')

    });
  }

  // we ask for a square resolution, it will cropped on top (landscape)
  // or cropped at the sides (landscape)
  var size = 1280;

  var constraints = {
    audio: false,
    video: {
      width: { ideal: size },
      height: { ideal: size },
      //width: { min: 1024, ideal: window.innerWidth, max: 1920 },
      //height: { min: 776, ideal: window.innerHeight, max: 1080 },
      facingMode: currentFacingMode,
    },
  };

  navigator.mediaDevices
    .getUserMedia(constraints)
    .then(handleSuccess)
    .catch(handleError);

  function handleSuccess(stream) {
    jQuery('.gvtryon_content_wrap').addClass('cameraStarted');
    jQuery('.gvtryon_content_wrap').removeClass('capturedImg');
    window.stream = stream; // make stream available to browser console
    video.srcObject = stream;
    if (constraints.video.facingMode) {
      if (constraints.video.facingMode === 'environment') {
        gvtryon_switch_camera_button.setAttribute('aria-pressed', true);
      } else {
        gvtryon_switch_camera_button.setAttribute('aria-pressed', false);
      }
    }

    const track = window.stream.getVideoTracks()[0];
    const settings = track.getSettings();
    str = JSON.stringify(settings, null, 4);
    // console.log('settings ' + str);
  }

  function handleError(error) {
    console.error('getUserMedia() error: ', error);
  }
}

function takeSnapshot() {
  // if you'd like to show the canvas add it to the DOM
  // var canvas = document.createElement('canvas');
  jQuery('.gvtryon_content_wrap').addClass('capturedImg');
  var canvas = document.getElementById('video_cap_canvas');
  var width = video.videoWidth;
  var height = video.videoHeight;

  canvas.width = width;
  canvas.height = height;

  context = canvas.getContext('2d');
  context.drawImage(video, 0, 0, width, height);

  // polyfil if needed https://github.com/blueimp/JavaScript-Canvas-to-Blob

  // https://developers.google.com/web/fundamentals/primers/promises
  // https://stackoverflow.com/questions/42458849/access-blob-value-outside-of-canvas-toblob-async-function
  function getCanvasBlob(canvas) {
    return new Promise(function (resolve, reject) {
      canvas.toBlob(function (blob) {
        resolve(blob);
      }, 'image/jpeg');
    });
  }

  // some API's (like Azure Custom Vision) need a blob with image data
  getCanvasBlob(canvas).then(function (blob) {
    // do something with the image blob
  });
  stopCamera();
}

function getanddownloadsnap () {
  html2canvas(document.querySelector('#gvtryon_vid_container'), {
    onrendered: function(canvas) {
      // document.body.appendChild(canvas);
      return Canvas2Image.saveAsPNG(canvas);
    }
  });
}


// https://hackernoon.com/how-to-use-javascript-closures-with-confidence-85cd1f841a6b
// closure; store this in a variable and call the variable as function
// eg. var takeSnapshotUI = createClickFeedbackUI();
// takeSnapshotUI();

function createClickFeedbackUI() {
  // in order to give feedback that we actually pressed a button.
  // we trigger a almost black overlay
  var overlay = document.getElementById('video_overlay'); //.style.display;

  // sound feedback
  // var sndClick = new Howl({ src: ['snd/click.mp3'] });

  var overlayVisibility = false;
  var timeOut = 80;

  function setFalseAgain() {
    overlayVisibility = false;
    overlay.style.display = 'none';
  }

  return function () {
    if (overlayVisibility == false) {
      // sndClick.play();
      overlayVisibility = true;
      overlay.style.display = 'block';
      setTimeout(setFalseAgain, timeOut);
    }
  };
}

function interval(func, wait, times){
    var interv = function(w, t){
        return function(){
            if(breakintervalfunc == true){
                t = 0;
                breakintervalfunc = false;
            }
            if(typeof t === "undefined" || t-- > 0){
                setTimeout(interv, w);
                try{
                    func.call(null);
                }
                catch(e){
                    t = 0;
                    throw e.toString();
                }
            }
        };
    }(wait, times);

    setTimeout(interv, wait);
};


async function setFrameOnImage(){
  jQuery('#gvtryon_modal_inner').addClass('loading');
    return new Promise((resolve, reject) => {
      if(mode == 'video'){
          breakintervalfunc = false;
          interval(function(){
              cameraFrame = detectFaces();
          }, 100, 10000);
      }
      else{
          cameraFrame = detectFaces();
      }
    });
}

async function detectFaces(){
    let inputElement =  document.getElementById('video_canvas');
    inputElement.onloadeddata = (event) => {
        console.log('input loaded');
    };
    if(facemashModel === null || facemashModel === undefined){
      facemashModel = await facemesh.load();
    }
    return await facemashModel.estimateFaces(inputElement).then(function(predictions){ 
        onEstimateFacesResult(inputElement,predictions)
    });
}

function getDistance(xA, yA, xB, yB) { 
    var xDiff = xA - xB; 
    var yDiff = yA - yB; 
    return Math.sqrt(xDiff * xDiff + yDiff * yDiff);
}

function getAngle(xA, yA, xB, yB){
    return Math.atan2( yB-yA, xB-xA ) * 180 / Math.PI;
}

function getCoordinate (inputElement,canvasElement,x,y) {
    let ratio,resizeX, resizeY,leftAdjustment;
    ratio = canvasElement.clientHeight/inputElement.height;
    resizeX = x*ratio;
    resizeY = y*ratio;
   
    return [resizeX, resizeY];
}

function onEstimateFacesResult(inputElement,predictions){
    jQuery('.gvtryon_content_wrap').addClass('overlayhascontent');
    jQuery('#gvtryon_modal_inner').removeClass('loading');

    let canvasElement = inputElement;
    if (predictions.length > 0) {
        for (let x = 0; x < predictions.length; x++) {
            const keypoints = predictions[x].scaledMesh;  //468 key points of face;
            let faceLeftPoint = getCoordinate(inputElement,canvasElement,keypoints[faceEyesStartToEnd[0]][0],keypoints[faceEyesStartToEnd[0]][1]);
            let faceMiddlePoint = getCoordinate(inputElement,canvasElement,keypoints[faceEyesStartToEnd[1]][0],keypoints[faceEyesStartToEnd[1]][1]);
            let faceRightPoint = getCoordinate(inputElement,canvasElement,keypoints[faceEyesStartToEnd[2]][0],keypoints[faceEyesStartToEnd[2]][1]);
            let faceMiddleBottomPoint = getCoordinate(inputElement,canvasElement,keypoints[faceEyesStartToEnd[3]][0],keypoints[faceEyesStartToEnd[3]][1]);
            
            let fframeLeft = faceLeftPoint[0];
            let fframeLeftTop = faceLeftPoint[1];

            let fframeMiddleTop = faceMiddlePoint[1];
            let fframeMiddleLeft = faceMiddlePoint[0];

            let fframeRight = faceRightPoint[0];
            let fframeRightTop = faceRightPoint[1];

            let ffacewidth =  getDistance( fframeLeft, fframeLeftTop, fframeRight, fframeRightTop);
            let angle = getAngle(fframeLeft, fframeLeftTop, fframeRight, fframeRightTop);
            
            let appendtoOverlayid = '#video_overlay';
            let standardFrameSizeMM = jQuery(newCloneElement).attr('data-width');
            let standardFaceSizeMM = jQuery(newCloneElement).attr('data-gvtryon_standard_face_width');
            let diffY = jQuery(newCloneElement).attr('data-diffY');

            let FrameWidthInMM = convertPXtoMM(ffacewidth);

            let SFS = standardFrameSizeMM?standardFrameSizeMM:130,//80,
                SFaceS = standardFaceSizeMM?standardFaceSizeMM:130,//150,
                CFaceS = FrameWidthInMM,//100,
                CFS = (SFS * CFaceS) / SFaceS;

            let currentFrameSizePX = convertMMtoPX(CFS);
            let newdiff = (canvasElement.clientHeight * diffY)/300;
            jQuery(newCloneElement).appendTo(jQuery(appendtoOverlayid));
            jQuery(newCloneElement).css({
                top: (parseInt(fframeMiddleTop)  + (diffY?parseInt(newdiff):0)), 
                left: fframeMiddleLeft, 
                // right: fframeRight,
                width: currentFrameSizePX,
                // height: fframeElement.height,
                'transform-origin': 'center center',
                transform: 'translate(-50%, 0) rotate('+angle+'deg)',
                position:'absolute',
                zIndex: 55,
            });
            jQuery(appendtoOverlayid).show();
        }
    }
}

function clearOverlay(){
  let appendtoOverlayid = '#video_overlay';
  jQuery(appendtoOverlayid).empty();
  jQuery('.gvtryon_content_wrap').removeClass('overlayhascontent');
}
function convertMMtoPX(val) {
  return (val * 3.7795275591);
}
function convertPXtoMM(val) {
  return (val / 3.7795275591);
}