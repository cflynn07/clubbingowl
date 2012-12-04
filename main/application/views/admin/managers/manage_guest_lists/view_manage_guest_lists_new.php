<div class="ui-widget" style="width:1050px;">
		
	<h1>Create New Guest List</h1>
	<p>
		Create, update and delete your available guest lists for each venue you're authorized to represent.
	</p>
	
	<!-- modal window contents -->
	<div id="guest_lists_new_modal_window" style="display:none">
		<?php //Plan is to place copy here to explain deleting guest lists restrictions ?>
	</div>
	<!-- end modal window contents -->
	
	<fieldset>
	<form id="guest_list_new_form">
		<legend>Guest List Configuration</legend> 
		
								<p> 
			<label>Venue:</label> 
			<select name="guest_list_venue" class="dropdown">
				<?php foreach($promoter_team_venues as $venue): ?>
				<option value="<?=$venue->tv_id?>"><?=$venue->tv_name?></option>
				<?php endforeach; ?>
			</select> 
		</p>
		
								<p> 
			<label>Weekday:</label> 
			<select name="guest_list_weekday" class="dropdown"> 
				<option value="0">Mondays</option>
				<option value="1">Tuesdays</option>
				<option value="2">Wednesdays</option>
				<option value="3">Thursdays</option>
				<option value="4">Fridays</option>
				<option value="5">Saturdays</option>
				<option value="6">Sundays</option>
			</select> 
		</p>
		
							<p> 
			<label>Guest List Name:</label> 
			<input class="mf" name="guest_list_name" type="text" value="" /> 
			<p id="guest_list_name_error" class="error" style="min-height:50px; display:none;">Guest List name can not contain special characters (such as: #*$&%).</p>
		</p>
		
							<p> 
			<label>Guest List Image:</label> 
			<a class="ajaxify" href="<?= $central->promoter_admin_link_base ?>manage_image/">
				<?php if(isset($manage_image->image_data)): ?>
					<img src="<?= $central->s3_uploaded_images_base_url . $manage_image->type . '/originals/temp/' . $manage_image->image_data->image . '_t.jpg'?>" alt="upload image" />
				<?php else: ?>
					<img src="http://www.placehold.it/66x93/CCC/000/&text=Upload" alt="upload image" />
				<?php endif; ?>	
			</a>
		</p>
		
		<div style="clear:both;"></div>
	
							<p>
			<label>Guest List Description:</label>
			<textarea rows=5 style="resize: none; width: 100%; background: none repeat scroll 0 0 #EEEEEE;" name="guest_list_description"></textarea>
		</p>
		
							<p>
			<label>Minimum Age:</label>
			<select name="guest_list_min_age">
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21" selected="selected">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
			</select>
		</p>
		
			<p>
			<label>Regular Cover:</label>
			<input style="width:50px;" name="guest_list_reg_cover"></input>
		</p>
			<p>
			<label>Guest-list Cover:</label>
			<input style="width:50px;" name="guest_list_gl_cover"></input>
		</p>
			<p>
			<label>Door Opens:</label>
			<select name="guest_list_open">
				<option value="0">00:00</option>
				<option value="1">01:00</option>
				<option value="2">02:00</option>
				<option value="3">03:00</option>
				<option value="4">04:00</option>
				<option value="5">05:00</option>
				<option value="6">06:00</option>
				<option value="7">07:00</option>
				<option value="8">08:00</option>
				<option value="9">09:00</option>
				<option value="10">10:00</option>
				<option value="11">11:00</option>
				<option value="12">12:00</option>
				<option value="13">13:00</option>
				<option value="14">14:00</option>
				<option value="15">15:00</option>
				<option value="16">16:00</option>
				<option value="17">17:00</option>
				<option value="18">18:00</option>
				<option value="19">19:00</option>
				<option value="20" selected="selected">20:00</option>
				<option value="21">21:00</option>
				<option value="22">22:00</option>
				<option value="23">23:00</option>
			</select>
		</p>
			<p>
			<label>List Closes:</label>
			<select name="guest_list_close">
				<option value="0" selected="selected">00:00</option>
				<option value="1">01:00</option>
				<option value="2">02:00</option>
				<option value="3">03:00</option>
				<option value="4">04:00</option>
				<option value="5">05:00</option>
				<option value="6">06:00</option>
				<option value="7">07:00</option>
				<option value="8">08:00</option>
				<option value="9">09:00</option>
				<option value="10">10:00</option>
				<option value="11">11:00</option>
				<option value="12">12:00</option>
				<option value="13">13:00</option>
				<option value="14">14:00</option>
				<option value="15">15:00</option>
				<option value="16">16:00</option>
				<option value="17">17:00</option>
				<option value="18">18:00</option>
				<option value="19">19:00</option>
				<option value="20">20:00</option>
				<option value="21">21:00</option>
				<option value="22">22:00</option>
				<option value="23">23:00</option>
			</select>
		</p>
			<p>
			<label>Additional Info 1:</label>
			<input style="width:250px;" name="guest_list_additional_info_1"></input>
		</p>
			<p>
			<label>Additional Info 2:</label>
			<input style="width:250px;" name="guest_list_additional_info_2"></input>
		</p>
			<p>
			<label>Additional Info 3:</label>
			<input style="width:250px;" name="guest_list_additional_info_3"></input>
		</p>
		
		
		<p>Options</p>
	
		<div class="one_fourth_last"> 
			<p><input type="checkbox" class="iphone" name="guest_list_auto_approve" />Auto approve reservation requests</p>
		</div> 
		<div class="one_fourth_last"> 
			<p><input type="checkbox" class="iphone" name="guest_list_auto_promote" checked="checked"/>Auto promote guest-list on Facebook</p>
		</div>
		
		<div class="clearboth"></div> 
		
		<p style="color:red;" id="display_message"></p>
		
		<p>
			<table>
				<tr>
					<td><input id="submit_new_guest_list" class="button" type="submit" value="Submit" /></td>
					<td>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;"/>
						<img id="ajax_complete_success" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none;"/>
						<img id="ajax_complete_error" src="<?=$central->admin_assets?>images/icons/notifications/error.png" alt="error" style="display:none;"/>	
					</td>
				</tr>
			</table>
		</p> 
		
	</form>
	</fieldset>
	
	<div class="clearboth"></div> 
</div>

<div style="display:none;">
	<a class="ajaxify" id="back" href="<?= $central->front_link_base . 'admin/promoters/manage_guest_lists/' ?>">Back</a>
</div>

<div style="height:50px;"></div>