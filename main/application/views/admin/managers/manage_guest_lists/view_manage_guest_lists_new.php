<h1>Create New Guest List</h1>
<p>
	Create, update and delete your available guest lists for each venue you're authorized to represent.
</p>

<div class="" style="width:980px;">

	
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
				<?php foreach($team_venues as $venue): ?>
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
		
		
		
		
		
							<p style="margin-bottom:0;"> 
			<label>Guest List Image:</label> 
			
			<div style="display:inline-block; margin-left: 155px;">
				<div id="image_holder"></div>
				<input id="upload_new_image" class="button" type="submit" value="Upload Image" />
				<img id="ajax_loading_image" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;" />
			</div>
			
			
			
			<?php if(false): ?>
			<a class="ajaxify" href="<?= $central->manager_admin_link_base ?>manage_image/">
				<?php if(isset($manage_image->image_data)): ?>
					<img src="<?= $central->s3_uploaded_images_base_url . $manage_image->type . '/originals/temp/' . $manage_image->image_data->image . '_t.jpg'?>" alt="upload image" />
				<?php else: ?>
					<img src="http://www.placehold.it/66x93/CCC/000/&text=Upload" alt="upload image" />
				<?php endif; ?>	
			</a>
			<?php endif; ?>
		
		</p>
		
		
		
		
		<div style="clear:both;"></div>
	
							<p>
			<label>Guest List Description:</label><br/>
			<span style="color:gray;">Remember, when clients join your list their Facebook friends will see this description. A better description = more clients.</span>
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
				<option value="0">12:00 am</option>
				<option value="1">1:00 am</option>
				<option value="2">2:00 am</option>
				<option value="3">3:00 am</option>
				<option value="4">4:00 am</option>
				<option value="5">5:00 am</option>
				<option value="6">6:00 am</option>
				<option value="7">7:00 am</option>
				<option value="8">8:00 am</option>
				<option value="9">9:00 am</option>
				<option value="10">10:00 am</option>
				<option value="11">11:00 am</option>
				<option value="12">12:00 pm</option>
				<option value="13">1:00 pm</option>
				<option value="14">2:00 pm</option>
				<option value="15">3:00 pm</option>
				<option value="16">4:00 pm</option>
				<option value="17">5:00 pm</option>
				<option value="18">6:00 pm</option>
				<option value="19">7:00 pm</option>
				<option value="20" selected="selected">8:00 pm</option>
				<option value="21">9:00 pm</option>
				<option value="22">10:00 pm</option>
				<option value="23">11:00 pm</option>
			</select>
		</p>
			<p>
			<label>List Closes:</label>
			<select name="guest_list_close">
				<option value="0" selected="selected">12:00 am</option>
				<option value="1">1:00 am</option>
				<option value="2">2:00 am</option>
				<option value="3">3:00 am</option>
				<option value="4">4:00 am</option>
				<option value="5">5:00 am</option>
				<option value="6">6:00 am</option>
				<option value="7">7:00 am</option>
				<option value="8">8:00 am</option>
				<option value="9">9:00 am</option>
				<option value="10">10:00 am</option>
				<option value="11">11:00 am</option>
				<option value="12">12:00 pm</option>
				<option value="13">1:00 pm</option>
				<option value="14">2:00 pm</option>
				<option value="15">3:00 pm</option>
				<option value="16">4:00 pm</option>
				<option value="17">5:00 pm</option>
				<option value="18">6:00 pm</option>
				<option value="19">7:00 pm</option>
				<option value="20">8:00 pm</option>
				<option value="21">9:00 pm</option>
				<option value="22">10:00 pm</option>
				<option value="23">11:00 pm</option>
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
		<div style="display:none;" class="one_fourth_last"> 
			<p><input type="checkbox" class="iphone" name="guest_list_auto_promote" checked="checked"/>Auto promote guest list on Facebook</p>
		</div>
		
		
		
		<hr/>
		<h2 style="margin-bottom:5px;">Add to Promoters' guest lists</h2>
		<span style="color:gray;">Add this guest list to your promoter's guest lists. This option will override any guest lists set up by your promoters with the same name as this guest list.</span>
		<br/>
		<div>
			<?php foreach($promoters as $pro): ?>
				
				<?php 
					if($pro->up_completed_setup === '0' || $pro->up_banned === '1')
						continue;
				?>
			<br/>
			<p>
				<table>
					<tbody>
						
						<?php foreach($promoters as $pro): ?>
							
							<?php
								 if($pro->up_completed_setup === '0' || $pro->up_banned === '1')
								continue; 
							?>
							
						<tr>
							<td style="vertical-align:middle;">
								<input type="checkbox" class="iphone" name="promoters_link" value="<?= $pro->up_id ?>" />
							</td>
							<td>
								<img style="height:50px;" src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $pro->up_profile_image . '_t.jpg' ?>" alt="profile image" />
							</td>
							<td style="padding-left:10px; vertical-align:middle;">
								<?= $pro->u_full_name ?>
							</td>
						</tr>
						<?php endforeach; ?>
						
					</tbody>
				</table>
			<?php endforeach; ?>
		</div>
		<hr/>
		
		
		
		
		<div class="clearboth"></div> 
		
		<p style="color:red;" id="display_message"></p>
		
		<p>
			<table>
				<tr>
					<td><input id="submit_new_guest_list" class="button" type="submit" value="Submit" /></td>
					<td>
						<img id="ajax_loading" 			src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;"/>
						<img id="ajax_complete_success" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none;"/>
						<img id="ajax_complete_error" 	src="<?=$central->admin_assets?>images/icons/notifications/error.png" alt="error" style="display:none;"/>	
					</td>
				</tr>
			</table>
		</p> 
		
	</form>
	</fieldset>
	
	<div class="clearboth"></div> 
</div>

<div style="display:none;">
	<a class="ajaxify" id="back" href="<?= $central->front_link_base . 'admin/managers/settings_guest_lists/' ?>">Back</a>
</div>

<div style="height:50px;"></div>