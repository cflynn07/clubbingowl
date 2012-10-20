<?php
	$page_obj = new stdClass;
	$page_obj->tv = $tv;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<div id="admin_manager_settings_venues_edit_wrapper">
	<h1>Edit <?= $tv->name?>'s settings</h1>
	
	<!-- modal window contents -->
	<div id="venue_new_guest_list" style="display:none">
		
		<form>
			<fieldset>
				<label>List Name</label><br>
				<input name="list_name" type="text" />
				
				<br><br>
				
				<label>Weekday</label><br>
				<select name="list_weekday">
					<option value="0">Mondays</option>
					<option value="1">Tuesdays</option>
					<option value="2">Wednesdays</option>
					<option value="3">Thursdays</option>
					<option value="4">Fridays</option>
					<option value="5">Saturdays</option>
					<option value="6">Sundays</option>
				</select>
				
				<br><br>
				
				<label>Description</label>
				<textarea name="list_description" rows=3 style="border: 1px solid #000; resize: none; width: 100%; background: none repeat scroll 0 0 #EEEEEE;"></textarea>
				
				<label>Auto-Approve</label><br>
				<input type="checkbox" class="iphone" name="list_auto_approve" checked="checked"/>
			</fieldset>
		</form>
		
		<p class="form_message" style="color:red"></p>
		
	</div>
	
	<div id="confirm_dialog" style="display:none;">	
		<p></p>
	</div>
	
	<!-- end modal window contents -->
	
	<fieldset>
	<form id="venue_new_form">
		<legend>Venue Configuration</legend> 
	
		<br>
	
								<p> 
			<label>Venue Name:</label> 
			<span><?= $tv->name ?></span>
		</p>
	
								<p> 
			<label>Street Address:</label> 
			<input class="mf" name="venue_street_address" type="text" value="<?= $tv->street_address ?>" /> 
		</p>
		
								<p> 
			<label>City:</label> 
			<input class="mf" name="venue_city" type="text" value="<?= $tv->city ?>" /> 
		</p>
		
								<p> 
			<label>State:</label> 
			<select name="venue_state">
				<?php foreach($this->config->item('states') as $key => $state): ?>
					<option <?php if($key == $tv->state): ?>selected="selected"<?php endif; ?> value="<?= $key ?>"><?= $state ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		
								<p> 
			<label>Zip Code:</label> 
			<input class="sf" name="venue_zip" type="text" value="<?= $tv->zip ?>" /> 
		</p>
	
							<p> 
			<label>Venue Image:</label> 
			<a class="ajaxify" href="<?= $central->manager_admin_link_base ?>manage_image/">
				<?php if(isset($manage_image->image_data)): ?>
					<img src="<?= $central->s3_uploaded_images_base_url . $manage_image->type . '/originals/temp/' . $manage_image->image_data->image . '_t.jpg'?>" alt="upload image" />
				<?php else: ?>
					<img src="http://www.placehold.it/286x89/CCC/000/&text=Upload" alt="upload image" />
				<?php endif; ?>	
			</a>
		</p>
		
		<div style="clear:both;"></div>
	
							<p>
			<label>Venue Description:</label>
			<textarea rows=5 style="padding: 5px; border: 1px solid #000; resize: none; width: 100%; background: none repeat scroll 0 0 #EEEEEE;" name="venue_description"><?= $tv->description ?></textarea>
		</p>
		
		
		<div class="clearboth"></div> 
		
		<p id="display_message"></p>
		
		<p>
			<table>
				<tr>
					<td>
						<input id="submit_new_venue" class="button" type="submit" value="Submit" />
					</td>
					<td>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="visibility:hidden; display:inline"/>
						<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="visibility:hidden; display:inline"/>
					</td>
				</tr>
			</table>
		</p> 
		
	</form>
	</fieldset>

</div>

<div style="height: 40px;" class="clearboth"></div> 