<script type="text/javascript">
jQuery(function(){
		
	window.dialog_actions = false;
	var display_approve_deny_dialog = function(ui_element){
		
		//is this a promoter or team request
		
		var request_type = jQuery(ui_element).parents('tr').find('td.request_type').html();
		var table_request = jQuery(ui_element).parents('tr').find('td.table').html();
		var tv_id = jQuery(ui_element).parents('tr').find('td.tv_id').html();
		var venue_name = jQuery(ui_element).parents('tr').find('td.venue_name').html();
		var date = jQuery(ui_element).parents('tr').find('td.date').html();
		
		jQuery('div#dialog_actions_table img.loading_indicator').css('display', 'none');
		jQuery('div#dialog_actions img.loading_indicator').css('display', 'none');
		jQuery('div#dialog_actions_table').find('textarea').val('').trigger('keyup');
		jQuery('div#dialog_actions').find('textarea').val('').trigger('keyup');

		if(table_request == '1'){
			
			jQuery('div#dialog_actions_table tbody').find('td').each(function(){
				jQuery(this).empty();
			});
			
			jQuery('div#dialog_actions_table tbody td.assigned_table').html('None');
			
			if(request_type == 'promoter')
				jQuery('div#dialog_actions_table form').css('display', 'none'); //manager's dont submit messages to promoter requests
			else
				jQuery('div#dialog_actions_table form').css('dipslay', 'block');
				
			var parent_tr = jQuery(ui_element).parents('tr');
			
			var user_name = parent_tr.find('td.user_name').html();
			var user_pic = parent_tr.find('td.user_pic').html();
			var venue = parent_tr.find('td.venue').html();
			var promoter = parent_tr.find('td.promoter').html();
			var request_msg = parent_tr.find('td.request_msg').html();
			var min_spend = parent_tr.find('td.min_spend').html();
			var entourage = parent_tr.find('td.entourage').html();
			
			var dat_body = jQuery('div#dialog_actions_table tbody');
			dat_body.find('td.user_name').html(user_name);
			dat_body.find('td.user_pic').html(user_pic);
			dat_body.find('td.venue').html(venue);
			dat_body.find('td.promoter').html(promoter);
			dat_body.find('td.request_msg').html(request_msg);
			dat_body.find('td.min_spend').html(min_spend);
			dat_body.find('td.entourage').html(entourage);
			
		}
				
		var fql_user;
		//fix for ipad
		var tglr_id=0;
		var pglr_id=0;

		if(request_type == 'manager'){	
			
			var tglr_id = jQuery(ui_element).parents('tr').find('td.tglr_id').html();
			var tglr_head_user = jQuery(ui_element).parents('tr').find('td.tglr_head_user').html();
			
			//find head user in vc_fql_users
			for(var i = 0; i < vc_fql_users.length; i++){
				
				if(vc_fql_users[i].uid == tglr_head_user){
					fql_user = vc_fql_users[i];
					break;
				}
				
			}
			
		}else if(request_type == 'promoter'){
			
			var pglr_id = jQuery(ui_element).parents('tr').find('td.pglr_id').html();
			var pglr_head_user = jQuery(ui_element).parents('tr').find('td.pglr_head_user').html();
							
			//find head user in vc_fql_users
			for(var i = 0; i < vc_fql_users.length; i++){
				
				if(vc_fql_users[i].uid == pglr_head_user){
					fql_user = vc_fql_users[i];
					break;
				}
				
			}
			
		}
		
		if(table_request == '1'){
			jQuery('div#dialog_actions_table img.pic_square').attr('src', fql_user.pic_square);
			jQuery('div#dialog_actions_table span.name').html(fql_user.name);
			jQuery('div#dialog_actions_table p.assign_title span.venue_name').html(venue_name);
			jQuery('div#dialog_actions_table p.assign_title span.date').html(date);
			jQuery('div#dialog_actions_table p.error').html('').css('display', 'none');
		}else{
			jQuery('div#dialog_actions img.pic_square').attr('src', fql_user.pic_square);
			jQuery('div#dialog_actions span.name').html(fql_user.name);
			jQuery('div#dialog_actions p.assign_title span.venue_name').html(venue_name);
			jQuery('div#dialog_actions p.assign_title span.date').html(date);
		}
		
		// ------------------------------------------------ approve deny request dialog -------------------------------------------------
		var approve_deny_request = function(approved){
			
			var dialog = jQuery('div#dialog_actions' + ((table_request == '1') ? '_table' : ''));
	
			//no response for promoter requests
			if(request_type == 'manager')
				var message = jQuery.trim(dialog.find('form textarea[name = message]').val());
			else
				var message = '';
			
			//find assigned table
			
			var selected_table = dialog.find('div.vlf div.selected');
			var vlfit_id = false;
			if(selected_table.length == 0 && approved && table_request == '1'){ //if the user hit approved, they need to select a table
				dialog.find('p.error').html('You must select a table<div style="clear:both"></div>').css('display', 'block');
				return;
			}else{
				if(selected_table.length > 0){
					selected_table = selected_table[0];
					vlfit_id = jQuery(selected_table).find('div.vlfit_id').html();
				}
				dialog.find('p.error').html('').css('display', 'none');
			}
			
			console.log(vlfit_id);
			
			dialog.find('img.loading_indicator').css('display', 'block');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					ci_csrf_token: cct,
					vc_method: 'team_guest_list_request_accept_deny',
					accept_deny: approved,
					request_type: request_type,
					glr_id: ((request_type == 'promoter') ? pglr_id : tglr_id),
					table_request: table_request,
					vlfit_id: vlfit_id,
					message: message
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
									
					if(data.success){
						
						dialog.find('img.loading_indicator').css('display', 'none');
						var parent_tr = jQuery(ui_element).parents('tr');
						
						jQuery(ui_element).replaceWith((approved) ? '<span style="color: green;">Approved</span>' : '<span style="color: red;">Declined</span>');
						
					<?php if($this->uri->rsegment(3) == 'guest_lists'): ?>
						//guest_lists rseg
						
						if(message.length > 0){
							parent_tr.find('td.response_message').html(message);
						}
						
					<?php elseif(!$this->uri->rsegment(3)): ?>
						//dashboard rseg
						
						jQuery(window).scrollTop(0);			
									
						var color = '';
						if(approved){
							color = 'green';
						}else{
							color = 'red';
						}
						parent_tr.find('td').css('background', color);
						
						parent_tr.fadeOut(1400, function(){
							jQuery(this).remove();
						})
						
					<?php endif; ?>
					
						dialog.dialog('close');
						
					}else{
						dialog.find('p.error').html('An unknown error has occured. Please contact support for assistance.');
					}
					
				},
				failure: function(){
					console.log('failure');
				}
			});
			
		};		
		// ------------------------------------------------ end approve deny request dialog -------------------------------------------------

		var buttons = [{
			text: 'Approve',
			id: 'ui-approve-button',
			click: function(){
				approve_deny_request(true);
			}
		},{
			text: 'Decline',
			click: function(){
				approve_deny_request(false);
			}
		}];
		
		window.dialog_actions = jQuery('div#dialog_actions' + ((table_request == '1') ? '_table' : '')).dialog({
			title: 'Approve or Decline Request',
			height: 'auto', //((table_request == '1') ? 'auto' : 'auto'),
			width: ((table_request == '1') ? 1000 : 320),
			modal: true,
			buttons: ((table_request == '1') ? [] : buttons),
			resizable: false,
			draggable: false
		});
		
		if(table_request == '1'){
			
			jQuery('div#dialog_actions_table').find('img.loading_indicator').css('display', 'block');
			jQuery('div#dialog_actions_table').find('div.floors').empty().css('display', 'none');
			
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
						ci_csrf_token: 	cct,
						vc_method: 		'venue_floorplan_retrieve',
						request_type: 	request_type,
						glr_id: 		((request_type == 'promoter') ? pglr_id : tglr_id),
						tv_id: 			tv_id
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					<?php $factor = 0.59; ?>
					
					//construct floorplan
				//	for(var i = 0; i < data.venue_floors.length; i++){
					for (var i in data.venue_floors){
						
						var table_counter = 0;
						var vlf = '<div class="vlf">';
						for(var j = 0; j < data.venue_floors[i].items.length; j++){
							vlf += '<div class="item ' + data.venue_floors[i].items[j].vlfi_item_type + '" style="top:' + Math.ceil(data.venue_floors[i].items[j].vlfi_pos_y * <?= $factor ?>) + 'px; left:' + Math.ceil(data.venue_floors[i].items[j].vlfi_pos_x * <?= $factor ?>) + 'px; width:' + Math.ceil(data.venue_floors[i].items[j].vlfi_width * <?= $factor ?>) + 'px; height:' + Math.ceil(data.venue_floors[i].items[j].vlfi_height * <?= $factor ?>) + 'px;">';

							vlf += '<span class="title">';

							switch(data.venue_floors[i].items[j].vlfi_item_type){
								case 'table':
									vlf += 'T-' + table_counter;
									table_counter++;
									break;
								case 'bar':
									vlf += '(B)';
									break;
								case 'stage':
									vlf += '(S)';
									break;
								case 'dancefloor':
									vlf += '(D)';
									break;
								case 'djbooth':
									vlf += '(DJ)';
									break;
								case 'bathroom':
									vlf += '(Br)';
									break;
								case 'stairs':
									vlf += '(St)';
									break;
							}
						
							vlf += '</span>';	
							
							if(data.venue_floors[i].items[j].vlfi_item_type == 'table'){
						//		vlf += '<div class="max_capacity">' + data.venue_floors[i].items[j].vlfit_capacity + '</div>';
								vlf += '<div class="min_price">$11,500</div>';
								vlf += '<div class="vlfit_id">' + data.venue_floors[i].items[j].vlfit_id + '</div>';
								vlf += '<div style="display:none;" class="vlfit_id_' + data.venue_floors[i].items[j].vlfit_id + '">' + data.venue_floors[i].items[j].vlfit_id + '</div>';
							}
											
							vlf += '</div>';
						}
						vlf += '</div>';
												
						jQuery('div#dialog_actions_table div.floors').append(vlf);
						setTimeout(function(){
							jQuery('div#dialog_actions_table').dialog('option', 'position', 'center');
						}, 50);
						
					}
					
					//indicate reserved tables
					//TODO
					for(var i = 0; i < data.table_reservations.length; i++){
						
						
						var table = jQuery('div#dialog_actions_table div.vlf div.table').find('div.vlfit_id_' + data.table_reservations[i].vlfit_id).parents('div.table');
						console.log(table);
						table.addClass('reserved');
						
					}
					
					jQuery('div#dialog_actions_table div.floors div.vlf div.table').each(function(){
						
						if(jQuery(this).hasClass('reserved')){
							
							jQuery(this).css({
								cursor: 'default',
								background: 'darkRed'
							});
							
						}else{
							
							jQuery(this).css('cursor', 'pointer');
							
							jQuery(this).hover(function(el){
								jQuery(el).addClass('drop_hover');
							}, function(el){
								jQuery(el).removeClass('drop_hover');
							});
							
							jQuery(this).bind('click', function(){
								jQuery('div#dialog_actions_table div.table').each(function(){
									jQuery(this).removeClass('drop_hover');
									jQuery(this).removeClass('selected');
								});
								
								jQuery(this).addClass('drop_hover');
								jQuery(this).addClass('selected');
								
								var table_clone = jQuery(this).clone();
								jQuery('div#dialog_actions_table div.client table tbody td.assigned_table').empty().append(table_clone);
								
							});
							
						}
						
					});
					
					window.dialog_actions.dialog('option', 'buttons', buttons);
				
					jQuery('div#dialog_actions_table').find('img.loading_indicator').css('display', 'none');
					jQuery('div#dialog_actions_table').find('div.floors').css('display', 'block');
					
				},
				failure: function(){
					console.log('failure');
				}
			});
			
		}	
	};
	
	
	jQuery('span.app_dec_action').live('click', function(){
		display_approve_deny_dialog(this);
		return false;
	});
});
</script>

