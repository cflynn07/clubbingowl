<?php
	$page_obj = new stdClass;
	$page_obj->guest_list = $guest_list;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<?php Kint::dump($guest_list); ?>

<h1>Edit Guest List</h1>

<div class="" style="width:980px; margin-bottom:40px;">

	
	<!-- modal window contents -->
	<div id="guest_lists_new_modal_window" style="display:none">
		<?php //Plan is to place copy here to explain deleting guest lists restrictions ?>
	</div>
	<!-- end modal window contents -->
	
	
	
	<fieldset>
	<form id="guest_list_new_form">
		<legend>Guest List Configuration</legend> 
		
								<p> 
			<label>Venue:</label> <span><?= $guest_list->tv_name ?></span>
		</p>
		
		
		
		<?php if($guest_list->tgla_event == '0'): ?>
		
			<p> 
				<label>Weekday:</label> <span><?= rtrim(ucfirst($guest_list->tgla_day), 's') ?></span>
			</p>
		
		<?php else: ?>
		
			<p> 
				<label>Date:</label> <span><?= $guest_list->tgla_event_date ?></span>
			</p>
		
		<?php endif; ?>
		
		
		
							<p> 
			<label>Guest List Name:</label> <span><?= $guest_list->tgla_name ?></span>
			
			<br/>
			<img style="vertical-align:middle;" src="<?=$central->admin_assets?>images/icons/small_icons_2/Info.png" alt="info" class="tooltip" title="Your guest list name is a part of it's URL (ex: www.clubbingowl.com/venues/boston/club_argo/guest_lists/argo_fridays/ ). If you changed it, all of the links pointing to your guest list on Facebook and your client's bookmarks would break.">
			<span style="margin-left:10px; color:gray;">Why can't I change my guest list name?</span>
			
		</p>
		
							<p> 
								
			<label>Guest List Image:</label> 
								
			<div style="display:inline-block; margin-left: 155px;">
				
				<div id="image_holder">
				</div><br/>
				
				<input id="upload_new_image" class="button" type="submit" value="Upload Image" />
				<img id="ajax_loading_image" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;" />
			</div>
			
		</p>
		
		<div style="clear:both;"></div>
	
							<p>
			<label>Guest List Description:</label><br/>
			<span style="color:gray;">Remember, when clients join your list their Facebook friends will see this description. A better description = more clients.</span>
			<textarea rows=5 style="resize: none; width: 100%; background: none repeat scroll 0 0 #EEEEEE;" name="guest_list_description"><?= $guest_list->tgla_description ?></textarea>
		</p>
		
		
		
		
		
							<p>
			<label>Minimum Age:</label>
			<select name="guest_list_min_age">
				<?php for($i = 14; $i < 24; $i++): ?>
					<option <?= (($i == $guest_list->tgla_min_age) ? 'selected="selected"' : '') ?> value="<?= $i ?>"><?= $i ?></option>
				<?php endfor; ?>
			</select>
		</p>
		
			<p>
			<label>Regular Cover:</label>
			<input style="width:50px;" name="guest_list_reg_cover" value="<?= $guest_list->tgla_regular_cover ?>"></input>
		</p>
			<p>
			<label>Guest-list Cover:</label>
			<input style="width:50px;" name="guest_list_gl_cover" value="<?= $guest_list->tgla_gl_cover ?>"></input>
		</p>
			<p>
			<label>Door Opens:</label>
			<select name="guest_list_open">
				<option <?= (($guest_list->tgla_door_open == '0') ? 'selected="selected"' : '') ?> value="0">12:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '1') ? 'selected="selected"' : '') ?> value="1">01:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '2') ? 'selected="selected"' : '') ?> value="2">02:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '3') ? 'selected="selected"' : '') ?> value="3">03:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '4') ? 'selected="selected"' : '') ?> value="4">04:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '5') ? 'selected="selected"' : '') ?> value="5">05:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '6') ? 'selected="selected"' : '') ?> value="6">06:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '7') ? 'selected="selected"' : '') ?> value="7">07:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '8') ? 'selected="selected"' : '') ?> value="8">08:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '9') ? 'selected="selected"' : '') ?> value="9">09:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '10') ? 'selected="selected"' : '') ?> value="10">10:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '11') ? 'selected="selected"' : '') ?> value="11">11:00 am</option>
				<option <?= (($guest_list->tgla_door_open == '12') ? 'selected="selected"' : '') ?> value="12">12:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '13') ? 'selected="selected"' : '') ?> value="13">1:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '14') ? 'selected="selected"' : '') ?> value="14">2:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '15') ? 'selected="selected"' : '') ?> value="15">3:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '16') ? 'selected="selected"' : '') ?> value="16">4:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '17') ? 'selected="selected"' : '') ?> value="17">5:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '18') ? 'selected="selected"' : '') ?> value="18">6:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '19') ? 'selected="selected"' : '') ?> value="19">7:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '20') ? 'selected="selected"' : '') ?> value="20">8:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '21') ? 'selected="selected"' : '') ?> value="21">9:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '22') ? 'selected="selected"' : '') ?> value="22">10:00 pm</option>
				<option <?= (($guest_list->tgla_door_open == '23') ? 'selected="selected"' : '') ?> value="23">11:00 pm</option>
				
			</select>
		</p>
			<p>
			<label>List Closes:</label>
			<select name="guest_list_close">
				<option <?= (($guest_list->tgla_door_close == '0') ? 'selected="selected"' : '') ?> value="0">12:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '1') ? 'selected="selected"' : '') ?> value="1">01:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '2') ? 'selected="selected"' : '') ?> value="2">02:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '3') ? 'selected="selected"' : '') ?> value="3">03:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '4') ? 'selected="selected"' : '') ?> value="4">04:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '5') ? 'selected="selected"' : '') ?> value="5">05:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '6') ? 'selected="selected"' : '') ?> value="6">06:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '7') ? 'selected="selected"' : '') ?> value="7">07:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '8') ? 'selected="selected"' : '') ?> value="8">08:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '9') ? 'selected="selected"' : '') ?> value="9">09:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '10') ? 'selected="selected"' : '') ?> value="10">10:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '11') ? 'selected="selected"' : '') ?> value="11">11:00 am</option>
				<option <?= (($guest_list->tgla_door_close == '12') ? 'selected="selected"' : '') ?> value="12">12:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '13') ? 'selected="selected"' : '') ?> value="13">1:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '14') ? 'selected="selected"' : '') ?> value="14">2:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '15') ? 'selected="selected"' : '') ?> value="15">3:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '16') ? 'selected="selected"' : '') ?> value="16">4:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '17') ? 'selected="selected"' : '') ?> value="17">5:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '18') ? 'selected="selected"' : '') ?> value="18">6:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '19') ? 'selected="selected"' : '') ?> value="19">7:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '20') ? 'selected="selected"' : '') ?> value="20">8:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '21') ? 'selected="selected"' : '') ?> value="21">9:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '22') ? 'selected="selected"' : '') ?> value="22">10:00 pm</option>
				<option <?= (($guest_list->tgla_door_close == '23') ? 'selected="selected"' : '') ?> value="23">11:00 pm</option>
			</select>
		</p>
			<p>
			<label>Additional Info 1:</label>
			<input style="width:250px;" name="guest_list_additional_info_1" value="<?= $guest_list->tgla_additional_info_1 ?>"></input>
		</p>
			<p>
			<label>Additional Info 2:</label>
			<input style="width:250px;" name="guest_list_additional_info_2" value="<?= $guest_list->tgla_additional_info_2 ?>"></input>
		</p>
			<p>
			<label>Additional Info 3:</label>
			<input style="width:250px;" name="guest_list_additional_info_3" value="<?= $guest_list->tgla_additional_info_3 ?>"></input>
		</p>
		
		
		
		
		
		<p>Options</p>
	
		<div class="one_fourth_last"> 
			<p><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= (($guest_list->tgla_auto_approve == '1') ? 'checked="checked"' : '') ?> />Auto approve reservation requests</p>
		</div>




		<hr/>
		<h2 style="margin-bottom:5px;">Add to Promoters' guest lists</h2>
		<span style="color:gray;">Add this guest list to your promoter's guest lists. This option will override any guest lists set up by your promoters with the same name as this guest list.</span>
		<br/>
		<div>
			
			
			
			
			<br/>

			<table>
				<tbody>
					
					<?php foreach($promoters as $pro): ?>
						
						<?php if($pro->up_completed_setup === '0' || $pro->up_banned === '1')
							continue; ?>
						
					<tr>
						<td style="vertical-align:middle;">
															
							<input type="checkbox" class="iphone" name="promoters_link" <?= (in_array($pro->up_id, $linked_promoters)) ? 'checked="checked"' : '' ?> value="<?= $pro->up_id ?>" />
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
		
		
			
			
		</div>
		<hr/>






		
		<div class="clearboth"></div> 
		
		<p style="color:red;" id="display_message"></p>
		
		<p>
			<table>
				<tr>
					<td><input id="submit_new_guest_list" class="button" type="submit" value="Submit" /></td>
					<td>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none;"/>
						<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none;"/>
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

<br/>
<br/>
<br/>