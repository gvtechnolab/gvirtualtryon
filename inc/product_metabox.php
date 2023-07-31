<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly


// Register meta box for food_locker post type
function gvtryon_set_frame_upload_metabox()
{
	add_meta_box('gvtryon_frame_upload_meta_box', __('GFit Virtual Frames', 'gfit-virtual-tryon'), 'gvtryon_frame_uplod_meta_box_callback', 'product', 'normal', 'low');
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
	<div style="display: block;padding: 10px;border-bottom: 1px solid #333;">
		<label for="gvtryon_frame_width">Frame Width: </label>
		<input id="gvtryon_frame_width" name="gvtryon_frame_width" type="number" min="80" max="250"
			value="<?php echo esc_html($gvtryon_frame_width); ?>" />
		<small>
			<?php echo esc_html(__('mm', 'gfit-virtual-tryon')); ?>
		</small>
	</div>
	<div style="display: block;padding: 10px;border-bottom: 1px solid #333;">
		<label for="gvtryon_standard_face_width">Standard Fase Width: </label>
		<input id="gvtryon_standard_face_width" name="gvtryon_standard_face_width" type="number"
			value="<?php echo esc_html($gvtryon_standard_face_width); ?>" />
		<small>
			<?php echo esc_html(__('mm', 'gfit-virtual-tryon')); ?>
		</small>

	</div>

	<div style="display: block;padding: 10px;border-bottom: 1px solid #333;">
		<div>
			<input id="gvtryon_frame_image" name="gvtryon_frame_image" type="text" value="<?php echo esc_url($url); ?>" />
			<input id="gvtryon_upl_button" type="button" value="Upload Image" /><br />
			<div style="padding: 10px 0;">
				<img src="<?php echo esc_url($url); ?>" style="width:200px;" id="gvtryon_picsrc_img" />
			</div>
		</div>
		<small>
			<?php echo esc_html(__('Upload frames with png format and transparent background', 'gfit-virtual-tryon')); ?>
		</small>
		<p id="gvtryon_reload_message" style="width: 100%; color:  #ff0000; margin: 10px 0;display: none;">
			<?php echo esc_html(__('Please Save/Update Product to view/Edit frame position', 'gfit-virtual-tryon')); ?>
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
	<div class="gvtryon_edit_virtual clear" data-isEdit="false">
		<div id="gvtryon_admin_canvas_container">
			<div id="gvtryon_admin_tryon_modal" width="400" height="300"></div>
			<canvas id="gvtryon_admin_tryon_canvas" width="400" height="300"></canvas>
		</div>
		<input type="submit" name="gvtryon_save_product" id="gvtryon_save_product" class="button button-primary button-large" value="Update">
		<div class="gvtryon_admin_canvas_variables" style="display: none">
			<div class="gvtryon_edit_buttons">
				<button type="button" id="gvtryon_edit_changes">Edit</button>
				<button type="button" id="gvtryon_done_changes">Done</button>
			</div>
			<div class="gvtryon_hidden_inputs">
				<label style="display: block;">
					<strong><?php echo esc_html(__("Width :", "gfit-virtual-tryon")); ?> </strong>
					<input id="frameWidthO" name="frameWidthO" placeholder="Frame width in px" />
				</label>
				<label style="display: block;">
					<strong><?php echo esc_html(__("Height :", "gfit-virtual-tryon")); ?></strong>
					<input id="frameHeightO" name="frameHeightO" placeholder="Frame height in px" />
				</label>
				<label style="display: block;">
					<strong><?php echo esc_html(__("PositionX :", "gfit-virtual-tryon")); ?></strong>
					<input id="frameMiddelPointX" name="frameMiddelPointX" placeholder="Frame left position"
						disabled="true" />
				</label>
				<label style="display: block;">
					<strong><?php echo esc_html(__("PositionY :", "gfit-virtual-tryon")); ?> </strong>
					<input id="frameMiddelPointY" name="frameMiddelPointY" placeholder="Frame top position" />
				</label>
				<label style="display: block;">
					<strong><?php echo esc_html(__("StandardEyeLine :", "gfit-virtual-tryon")); ?> </strong>
					<input id="StandardEyeLine" name="StandardEyeLine" placeholder="StandardEyeLine" disabled="true" />
				</label>
				<label style="display: block;">
					<strong><?php echo esc_html(__("DiffY :", "gfit-virtual-tryon")); ?> </strong>
					<input id="diffY" name="gvtryon_diffY" placeholder="diffY" value="<?php echo esc_html($diffY); ?>" />
				</label>
			</div>
		</div>
	</div>

	<?php
}

function gvtryon_save_product_meta_fields($post_id)
{
	if (isset($_POST['gvtryon_frame_image'])) {
		update_post_meta($post_id, 'gvtryon_frame_image', sanitize_text_field($_POST['gvtryon_frame_image']));
	}
	if (isset($_POST['gvtryon_frame_width'])) {
		update_post_meta($post_id, 'gvtryon_frame_width', sanitize_text_field($_POST['gvtryon_frame_width']));
	}
	if (isset($_POST['gvtryon_standard_face_width'])) {
		update_post_meta($post_id, 'gvtryon_standard_face_width', sanitize_text_field($_POST['gvtryon_standard_face_width']));
	}
	if (isset($_POST['gvtryon_diffY'])) {
		update_post_meta($post_id, 'gvtryon_diffY', sanitize_text_field($_POST['gvtryon_diffY']));
	}
}
add_action('save_post', 'gvtryon_save_product_meta_fields');