<style type="text/css">
button#ui-approve-button{
	background: green;
	border: 1px solid green;
}

div#dialog_actions_table div.floors{
	overflow-y: auto;
	text-align: center;
}

div#dialog_actions_table{
	background: #F0F0F0;
}

div#dialog_actions_table div.floors div.vlf{
	background: rgb(38, 38, 38);
	border: 1px solid #000;
	position: relative;
	width: <?= ceil(800 * $factor) ?>px;
	height: <?= ceil(600 * $factor) ?>px;
	margin: 5px;
	display: inline-block;
}

div#dialog_actions_table div.floors div.item{
	position: absolute;
	font-size: 9px;
	background: transparent;
	z-index: 1; 
	color: #FFF;
	vertical-align: middle;
	text-align:left;
	padding: 1px;
	border: 1px solid #000;
}

div#dialog_actions_table div.floors div.item div.vlfit_id{
	display: none;
}

div#dialog_actions_table div.floors div.item > div.day_price{
	display:none !important;
}

div#dialog_actions_table div.floors div.item > span.title{
	top: -5px;
	position: relative;
}

div#dialog_actions_table div.floors div.table{
	border: 1px solid red !important;
	cursor: pointer;
}

div#dialog_actions_table div.floors div.drop_hover{
	background:red !important;
}

