<div class="two_third">
	
	<div id="visible_content"> <?//this content will be swapped via javascript for step 2 - photo uploading ?> 
		
		<h1>Promoter Setup</h1>
	
		<h2>Welcome to ClubbingOwl!</h2>
		<p>We need to ask you some simple questions to set up your promoter profile. This should only take a few minutes. Once you've completed
			these steps you will be able to explore your ClubbingOwl promoter admin panel.</p>
			
		<?= form_open('#', array('id' => 'promoter_setup_form')) ?>
			<fieldset> 
					<legend>Basic Information</legend> 
									
					<p>
						<label>Public Identifier:</label>
						<input class="sf" id="public_identifier" name="public_identifier" type="text" value="" />
						<img src="<?=$central->admin_assets?>images/icons/small_icons_2/Info.png" alt="info" class="tooltip" title="Your profile will be accessible at https://www.clubbingowl.com/promoters/YOUR_PUBLIC_IDENTIFIER/ ">
						
						<p style="display:none;height:50px;vertical-align:center;" id="public_identifier_error" class="error">
							Public Identifier cannot contain any special characters such as: %$*!
						</p>
						
					</p>
					
					<p>
						<label>SMS/Text Number</label>
						<input class="sf" id="sms_text_number" name="sms_text_number" type="text" value="" />
						<img src="<?=$central->admin_assets?>images/icons/small_icons_2/Info.png" alt="info" class="tooltip" title="ClubbingOwl will notify you via SMS when you recieve a new guest-list reservation. You can reply to accept/decline the request.">
					</p>
										
					<p>
						
						<label><strong>Tell Us About Yourself:</strong></label><br>
						
						<span class="">This will be featured on your promoter profile, and will be displayed on Facebook when your clients share a link to your ClubbingOwl profile. <br /><br />A good description will bring more clientelle from Google and other search engines to your profile. Write about why you're a good promoter and how you'll help your clients should party with you. You can always edit this later in your settings panel.</span><br/><br/>
						<span id="biography_char_remaining" style="float:left; color:red; font-size:16px;"></span>

						<textarea id="text_biography" style="width:680px; height:70px; border:1px solid #CCC; resize:none; padding: 5px;" name="biography"></textarea>
												
					</p>
					
					<p id="step_1_response_message" style="color: red;"></p> 
					
					<p>
						<input class="button" type="submit" value="Continue"/>
						<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="visibility:hidden;display:inline"/>
						<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="visibility:hidden;display:inline"/>
					</p> 
			</fieldset>
		</form>
				 
	</div>
	
</div>










<div id="step_2_content" style="display:none;">
	<fieldset>
			<legend>Upload Profile Picture</legend> 
						
			<img id="profile_pic" src="http://placehold.it/200x200?text=" alt="profile picture" />
				
						<?= ''//form_open('#', array('id' => 'promoter_pic_crop_form', 'style' => 'display:none;')) ?>
				<form id="my_profile_pic_form" action="#" style="display:none;">	
					<input type="hidden" name="width" value="0" id="width" />
					<input type="hidden" name="height" value="0" id="height" />
					<input type="hidden" name="x0" value="0" id="x0" />
					<input type="hidden" name="y0" value="0" id="y0" />
					<input type="hidden" name="x1" value="0" id="x1" />
					<input type="hidden" name="y1" value="0" id="y1" />
				</form>
			
			<div class="clearboth"></div>
			
			<p>
				<table>
					<tr>
						<td><input class="button" type="submit" value="Choose File" id="ocupload_button" /></td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td><input class="button" type="submit" value="Save Selection" id="crop_button" style="display:none" /></td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td><img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="visibility:hidden;display:inline"/></td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td><img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="visibility:hidden;display:inline"/></td>
					</tr>
				</table>
			</p> 
	</fieldset>
</div>





<div id="step_3_content" style="display:none;">
	<fieldset>
			<legend>Congratulations!</legend> 
			
			<h2>You have completed the setup.</h2>
			
			<p>
				Your profile page is now visable to others in ClubbingOwl, however <strong>you haven't set up any guest lists yet.</strong>
				
				
				
			</p>
			
			
			<p>
				<table>
					<tr>
						<td><input onclick="javascript:window.location = '/admin/promoters/manage_guest_lists/';return;" class="button" type="submit" value="Setup Guest Lists" id="" /></td>
					</tr>
				</table>
			</p> 
	</fieldset>
</div>

<div class="clearboth"></div>
<script type="text/javascript">
window.page_obj={};window.page_obj.first_time_setup=true;
jQuery(function(){window.vc_page_scripts.admin_promoter_setup_dashboard();});
</script>