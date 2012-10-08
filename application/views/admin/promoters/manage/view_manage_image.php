<?php 
	$page_obj = new stdClass;
	$page_obj->manage_image = $manage_image;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<fieldset>
		<legend>Upload and Crop <?= ($manage_image->type == 'guest_lists') ? 'Guest List' : 'Event' ?> Image</legend> 
		
		<img id="image" src="<?= (isset($manage_image->image_data)) ? $central->s3_uploaded_images_base_url . $manage_image->type . ((!$manage_image->existing) ? '/originals/temp/' : '/originals/') . $manage_image->image_data->image . '.jpg' : '' ?>" alt="<?= ($manage_image->type == 'guest_lists') ? 'Guest List' : 'Event' ?> Image" style="display:none;" />
		
		<div class="clearboth"></div>
		<br><br>
		<p>
			<table>
				<tr>
					<td><input class="button" type="submit" value="Choose File" id="ocupload_button" /></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td><input class="button" type="submit" value="Save Selection" id="crop_button" <?= (!$manage_image->existing) ? 'style="display:none;"' : '' ?> /></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;"/>
						<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none;"/>
					</td>
				</tr>
			</table>
		</p> 
</fieldset>

<form target="#" id="pic_crop_form" style="display:none;">
	<input type="hidden" name="width" value="<?= (isset($manage_image->image_data)) ? ($manage_image->image_data->x1 - $manage_image->image_data->x0) : '' ?>" />
	<input type="hidden" name="height" value="<?= (isset($manage_image->image_data)) ? ($manage_image->image_data->y1 - $manage_image->image_data->y0) : '' ?>" />
	<input type="hidden" name="x0" value="<?= (isset($manage_image->image_data)) ? $manage_image->image_data->x0 : '' ?>" />
	<input type="hidden" name="y0" value="<?= (isset($manage_image->image_data)) ? $manage_image->image_data->y0 : '' ?>" />
	<input type="hidden" name="x1" value="<?= (isset($manage_image->image_data)) ? $manage_image->image_data->x1 : '' ?>" />
	<input type="hidden" name="y1" value="<?= (isset($manage_image->image_data)) ? $manage_image->image_data->y1 : '' ?>" />
</form>

<div style="display:none;">
	<a class="ajaxify" id="back" href="<?= $central->front_link_base . 'admin/promoters/' . $manage_image->return . '/' ?>">Back</a>
</div>