div#dialog_actions_table div.floors div.table > div.max_capacity{
	font-size: 9px !important;
	position: relative;
	top: -14px;
}
div#dialog_actions_table div.floors div.table > div.min_price{
	position: relative; 
	top: -15px;
}


div#dialog_actions_table div.floors div.dancefloor{
	background: #000 !important;
	z-index: 0 !important;
}

div#dialog_actions_table div.floors div.stage{
	background: #000 !important;
	z-index: 0 !important;
}

div#dialog_actions_table div.floors div.djbooth{
	border: 1px solid rgb(140, 30, 30) !important;
}

div#dialog_actions_table div.floors div.bathroom{
	border: 2px solid #FFF !important;
}

div#dialog_actions_table div.floors div.entrance{
	border: 2px solid #FFF !important;
}

div#dialog_actions_table div.floors div.stairs{
	border: 1px solid rgb(74, 74, 74) !important;
}

div#dialog_actions_table div.floors div.item div.vlfi_id{
	display: none;
}

div#dialog_actions_table div.client table tbody td{
	vertical-align: top;
}

div#dialog_actions_table div.client table tbody td.assigned_table div.table{
	width: 48px !important;
	height: 48px !important;
	background: red;
	border: 1px solid #000;
	color: #FFF;
	margin-left: auto;
	margin-right: auto;
}

