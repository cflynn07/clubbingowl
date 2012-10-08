<script type="text/javascript">
jQuery(function(){
	
	jQuery('form#event_new_form').dumbFormState();
	
	jQuery('form#event_new_form input[name = event_date]').datepicker({
		minDate: +1,
		maxDate: '+6m'
	});
	
	jQuery('input#submit_new_guest_list').bind('click', function(){
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		var data = {
			venue: jQuery('form#event_new_form select[name = event_venue]').val(),
			event_date: jQuery('form#event_new_form input[name = event_date]').val(),
			event_name: jQuery('form#event_new_form input[name = event_name]').val(),
			event_description: jQuery('form#event_new_form textarea[name = event_description]').val(),
			auto_approve: ((jQuery('form#event_new_form input[name = event_auto_approve]').attr('checked') == undefined) ? false : true),
			guest_list_override: ((jQuery('form#event_new_form input[name = guest_list_override]').attr('checked') == undefined) ? false : true),
			ci_csfr_token: cct,
			vc_method: 'promoter_new_event'
		}
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: data,
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data); return;
				
				if(data.success){
					
					jQuery('form#event_new_form').dumbFormState('remove');
					window.location = '<?= $central->promoter_admin_link_base ?>manage_events/';
				
				}else{
					
					jQuery('p#display_message').html(data.message);
				
				}
				
			}
		});
	
		return false;
	});
	
});
</script>

<style type="text/css">
td.ui-datepicker-today{
	background-color: red;
}
td.ui-datepicker-unselectable{
	background-color: gray;
}
</style>
<?php Kint::dump($central); ?>
<?php Kint::dump($manage_image); ?>
<h1>Create New Event</h1>
<p>
	Events are special, one-time occurances that are non-recurring like regular weekly guest lists. (Ex: birthday party, new years eve party, etc)
</p>

<!-- modal window contents -->
<div id="events_new_modal_window" style="display:none">
	<?php //Plan is to place copy here to explain deleting guest lists restrictions ?>
</div>
<!-- end modal window contents -->

<fieldset>
<form id="event_new_form">
	<legend>Event Configuration</legend> 
	
							<p> 
		<label>My Authorized Venues:</label> 
		<select name="event_venue" class="dropdown">
			<?php foreach($promoter_team_venues as $venue): ?>
			<option value="<?=$venue->tv_id?>"><?=$venue->tv_name?></option>
			<?php endforeach; ?>
		</select> 
	</p>
	
							<p> 
		<label>Date:</label>
		<input type="text" name="event_date" class="sf"> 
	</p>
	
						<p> 
		<label>Event Name:</label> 
		<input class="mf" name="event_name" type="text" value="" /> 
	</p>
	
							<p> 
		<label>Event Image:</label>
		<a href="<?= $central->promoter_admin_link_base ?>manage_image/">
			<?php if(isset($manage_image->image_data)): ?>
				<img src="<?= $central->s3_uploaded_images_base_url . $manage_image->type . '/originals/temp/' . $manage_image->image_data->image . '_t.jpg'?>" alt="upload image" style="width:66px; height:88px;" />
			<?php else: ?>
				<img src="<?= $central->admin_assets ?>images/upload_image.png" alt="upload image" style="width:66px; height:88px;" />
			<?php endif; ?>
		</a>
	</p>
	
						<p> 
		<label>Event Description:</label>
		<textarea rows=5 style="resize: none; width: 100%;  background: none repeat scroll 0 0 #EEEEEE;" name="event_description"></textarea>
	</p>
	
	<p>Event Options</p>

	<div class="one_fourth_last"> 
		<p><input type="checkbox" class="iphone" name="event_auto_approve" />Auto approve reservation requests</p>
	</div> 
	
	<div class="clearboth"></div> 
	
	<div class="one_fourth_last"> 
		<p><input type="checkbox" class="iphone" name="guest_list_override" />Replace guest lists on event date</p>
	</div> 
	
	<div class="clearboth"></div> 
	
	<p id="display_message"></p>
	
	<p>
		<table>
			<tr>
				<td><input id="submit_new_guest_list" class="button" type="submit" value="Submit" /></td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				<td><img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="visibility:hidden;display:inline"/></td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				<td><img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="visibility:hidden;display:inline"/></td>
			</tr>
		</table>
	</p> 
	
</form>
</fieldset>

<div class="clearboth"></div> 