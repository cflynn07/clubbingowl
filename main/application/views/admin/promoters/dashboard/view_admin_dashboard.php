<?php 
	$page_obj = new stdClass;
	$page_obj->statistics 			= $statistics;
	$page_obj->team_chat_members 	= $team_chat_members;
	$page_obj->users 				= $users;
	
	
	$page_obj->trailing_req_chart_categories 	= array_keys($statistics->trailing_weekly_guest_list_reservation_requests);
	$page_obj->trailing_req_chart_values 		= array_values($statistics->trailing_weekly_guest_list_reservation_requests);
	$page_obj->backbone_pending_reservations 	= $statistics->backbone_pending_reservations;
	$page_obj->pending_reservations_users 		= $statistics->pending_reservations_users;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<div id="s_admin_promoter_dashboard">
	
		<?php // $this->load->view('admin/_common/view_promoter_dialog_reservation_respond'); ?>
		<?php $this->load->view('admin/_common/view_page_video_tutorial'); ?>
		
		<h1 style="display:inline-block;">Promoter Dashboard</h1>
		
		<?php if(false): ?>
		 - (<span class="page_video_tutorial"><img src="<?= $central->admin_assets ?>images/icons/small_icons/Film.png" /> <span>Video Tutorial</span></span>)
		<?php endif; ?>
		
		
		
		
		<?php Kint::dump($statistics); ?>
		
		
		
		
		<h3>
			Pending Reservation Requests
			<img class="info_icon tooltip" title="Pending guest list and table reservation requests." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
		</h3>
		<div id="pending_reservations" class="full_width last reservations" style="width: 1050px;">
			<table class="normal" style="width:100%; vertical-align:text-top !important;">
				<thead>
					<tr>
						<th>Head User</th>
						<th>Venue</th>
						<th>Guest List</th>
						<th>Date</th>
						<th>Request Message</th>
						<th>Entourage</th>
						<th>Table Request</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

		<hr>
		
		<div id="team_announcements_parent" class="resizable_container_parent full_width" style="width:1050px; padding:0; overflow:visible;">
			<h3>
				Team Announcements
				<img class="info_icon tooltip" title="Send announcements to your team members" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			
			<img id="messages_loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
						
			<div id="team_announcements" style="display:none; height:320px; min-height:0; overflow-y: scroll; padding:0; overflow-x: hidden; padding: 10px; border-bottom:1px dashed #CCC;">		
				
				<?php foreach($announcements as $an): ?>
					<div>
						
						<table class="normal" style="width:100%;">
							<tbody>
								<tr>
									<td style="width:50px; border-right:1px solid #CCC;">
										<div class="manager_pic pic_square_<?= $an->manager_oauth_uid ?>"></div>
										<p style="margin:0; white-space:nowrap;" class="name_<?= $an->manager_oauth_uid ?>"></p>
										<p style="margin:0; white-space:nowrap;"><?= date('m.d.y g:i a', $an->created) ?></p>
									</td>
									<td style="padding-top:10px;">
										
										<?php if($an->type == 'regular'): ?>
											<div class="announcement_message"><?= $an->message ?></div>
										<?php else: ?>
											
											<?php $message = json_decode($an->message); 
												if($message->subtype == 'new_client_notes'):
											?>
												
												<div class="announcement_message"><span class="name_<?= $an->manager_oauth_uid ?>"></span> has updated their notes on <a data-oauth_uid="<?= $message->client_oauth_uid ?>" class="ajaxify" href="<?= $central->promoter_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><span class="name_<?= $message->client_oauth_uid ?>"></span></a></div>
												<br/><a class="ajaxify" href="<?= $central->manager_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><img src="https://graph.facebook.com/<?= $message->client_oauth_uid ?>/picture" /></a>
												
											<?php endif; ?>
											
										<?php endif; ?>
									
									</td>
								</tr>
							</tbody>
						</table>
						<?php if(!$announcements): ?>
							<div style="text-align:center;">No Announcements</div>
						<?php endif; ?>							
					</div>
				<?php endforeach; ?>

				
			</div>	
			
		</div>

		<hr>
		
		<div style="clear:both;"></div>
		
		
		
		
		
		
		
		
		
		<h3>
			Promoter Statistics
			<img class="info_icon tooltip" title="Basic information about your profile page and guest lists." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" /> 
		</h3>
		<div style="clearboth"></div>
		
		<div id="promoter_stats_tabs" class="tabs promoter_stats_tabs" style="width:1050px;">
			<div class="ui-widget-header">
				<span>Statistics</span>
				<ul>
					<li><a href="#tabs-1">Profile Views</a></li>
					<li><a href="#tabs-2">Reservation Requests</a></li>
				</ul>
			</div>
			
			<img id="loading_gif" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		
			<div id="tabs-1" style="display:none; padding: 0px;"></div> <!-- end of first tab -->
			<div id="tabs-2" style="padding: 0px;"></div> <!-- end of second tab -->
		</div>
		
		
		
		
		
		
		<hr>
		
		
		
		
		
		
		
		<div style="width:1050px;">
			

			<div id="live_visitors_wrapper" class="one_half">
				<h3>
					Live Profile Visitors 
					<img class="info_icon tooltip" title="ClubbingOwl users that are actively viewing your promoter profile." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				<div class="visitors ui-widget" style="width:100%;">
					<div class="ui-widget-header">
						<span>Live Visitors</span>
					</div>										
					<div id="live_visitors">
						<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
					</div>				
				</div>
			</div>
						
				
				
				
			<div id="top_visitors_wrapper" class="one_half last">
				<h3>
					Top Profile Visitors
					<img class="info_icon tooltip" title="ClubbingOwl users that frequently visit your promoter profile." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				<div class="visitors ui-widget" style="width:100%;">
					<div class="ui-widget-header">
						<span>Top Visitors</span>
					</div>
					<div id="top_visitors">
						<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
					</div>				
				</div>
			</div>
			
			
			
			
			<div id="recent_visitors_wrapper" class="one_half last">
				<h3>
					Recent Profile Visitors 
					<img class="info_icon tooltip" title="The 100 most recent ClubbingOwl users to visit your promoter profile." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				<div class="visitors ui-widget" style="width:100%;">
					<div class="ui-widget-header">
						<span>Recent Visitors</span>
					</div>									
					<div id="recent_visitors" class="ui-widget-content">
						<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
					</div>
				</div>
			</div>
			
			
			<div style="clear:both;"></div>
		</div>
		
		
		
		
		
		
		
		<hr>
		
		<div style="width:1050px;">
			
			<div class="one_half last">
				<h3>
					Team & Client Statistics 
					<img class="info_icon tooltip" title="Basic information about your team and clients." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				<table class="normal tablesorter" style="width:100%;">
					<thead>
						<tr>
							<th>Statistic</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td>Team</td>
							<td><a href="http://www.facebook.com/pages/@/<?= $promoter->team->t_fan_page_id ?>?sk=app_<?= $this->config->item('facebook_app_id') ?>" target="_new"><?= $promoter->team->t_name ?></a></td>
						</tr>
						
						<tr>
							<td>Clients</td>
							<td><?= $statistics->num_clients ?></td>
						</tr>	
						
						<tr class="odd">
							<td>Upcoming Requests</td>
							<td><?= $statistics->num_upcoming_guest_list_reservations->count ?></td>
						</tr>
						
						<tr>
							<td>All-Time Requests</td>
							<td><?= $statistics->num_total_guest_list_reservations->count ?></td>
						</tr>
					
					</tbody>
				</table>
				<div class="clearboth"></div>
			</div>
			
		</div>

</div>