div#dialog_actions_table div.client table tbody td.assigned_table div.table div.vlfit_id{
	display: none;
}

div#dialog_actions_table div.client table tbody td.assigned_table div.table span.title{
	position: relative; 
	top: -3px;
}

div#dialog_actions_table div.client table tbody td.assigned_table div.table div.max_capacity{
	position: relative;
	top: -10px;
}

img.loading_indicator{
	margin-left: auto;
	margin-right: auto;
	display: none;
}

p.assign_title{
	font-size: 14px;
}

p.assign_title span.venue_name{
	font-weight: bold;
	color: red;
}
</style>

<div id="dialog_actions_table" style="display:none;">
	
	<p class="assign_title"><span class="venue_name"></span> @ <span class="date"></span></p>
	
	<div class="floors"></div>
	
	<div class="client" style="width:100%;">
		<table class="normal" style="margin-left:auto; margin-right:auto;">
			<thead>
				<th>Head User</th>
				<th>Picture</th>
				<th>Venue</th>
				<th>Promoter</th>
				<th>Request Msg</th>
				<th>Minimum Spend</th>
				<th>Entourage</th>
				<th>Assigned Table</th>
			</thead>
			<tbody>
				<tr>
					<td class="user_name"></td>
					<td class="user_pic"></td>
					<td class="venue"></td>
					<td class="promoter"></td>
					<td class="request_msg"></td>
					<td class="min_spend"></td>
					<td class="entourage"></td>
					<td class="assigned_table"></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<script type="text/javascript">
		jQuery(function(){
			var characters = 160;
			jQuery("div#dialog_actions_table span.dialog_actions_message_remaining").html("You have <strong>" + characters + "</strong> characters remaining");
			
			jQuery("div#dialog_actions_table textarea[name = message]").keyup(function(){
			    if(jQuery(this).val().length > characters){
			        jQuery(this).val(jQuery(this).val().substr(0, characters));
			    }
			        
			    var remaining = characters - jQuery(this).val().length;
				jQuery("div#dialog_actions_table span.dialog_actions_message_remaining").html("You have <strong>" + remaining + "</strong> characters remaining");
			});
		});
	</script>
	
	<div style="clear: both;"></div>

	<form>
		<fieldset>
			<label for="message">Send <span class="name"></span> a message: (optional)</label>
			<textarea rows="3" style="resize:none; width:100%; border:1px solid #333;" name="message"></textarea>
			<br>
			<span class="dialog_actions_message_remaining"></span>
		</fieldset>
	</form>
	
	<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
	<p class="error" style="display:none;"></p>
	
</div>

<div id="dialog_actions" style="display: none;">

	<script type="text/javascript">
		jQuery(function(){
			var characters = 160;
			jQuery("div#dialog_actions span.dialog_actions_message_remaining").html("You have <strong>" + characters + "</strong> characters remaining");
			
			jQuery("div#dialog_actions textarea[name = message]").keyup(function(){
			    if(jQuery(this).val().length > characters){
			        jQuery(this).val(jQuery(this).val().substr(0, characters));
			    }
			        
			    var remaining = characters - jQuery(this).val().length;
				jQuery("div#dialog_actions span.dialog_actions_message_remaining").html("You have <strong>" + remaining + "</strong> characters remaining");
			});
		});
	</script>
	
	<p class="assign_title"><span class="venue_name"></span> @ <span class="date"></span></p>
	
	<span>
		<img src="" class="pic_square" style="float: left; margin-right: 5px;" alt="picture" />
		<span class="name"></span>'s reservation request.
	</span>
	
	<div style="clear: both;"></div>
	<br>

	<form>
		<fieldset>
			<label for="message">Send <span class="name"></span> a message: (optional)</label>
			<textarea rows="3" style="resize:none; width:100%; border:1px solid #333;" name="message"></textarea>
			<br>
			<span class="dialog_actions_message_remaining"></span>
		</fieldset>
	</form>
	
	<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />

</div>