<div style="width:1050px;">
	<form id="my_profile_form">
	<fieldset>
			<legend>Update profile information</legend> 
			
			<h2>General</h2>
			
			<hr />
			
			<p>
				<label>Public Identifier:</label>
				<span style="font-weight: bold;"><?= $promoter->up_public_identifier ?></span>
			</p>
					
			<p>
				<label>Profile Picture:</label>
				<div id="admin_profile_picture_div" style="margin:0px; padding:0px;">
					<a class="ajaxify" href="<?=$central->promoter_admin_link_base?>my_profile_img/">
						<img id="admin_profile_picture_img" src="<?=$promoter->profile_image_complete_url?>" alt="profile picture" />
					</a>
				</div>
			</p>
			
			<hr />
			
			<p>
				<label>SMS/Text Number</label>
				<input class="sf" id="sms_text_number" name="sms_text_number" type="text" value="<?= $promoter->u_twilio_sms_number ?>" />
				<img src="<?=$central->admin_assets?>images/icons/small_icons_2/Info.png" alt="info" class="tooltip" title="VibeCompass will notify you via SMS when you recieve a new guest-list reservation. You can reply to accept/decline the request.">
			</p>
			
			<hr />
			
			<h2 style="clear:both;">Biography</h2> 
			
			<textarea name="biography"><?= $promoter->up_biography ?></textarea> 
			
			<div class="clearboth"></div> 
			
			<p>
				<input class="button submit" type="submit" value="Save"/>
				<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none"/>
				<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none"/>
			</p> 
	</fieldset>
	</form>
</div>