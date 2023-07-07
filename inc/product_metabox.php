<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly


// Register meta box for food_locker post type
function gvtryon_set_frame_upload_metabox()
{
	add_meta_box('gvtryon_frame_upload_meta_box', __('GFit Virtual Frames', PLUGIN_TEXT_DOMAIN), 'gvtryon_frame_uplod_meta_box_callback', 'product', 'normal', 'low');
}
add_action('add_meta_boxes', 'gvtryon_set_frame_upload_metabox');





// Add Content for nutrition metabox 
function gvtryon_frame_uplod_meta_box_callback($post)
{
	$url = get_post_meta($post->ID, 'gvtryon_frame_image', true);
	$gvtryon_frame_width = get_post_meta($post->ID, 'gvtryon_frame_width', true);
	$gvtryon_frame_width = $gvtryon_frame_width ? $gvtryon_frame_width : 130;

	$gvtryon_standard_face_width = get_post_meta($post->ID, 'gvtryon_standard_face_width', true);
	$gvtryon_standard_face_width = $gvtryon_standard_face_width ? $gvtryon_standard_face_width : 130;

	$diffY = get_post_meta($post->ID, 'gvtryon_diffY', true);
	?>

	<!-- Step 1:- orignal Image upload section-->

	<!-- <div style="display: block;padding: 10px;border-bottom: 1px solid #333;" class="gvtryon_image_upload">
		<p class="gvtryon_image_upload_heading">Image Upload</p>
		<div class="gvtryon_image_upload_section">
			<input id="gvtryon_frame_image" name="gvtryon_frame_image" type="text" value="<?php echo $url; ?>" />
			<input id="gvtryon_upl_button" type="button" value="Upload Image" /><br />
			 <div style="padding: 10px 0;"> 
			<div>
				<img src="<?php echo $url; ?>" style="width:200px;" id="gvtryon_picsrc_img" />
			</div>
		</div>
		<small>
			// <?php echo __('Upload frames with png format and transparent background'); ?>
		</small>
		<p id="gvtryon_reload_message" style="width: 100%; color:  #ff0000; margin: 10px 0;display: none;">
			//<?php echo __('Please Save/Update Product to view/Edit frame position'); ?>
		</p> -->

	<!-- Step 1:- Updated image upload section with tab logic  -->

	
	<ul class="gvtryon_tab_container">
		<li data-tab-target ="#image_upload" class="tab active" >Image Upload</li>
		<li data-tab-target ="#frame_setting"  class="tab" >Frame Setting</li>
	</ul>


	<!--content area-->

	<div class="tab-content" >

		<div id="image_upload" data-tab-content class="active">

			<div style="display: block;padding: 10px; border-bottom: 1px solid #333;" class="gvtryon_image_upload">

				<p class="gvtryon_image_upload_heading">Image Upload</p>

				<div class="gvtryon_image_upload_section">

					<input id="gvtryon_frame_image" name="gvtryon_frame_image" type="text" value="<?php echo $url; ?>" />
					<input id="gvtryon_upl_button" type="button" value="Upload Image" /><br />
				</div>

					<!-- <div style="padding: 10px 0;"> -->
					<div class="gvtryon_frame_image_subsection" >
						<img src="<?php echo $url; ?>" style="width:200px;" id="gvtryon_picsrc_img" />
					</div>
			
					<small class="gvtryon_upload_msg">
						<?php echo __('Upload frames with png format and transparent background'); ?>
					</small>
					<p id="gvtryon_reload_message" style="width: 100%; color:  #ff0000; margin: 10px 0;display: none;">
						<?php echo __('Please Save/Update Product to view/Edit frame position'); ?>
					</p>


				<script>
					jQuery(document).ready(function ($) {
						jQuery('#gvtryon_upl_button').click(function () {
							window.send_to_editor = function (html) {
								imgurl = jQuery(html).attr('src')
								jQuery('#gvtryon_frame_image').val(imgurl);
								jQuery('#gvtryon_picsrc_img').attr("src", imgurl);
								tb_remove();
								jQuery('#gvtryon_reload_message').show();
							}

							formfield = jQuery('#gvtryon_frame_image').attr('name');
							tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
							return false;
						}); // End on click
					});
				</script>
			</div>
		</div>


		<div id="frame_setting" data-tab-content>



				<!-- Step 2: updated frame width and standard face width area  -->

			<div class="gvtryon_edit_virtual clear" data-isEdit="false">

				<p class="gvtryon_edit_virtual_heading">Frame Setting</p>

				<div class="gvtryon_edit_virtual_container">
					<div>
						<label for="gvtryon_frame_width">Frame Width: </label>
							<input id="gvtryon_frame_width" name="gvtryon_frame_width" type="number" min="80" max="250"
							value="<?php echo $gvtryon_frame_width; ?>" />
								<small>
							<?php echo __('mm'); ?>
								</small>
					</div>

					<div>
						<label for="gvtryon_standard_face_width">Standard Fase Width: </label>
							<input id="gvtryon_standard_face_width" name="gvtryon_standard_face_width" type="number"
								value="<?php echo $gvtryon_standard_face_width; ?>" />
								<small>
									<?php echo __('mm'); ?>
								</small>
					</div>
			</div>


			<div id="gvtryon_admin_canvas_container">
			<div id="gvtryon_admin_tryon_modal" width="400" height="300"></div>
				<canvas id="gvtryon_admin_tryon_canvas" width="400" height="300"></canvas>
			</div>

			<div class="gvtryon_admin_canvas_variables">
				<div class="gvtryon_edit_buttons">
					<button type="button" id="gvtryon_edit_changes">Edit</button>
					<button type="button" id="gvtryon_done_changes">Done</button>
				</div>
					<div class="gvtryon_hidden_inputs">
						<label style="display: block;">
							<strong>Width : </strong>
							<input id="frameWidthO" name="frameWidthO" placeholder="frame width in px" />
						</label>
						<label style="display: block;">
							<strong>Height :</strong>
							<input id="frameHeightO" name="frameHeightO" placeholder="frame height in px" />
						</label>
						<label style="display: block;">
							<strong>PositionX :</strong>
							<input id="frameMiddelPointX" name="frameMiddelPointX" placeholder="frame left position"
								disabled="true" />
						</label>
						<label style="display: block;">
							<strong>PositionY : </strong>
							<input id="frameMiddelPointY" name="frameMiddelPointY" placeholder="frame top position" />
						</label>
						<label style="display: block;">
							<strong>StandardEyeLine : </strong>
							<input id="StandardEyeLine" name="StandardEyeLine" placeholder="StandardEyeLine" disabled="true" />
						</label>
						<label style="display: block;">
							<strong>diffY : </strong>
							<input id="diffY" name="gvtryon_diffY" placeholder="diffY" value="<?php echo $diffY; ?>" />
						</label>
					</div>
				</div>
			</div>


		</div>

	</div>
	
	<!--my script -->

	<script>
		const tabs = document.querySelectorAll('[data-tab-target]');
		// console.log(tabs);
		tabs.forEach(tab => {
			tab.addEventListener('click',()=>{
				const target = document.querySelector(tab.dataset.tabTarget);

				// console.log(tab.dataset.tabTarget);

				// console.log(target);
				
				const tabContents = document.querySelectorAll('[data-tab-content]');

				tabContents.forEach(tabContent =>{
					tabContent.classList.remove('active');
				});

				//remove active tab using this code

				tabs.forEach(tab =>{
					tab.classList.remove('active');
				});

				tab.classList.add('active');
				target.classList.add('active');
			})
		});

	</script>

<?php
}

function gvtryon_save_product_meta_fields($post_id)
{
	if (isset($_POST['gvtryon_frame_image'])) {
		update_post_meta($post_id, 'gvtryon_frame_image', $_POST['gvtryon_frame_image']);
	}
	if (isset($_POST['gvtryon_frame_width'])) {
		update_post_meta($post_id, 'gvtryon_frame_width', $_POST['gvtryon_frame_width']);
	}
	if (isset($_POST['gvtryon_standard_face_width'])) {
		update_post_meta($post_id, 'gvtryon_standard_face_width', $_POST['gvtryon_standard_face_width']);
	}
	if (isset($_POST['gvtryon_diffY'])) {
		update_post_meta($post_id, 'gvtryon_diffY', $_POST['gvtryon_diffY']);
	}
}
add_action('save_post', 'gvtryon_save_product_meta_fields');


?>