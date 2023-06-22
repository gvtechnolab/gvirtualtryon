<?php
add_action('woocommerce_after_add_to_cart_button', 'gvtryon_popup_callback_primary');
function gvtryon_popup_callback_primary()
{
	global $post;
	$url = get_post_meta($post->ID, 'gvtryon_frame_image', true);
	$gvtryon_frame_width = get_post_meta($post->ID, 'gvtryon_frame_width', true);
	$gvtryon_standard_face_width = get_post_meta($post->ID, 'gvtryon_standard_face_width', true);
	$gvtryon_diffY = get_post_meta($post->ID, 'gvtryon_diffY', true);
	if ($url) {
		?>
		<button id="gvtryon_button" type="button" name="gv_tryon" class="button">Try On</button>
		<div id="gvtryon_modal" class="gvtryon-modal-wrap">
			<div id="gvtryon_modal_inner">
				<div id="container" class="gvtryon_content_wrap">
					<div class="gvtryon_header">
						<button type="button" id="gvtryon_close_button">&times;</button>
						<h3>
							<?php echo __('G Virtual TryOn', 'gvtryon'); ?>
						</h3>
						<span></span>
					</div>
					<div class="framelist">
						<ul>
							<li class="active">
								<div class="frame_info">
									<h5>
										<?php echo get_the_title($post->ID); ?>
									</h5>
									<img id="frameimage"
										data-gvtryon_standard_face_width="<?php echo $gvtryon_standard_face_width; ?>"
										data-width="<?php echo $gvtryon_frame_width; ?>" src="<?php echo $url; ?>"
										data-diffY="<?php echo $gvtryon_diffY; ?>" alt="" />
									<?php /* <strong>Frame Width: <em><?php echo $gvtryon_frame_width; ?>mm</em></strong> */?>
								</div>
							</li>
						</ul>
					</div>
					<div id="gvtryon_vid_container">
						<video id="video" autoplay playsinline></video>
						<div id="video_overlay"></div>
						<canvas id="video_canvas"></canvas>
						<canvas id="video_cap_canvas"></canvas>
					</div>
					<div id="gvtryon_gui_controls">
						<ul>
							<li><button id="gvtryon_start_camera_button" class="tooltip" type="button"><span
										class="tooltiptext">Start Camera</span></button></li>
							<li><button id="gvtryon_stop_camera_button" class="tooltip" type="button"><span
										class="tooltiptext">Stop Camera</span></button></li>
							<!-- <li class="mode">
									<span class="switch-wrap  showIfCameraStart">
										<span class="hide">Mode:</span> 
										<span class="switch-title left">Image</span> 
										<input type="checkbox" id="gvt_mode_check" checked /><label for="gvt_mode_check">Mode</label> <span class="switch-title right">Video</span>
									</span>
								</li> -->

							<li><button id="gvtryon_switch_camera_button" class="tooltip" name="switch Camera" type="button"
									aria-pressed="false"><span class="tooltiptext">Switch</span></button></li>
							<li><button id="gvtryon_take_photo_button" class="showIfCameraStart tooltip" name="take Photo"
									type="button"><span class="tooltiptext">Take Photo</span></button></li>
							<li><button id="gvtryon_set_frame_photo_button" class="showIfCameraStart tooltip"
									name="Set Frame Photo" type="button"><span class="tooltiptext">Set Frame</span></button>
							</li>
							<li class="showifovelayhaseframe"><button id="gvtryon_clear_frame_button" name="Clear Frame"
									class="tooltip" type="button"><span class="tooltiptext">Clear Frame</span></button></li>
							<li><button id="gvtryon_toggle_full_screen_button" name="toggle FullScreen"
									class="showIfCameraStart tooltip" type="button" aria-pressed="false"><span
										class="tooltiptext">FullScreen</span></button></li>
							<li><button id="gvtryon_download_snap" name="gvtryon_download_snap"
									class="showIfCameraStart tooltip" type="button"><span class="tooltiptext">Download
										Image</span></button></li>
						</ul>
						<!-- <div class="static_errorPopup tooltip">Info
							<span class="tooltiptext">
								<?php // echo __('G Virtual try-on works only on Chrome, Opera and Safari browsers', 'gvtryon'); ?>
							</span>
						</div> -->
					</div>

				</div>
			</div>
		</div>
		<?php
	}
}