<?php 
	$page_obj = new stdClass;
	$page_obj->promoter = $promoter;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<div style="width:1050px;">
	<fieldset>
		<legend>Update profile information</legend> 
		
		<h2>General</h2>
		
		<p>Note: Maximum width: 1024px & Maximum height: 1600px</p>
		
		<img id="original_profile_pic" style="border: 1px solid #000;" src="<?=$central->s3_uploaded_images_base_url?>profile-pics/originals/<?=$promoter->up_profile_image?>.jpg" alt="profile picture" />
			
		<img id="ajax_picture_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="visibility:hidden;display:inline"/>
		<img id="ajax_picture_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="visibility:hidden;display:inline"/>
		
		<div id="profile_pic_crop_form" style="display:none;">
			<form target="#" id="my_profile_pic_form">
				<input type="hidden" name="width" value="<?=$promoter->up_original_width?>" id="width" />
				<input type="hidden" name="height" value="<?=$promoter->up_original_height?>" id="height" />
				<input type="hidden" name="x0" value="<?=$promoter->up_x0?>" id="x0" />
				<input type="hidden" name="y0" value="<?=$promoter->up_y0?>" id="y0" />
				<input type="hidden" name="x1" value="<?=$promoter->up_x1?>" id="x1" />
				<input type="hidden" name="y1" value="<?=$promoter->up_y1?>" id="y1" />
			</form>
		</div>
		
		<div class="clearboth"></div>
		
		<p>
			<table>
				<tr>
					<td><input class="button" type="submit" value="Choose File" id="ocupload_button" /></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td><input class="button" type="submit" value="Save Selection" id="crop_button" /></td>
					
					<td>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;" />
						<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none;" />
					</td>

				</tr>
			</table>
		</p> 
	</fieldset>
</div>