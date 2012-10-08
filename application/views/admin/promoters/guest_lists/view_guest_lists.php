<?php 
	$page_obj = new stdClass;
	$page_obj->users = json_decode($users);
	$page_obj->weekly_guest_lists = $weekly_guest_lists;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>





<div id="dialog_actions" style="display: none;">

	<span>
		<img src="" class="pic_square" style="float: left; margin-right: 5px;" alt="picture" />
		<span class="name"></span>'s reservation request.
	</span>
	
	<div style="clear: both;"></div>
	<br>

	<form>
		<fieldset>
			<label for="message">Send <span class="name"></span> a message: (optional)</label>
			<textarea rows="5" style="resize:none; width:100%; border:1px solid #333;" name="message"></textarea>
			<br><br>
			<span id="dialog_actions_message_remaining"></span>
		</fieldset>
	</form>
	
	<div id="dialog_actions_loading_indicator" style="text-align: center; display: none;">
		<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
	</div>

</div>




<div id="loading_indicator">
	<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
</div>







<div id="guest_list_content" class="tabs" style="display:none; width:1050px">
	
	<div class="ui-widget-header">
		<span>Promoter Guest Lists</span>
	</div>
	
	<br>
	
	<div class="one_fourth">
		<ul class="sitemap" style="cursor: default; text-decoration:none !important;">
			<?php foreach(array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays') as $weekday): 
					$day_displayed = false;
			?>
			
				<?php foreach($weekly_guest_lists as $wgl): ?>
					
					<?php if($wgl->pgla_day == $weekday): ?>
						
						<?php if(!$day_displayed): ?>
							<span style="color: red; font-weight: bold;"><?= ucfirst($weekday) ?></span><br>
							<?php $day_displayed = true; ?>
						<?php endif; ?>
						
						<li class="<?= $wgl->pgla_id ?>" style="margin-left:15px;text-decoration:none;"><span style="text-decoration:underline;"><?= $wgl->pgla_name ?></span> (<span class="wgl_groups_count"><?= count($wgl->groups) ?></span>)<span class="pgla_id" style="display:none"><?= $wgl->pgla_id ?></span></li><br>
				
					<?php endif; ?>
					
				<?php endforeach; ?>
				
			<?php endforeach; ?>
		</ul>
		
		<hr>
		<br>
		<br>
		
		<div class="datepicker"></div>
		
	</div>


	
	<div id="lists_container" class="three_fourth last">
		<?php foreach($weekly_guest_lists as $wgl): ?>
			
			<div class="gl_status gl_status_<?= $wgl->pgla_id ?>" style="display:none;">
				<div class="ui-widget">
					<div class="ui-widget-header">
						<span>"<?= $wgl->pgla_name ?>" Status</span>
						<span style="float:right;color:grey;">Last Updated: Monday April 12, 2012</span>
					</div>
					
					<div class="ui-widget-content" style="padding:5px;padding-left:20px;">
						<span style="color:blue;text-decoration:underline;">Line is gonna be super long! Make sure to get here by 11! Get wild.</span>
					</div>
				</div>
			
				<hr>
				<div style="clear:both"></div>
			</div>
			
			
			<div class="list tabs" id="pgla_<?= $wgl->pgla_id ?>" style="display:none;">						
				
				<div class="ui-widget-header">
					<span>"<?= $wgl->pgla_name ?>" @ <span style="font-weight: bold;"><?= $wgl->tv_name ?></span></span>
					<span style="float:right;">
						<span class="pgla_id" style="display:none;"><?= $wgl->pgla_id ?></span>

						<input type="text" class="guest_list_datepicker" value="<?= date('l F j, Y', strtotime(rtrim($wgl->pgla_day, 's'))) ?>" style="height:10px; margin-right:-5px;"/>
					
					</span>
				</div>	
				
				<table class="normal tablesorter guestlists" style="width: 770px;">
					<thead>
						<tr>
							<th>Head User</th>
							<th>Picture</th>
							<th>Messages</th>
							<th>Table</th>
							<th>Status</th>
							<th>Entourage</th>
						</tr>
					</thead>
					<tbody>
						<tr style="display:none;"><td class="pgla_id"><?= $wgl->pgla_id ?></td></tr>
						<?php foreach($wgl->groups as $key1 => $group): ?>
						<tr>
							<td class="pglr_id hidden hidden" style="display:none"><?= $group->id ?></td>
							<td class="pglr_head_user hidden" style="display:none"><?= $group->head_user ?></td>
							<td><div class="name_<?= $group->head_user ?>"></div></td>
							<td><div class="pic_square_<?= $group->head_user ?>"></div></td>
							<td>
								<table class="user_messages" style="width:152px; text-wrap: unrestricted;">
									<tr><td class="message_header">Request Message:</td></tr>
									<tr><td><?= (strlen($group->pglr_request_msg)) ? $group->pglr_request_msg : ' - ' ?></td></tr>
									<tr><td class="message_header">Response Message:</td></tr>
									<tr><td class="response_message"><?= (strlen($group->pglr_response_msg)) ? $group->pglr_response_msg : ' - ' ?></td></tr>
									<tr><td class="message_header">Host Notes:</td></tr>
									<tr style="max-width:122px;">
										<td class="host_notes" style="max-width:122px;">
											<div class="edit" style="display:none;">
												<textarea></textarea>
												<br>
												<span class="message_remaining"></span>
											</div>
											<span class="original">
												<?= (strlen($group->pglr_host_message)) ? $group->pglr_host_message : '<span style="font-weight: bold;">Edit Message</span>' ?>
											</span>
											<img class="message_loading_indicator" style="display:none;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
										</td>
									</tr>
								</table>
							</td>
							<td><?= ($group->pglr_table_request == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
							<td class="actions">
								
								<?php if($group->pglr_approved == '1'): ?>
									<span style="color: green;">Approved</span>
								<?php elseif($group->pglr_approved == '-1'): ?>
									<span style="color: red;">Declined</span>
								<?php else: ?>
									<span class="app_dec_action" style="font-weight: bold; text-decoration: underline; cursor: pointer; color: blue;">Requested</span>
								<?php endif; ?>
								
							</td>
							<td style="white-space:nowrap; width:244px;">
								<?php if(!count($group->entourage_users)): ?>
									<p>No Entourage</p>
								<?php else: ?>
								<table>
									<thead>
										<tr>
											<th>Name</th>
											<th>Picture</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($group->entourage_users as $key2 => $ent_user): ?>
											<tr <?= ($key2 % 2) ? 'class="odd"' : '' ?>>
												<td><div class="name_<?= $ent_user ?>"></div></td>
												<td><div class="pic_square_<?= $ent_user ?>"></div></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
						<?php if(!$wgl->groups): ?>
							<tr class="no_reservations"><td colspan=7>This weeks guest list does not have any reservations yet.</td></tr>
						<?php endif; ?>
						
						<tr>
							<div class="pgla_id" style="display:none;"><?= $wgl->pgla_id ?></div>
							<td class="facebook_gl_invite" style="text-align:center; cursor:pointer; background-color:#333; color:#FFF;" colspan=7>
								<img src="<?= $central->admin_assets ?>images/icons/small_icons/Create.png" alt="" style="vertical-align: middle; margin-right: 5px;" />
								<span style="vertical-align: middle; text-decoration:underline;">Add your Facebook friends to this guest list.</span>
							</td>
						</tr>
										
					</tbody>
				</table>
						
			</div>
		<?php endforeach; ?>
	</div>
	
	<div style="clear:both"></div>
	
</div>















<?php if(false): ?>






<?php
/**
 * EJS Template
 * 
 * Template for new guest list request recieved via PUSHER
 */
?>

<div id="ejs promoter_guest_lists_templates" style="display:none;">
	
	<div id="ejs_guest_list_reservation">
	
			
								<tr style="display:none;"><td class="pgla_id"><?= $wgl->pgla_id ?></td></tr>
								<?php foreach($wgl->groups as $key1 => $group): ?>
								<tr>
									<td class="pglr_id hidden hidden" style="display:none"><?= $group->id ?></td>
									<td class="pglr_head_user hidden" style="display:none"><?= $group->head_user ?></td>
									<td><div class="name_<?= $group->head_user ?>"></div></td>
									<td><div class="pic_square_<?= $group->head_user ?>"></div></td>
									<td>
										<table class="user_messages" style="width:152px; text-wrap: unrestricted;">
											<tr><td class="message_header">Request Message:</td></tr>
											<tr><td><?= (strlen($group->pglr_request_msg)) ? $group->pglr_request_msg : ' - ' ?></td></tr>
											<tr><td class="message_header">Response Message:</td></tr>
											<tr><td class="response_message"><?= (strlen($group->pglr_response_msg)) ? $group->pglr_response_msg : ' - ' ?></td></tr>
											<tr><td class="message_header">Host Notes:</td></tr>
											<tr style="max-width:122px;">
												<td class="host_notes" style="max-width:122px;">
													<div class="edit" style="display:none;">
														<textarea></textarea>
														<br>
														<span class="message_remaining"></span>
													</div>
													<span class="original">
														<?= (strlen($group->pglr_host_message)) ? $group->pglr_host_message : '<span style="font-weight: bold;">Edit Message</span>' ?>
													</span>
													<img class="message_loading_indicator" style="display:none;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
												</td>
											</tr>
										</table>
									</td>
									<td><?= ($group->pglr_table_request == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
									<td class="actions">
										
										<?php if($group->pglr_approved == '1'): ?>
											<span style="color: green;">Approved</span>
										<?php elseif($group->pglr_approved == '-1'): ?>
											<span style="color: red;">Declined</span>
										<?php else: ?>
											<span class="app_dec_action" style="font-weight: bold; text-decoration: underline; cursor: pointer; color: blue;">Requested</span>
										<?php endif; ?>
										
									</td>
									<td style="white-space:nowrap; width:244px;">
										<?php if(!count($group->entourage_users)): ?>
											<p>No Entourage</p>
										<?php else: ?>
										<table>
											<thead>
												<tr>
													<th>Name</th>
													<th>Picture</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($group->entourage_users as $key2 => $ent_user): ?>
													<tr <?= ($key2 % 2) ? 'class="odd"' : '' ?>>
														<td><div class="name_<?= $ent_user ?>"></div></td>
														<td><div class="pic_square_<?= $ent_user ?>"></div></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php if(!$wgl->groups): ?>
									<tr class="no_reservations"><td colspan=7>This weeks guest list does not have any reservations yet.</td></tr>
								<?php endif; ?>
								
								<tr>
									<div class="pgla_id" style="display:none;"><?= $wgl->pgla_id ?></div>
									<td class="facebook_gl_invite" style="text-align:center; cursor:pointer; background-color:#333; color:#FFF;" colspan=7>
										<img src="<?= $central->admin_assets ?>images/icons/small_icons/Create.png" alt="" style="vertical-align: middle; margin-right: 5px;" />
										<span style="vertical-align: middle; text-decoration:underline;">Add your Facebook friends to this guest list.</span>
									</td>
								</tr>
																
	</div>
	
</div>


<script type="text/javascript">






<?php
/**
 * Promoter Pusher Notifications - Guest Lists
 * 
 * 		Subscribes to notifications regarding guest lists for promoters from pusher
 */
?>
jQuery(function(){
	
	var pusher_init = function(){
				
		/*
		
		team_chat_channel.bind('promoter_guest_list_reservation', function(){
		
			if(data.promoter_oauth_uid != '<?= $users_oauth_uid ?>')
				return;		//Event is not for THIS promoter on the team
			
			if(data.manual_add == 1 || data.manual_add == '1')
				return;		//Event fired for manually initiated event by promoter
				
			// ------------- Assemble all UIDS of all users in request ------------
			if(Object.prototype.toString.call(data.entourage) === '[object Array]')
				var users = data.entourage;
			else
				users = [];
			
			users.push(data.head_oauth_uid);
			// ------------- Assemble all UIDS of all users in request ------------
			
			jQuery.fbUserLookup(users, '', function(rows){
				
				//find head user
				var fb_head_user;
				var entourage;
				for(var i=0; i<rows.length; i++){
					if(rows[i].uid == data.head_oauth_uid){
						fb_head_user = rows[i];
						entourage = rows.slice();
						entourage.splice(i, 1);
						break;
					}
				}
				
				//find the promoter
				var promoter;
				for(var i=0; i < vc_team_chat_users.length; i++){
					if(vc_team_chat_users[i].uid == data.promoter_oauth_uid){
						promoter = vc_team_chat_users[i];
						break;
					}
				}
				
				//if head user is already on this guest list (manual_add) - remove
				jQuery('tbody tr td.pgla_id').each(function(){
					
					var pgla_id = jQuery(this).html();
													
					if(pgla_id == data.pgla_id){
						//we're on the current GL
															
						jQuery(this).parent().parent().find('td.pglr_head_user').each(function(){
							//remove if already present
						
							if(jQuery(this).html() == data.head_oauth_uid){
								jQuery(this).parent().remove();
							}
						
						});
						
						
						
						
						
						
						
						
						
						
						
						
						//now add new reservation
						var table_html = '<tr class="new_add">';
						table_html += '<td class="pglr_id hidden" style="display:none">' + data.pglr_id + '</td>';
						table_html += '<td class="pglr_head_user hidden" style="display:none">' + data.head_oauth_uid + '</td>';
			
						table_html += '<td><span class="name_' + data.head_oauth_uid + '"></span></td>';
						table_html += '<td><div class="pic_square_' + data.head_oauth_uid + '"></div></td>';
						
						
						table_html += '<td>';
						table_html += '<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
						table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
						table_html += '		<tr><td>' + ((data.request_msg.length > 0) ? data.request_msg : ' - ') + '</td></tr>';
						table_html += '		<tr><td class="message_header">Response Message:</td></tr>';
						table_html += '		<tr><td class="response_message"> - </td></tr>';
						table_html += '		<tr><td class="message_header">Host Notes:</td></tr>';
						table_html += '		<tr style="max-width:122px;">';
						table_html += '			<td class="host_notes" style="max-width:122px;">';
						table_html += '				<div class="edit" style="display:none;">';
						table_html += '					<textarea></textarea>';
						table_html += '					<br>';
						table_html += '					<span class="message_remaining"></span>';
						table_html += '				</div>';
						table_html += '				<span class="original">';
						table_html += '					<span style="font-weight: bold;">Edit Message</span>';
						table_html += '				</span>';
						table_html += '				<img class="message_loading_indicator" style="display:none;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />';
						table_html += '			</td>';
						table_html += '		</tr>';
						table_html += '</table>'; 
						table_html += '</td>';
						
						table_html += '<td><span style="color:' + ((data.table_request == 1) ? 'green' : 'red') + ';">' + ((data.table_request == 1) ? 'Yes' : 'No') + '</span></td>';
						table_html += '<td>' + ((data.approved == 1) ? '<span style="color:green;">Approved</span>' : '<span class="app_dec_action" style="font-weight: bold; text-decoration: underline; cursor: pointer; color: blue;">Requested</span>') + '</td>';
						
						if(data.entourage.length > 0){
							table_html += '<td style="white-space:nowrap; width:244px;">';
							table_html += '		<table>';
							table_html += '			<thead>';
							table_html += '				<tr>';
							table_html += '					<th>Name</th>';
							table_html += '					<th>Picture</th>';
							table_html += '				</tr>';
							table_html += '			</thead>';
							table_html += '			<tbody>';
							for(var i=0; i<data.entourage.length; i++){
								
								if(data.entourage[i] == data.head_oauth_uid)
									continue;
									
								table_html += '<tr' + ((i % 2) ? ' class="odd"' : '') + '>';
								table_html += '		<td><span class="name_' + data.entourage[i] + '"></span></td>';	
								table_html += '		<td><div class="pic_square_' + data.entourage[i] + '"></div></td>';	
								table_html += '</tr>';											
							}
							table_html += '			</tbody>';
							table_html += '		</table>';
							table_html += '</td>';
						}else{
							table_html += '<td style="white-space: nowrap;"><p>No Entourage</p></td>';
						}
			
						table_html += '</tr>';
						
						
						
						
						
						
						
						
						
						
						
						
						jQuery(this).parent().parent().find('tr.no_reservations').remove();			
						
						
						
						
						
								
						jQuery(table_html).insertBefore(jQuery(this).parent().parent().find('tr:last'));
						
						
						
						
						
						//-------------
						//set new guest list visible
						jQuery('ul.sitemap li').css('font-weight', 'normal');
						jQuery('ul.sitemap').find('li.' + data.pgla_id).css('font-weight', 'bold');
						
						var count = parseInt(jQuery('ul.sitemap').find('li.' + data.pgla_id).find('span.wgl_groups_count').html());
						count++;
						jQuery('ul.sitemap').find('li.' + data.pgla_id).find('span.wgl_groups_count').html(count);
						
						jQuery('div#lists_container div.list').css('display', 'none');
						jQuery('div#pgla_' + data.pgla_id).css('display', 'block');
						//-------------
						
						//find out offset of the added GL entry
						var offset = jQuery('tr.new_add').offset();
						jQuery(document).scrollTop(offset.top - 50);
						
						window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
						
						
						
						
						for(var i=0; i<rows.length; i++){
							jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
							jQuery('span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						}
						
						jQuery('tr.new_add td').show('highlight', { color:'red' }, 1500, function(){
							jQuery('tr.new_add').removeClass('new_add');
							
							
						});
						
					}								
					
				});
			});
	
		}
		*/
					
	}
	
	window.EventHandlerObject.addListener('pusher_init', pusher_init);
	
})
</script>

<?php endif; ?>