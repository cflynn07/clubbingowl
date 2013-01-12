<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->team 							= $team;
	$page_obj->users 							= $users;
	$page_obj->statistics 						= $statistics;
	$page_obj->trailing_gl_requests_categories 	= array_keys($statistics->trailing_gl_requests);
	$page_obj->trailing_gl_requests_values 		= array_values($statistics->trailing_gl_requests);
	$page_obj->users_oauth_uid 					= $users_oauth_uid;
	$page_obj->team_chat_members 				= $team_chat_members;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>



<?php Kint::dump($team); ?>
<?php Kint::dump($announcements); ?>




<?php //$this->load->view('admin/_common/view_manager_dialog_reservation_respond'); ?>
<?php //$this->load->view('admin/_common/view_page_video_tutorial'); ?>




<div id="manager_dashboard_wrapper">
	<h1 style="display:inline-block;">Manager Dashboard</h1>
	<?php if(false): ?>
	 - (<span class="page_video_tutorial"><img class="page_video_tutorial" src="<?= $central->admin_assets ?>images/icons/small_icons/Film.png" /> <span>Video Tutorial</span></span>)
	<?php endif; ?>
	
	
	<h3>
		Pending Reservation Requests 
		<img class="info_icon tooltip" title="Outstanding team and promoter guest list and table reservation requests from your widgets and promoters." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
	</h3>
	<div id="pending_reservations" class="full_width last reservations" style="width:980px;">
		<table class="normal" style="width:100%;vertical-align:text-top !important;">
			<thead>
				<tr>
					<th>Head User</th>
					<th>Promoter</th>
					<th>Venue</th>
					<th>Guest List</th>
					<th>Date</th>
					<th>Messages</th>
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




	<div class="resizable_container_parent full_width" id="team_announcements"  style="width:980px; padding:0; overflow:visible;">
		<h3>
			Team Announcements
			<img class="info_icon tooltip" title="Announcements for team members" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
		</h3>
		
		<img id="messages_loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..."  />
		
		<div id="resize_box" style="height:320px; min-height: 200px; margin-bottom:10px;">
			<div id="team_announcements_content" style="display:none; height:100%; overflow-y: scroll; overflow-x: hidden; padding: 10px; border-bottom:1px dashed #CCC;">
				
				
				
				
				
				
				<?php foreach($announcements as $an): ?>
					<div>
						
						<table class="normal" style="width:100%;">
							<tbody>
								<tr>
									<td style="width:50px; border-right:1px solid #CCC;">
										<div data-oauth_uid="<?= $an->manager_oauth_uid ?>" class="manager_pic pic_square_<?= $an->manager_oauth_uid ?>"></div>
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
											
											<?php if(false): ?>
												<div class="announcement_message"><span data-oauth_uid="<?= $an->manager_oauth_uid ?>" class="name_<?= $an->manager_oauth_uid ?>"</span> has updated their notes on <a class="ajaxify" href="<?= $central->manager_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><span data-oauth_uid="<?= $message->client_oauth_uid ?>" class="name_<?= $message->client_oauth_uid ?>"></span></a></div>
											<?php endif; ?>
											
												<div class="announcement_message"><span class="name_<?= $an->manager_oauth_uid ?>"></span> has updated their notes on <a data-oauth_uid="<?= $message->client_oauth_uid ?>" class="ajaxify" href="<?= $central->manager_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><span class="name_<?= $message->client_oauth_uid ?>"></span></a></div>
												<br/><a class="ajaxify" href="<?= $central->manager_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><img style="vertical-align:top; margin-right:5px;" src="https://graph.facebook.com/<?= $message->client_oauth_uid ?>/picture" /></a>
												<span>"<?= $message->public_notes ?>"</span>
												
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
		
		
		<br/>
		<a data-action="create-announcement" id="create_announcement_btn" class="button_link" style="float:right;" href="#">Create Announcement</a>

		<div id="announcement_dialog" style="display:none;">
			<p>New Staff Announcement</p>
			<textarea id="manager_announcement_textarea"></textarea>
			
			<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." style="display:none; margin-top:4px; margin-left:auto; margin-right:auto;" />
			<p id="manager_announcement_msg" style="margin-left:auto; margin-right:auto; margin-top:4px;"></p>
		
		</div>
		
	</div>
	
	
	<hr>
	
	
	<div style="clear:both;"></div>
	
	
	
	
	
	
	<div id="team_statistics" class="full_width" style="width:980px;">
		<h3>
			Team Statistics 
			<img class="info_icon tooltip" title="Basic statistics related to your team." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
		</h3>
		<div style="clearboth"></div>
		
		<div class="tabs team_stats_tabs" style="width:980px; display:none;">
			<div class="ui-widget-header">
				<span>Statistics</span>
				<ul>
					<li><a href="#tabs-1">Widget Views</a></li>
					<li><a href="#tabs-2">Team Reservation Requests</a></li>
				</ul>
			</div>
			<img id="loading_gif" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		
			<div id="tabs-1" style="display:none; padding: 0px;"></div> <!-- end of first tab -->
			<div id="tabs-2" style="padding: 0px;"></div> <!-- end of second tab -->
		</div>
	</div>
	<script type="text/javascript">
		jQuery('div.tabs').tabs().css('display', 'block');
	</script>
	
	
	
	
	
	
	
	
	<div style="clear:both"></div>
	
	<hr>
	
	
	
	
	
	
	
	<div style="width:980px;">
		<div class="one_half">
			
			<h3>
				Live Team Visitors 
				<img class="info_icon tooltip" title="ClubbingOwl users that are currently viewing your team's promoters, venue pages, and widgets" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			<div class="ui-widget" style="width:100%;">
				<div class="ui-widget-header">
					Live Visitors
				</div>
				
				<?php // $this->load->view('admin/_common/view_team_visitors_presence'); ?>
				
				
				
				
				<div id="live_visitors">
					<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
				</div>				
			</div>
					
		</div>
		
		<div class="one_half last">
			
			<h3>
				Top Team Visitors 
				<img class="info_icon tooltip" title="ClubbingOwl users frequently visit your team's promoter profiles, venue pages, and widgets" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			<div class="ui-widget" style="width:100%;">
				<div class="ui-widget-header">
					Top Visitors
				</div>				
				
				<div id="top_visitors">
					<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
				</div>				
			</div>
			
		</div>
		
		<div class="one_half last">
			
			<h3>
				Recent Team Visitors 
				<img class="info_icon tooltip" title="The 100 most recent ClubbingOwl users to visit your promoter profiles, venue pages, and widgets." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			
			<div class="ui-widget" style="width:100%;">
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
	
	<div style="clear:both"></div>
	
	<hr>

	<div style="width:980px;">
		<div class="one_half">
			
			<h3>
				Venue Statistics 
				<img class="info_icon tooltip" title="Team Venues and related statistics." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			<table class="normal tablesorter" style="width:100%;">
				<thead>
					<tr>
						<th>Default Image</th>
						<th>Venue</th>
						<th>Clients</th>
						<th>All-Time Requests</th>
						<th>Upcoming Requests</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($statistics->team_venues as $key => $venue): ?>
						<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
							<td><img src="<?= $central->s3_uploaded_images_base_url . 'venues/banners/' . $venue->tv_image . '_t.jpg' ?>" alt="venue image" style="width:100px;" /></td>
							<td><?= $venue->tv_name ?></td>
							<td><?= count($venue->clients) ?></td>
							<td><?= count($venue->all_time_guest_list_reservations) ?></td>
							<td><?= count($venue->upcoming_guest_list_reservations) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
		</div>
		
		<div class="one_half last">
			<h3>
				Promoter Statistics 
				<img class="info_icon tooltip" title="Team promoters and related statistics." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
			</h3>
			<table class="normal tablesorter" style="width:100%;">
				<thead>
					<tr>
						<th>Profile Image</th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($statistics->active_promoters as $key => $ap): ?>
						<?php 
						
							if($ap->up_completed_setup == 0 || $ap->up_banned == 1){
								continue;
							}
						
						?>
						<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
							<td><img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $ap->up_profile_image . '_t.jpg' ?>" alt="profile image" /></td>
							<td><?= $ap->u_full_name ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	
</div>




	<div style="clear:both"></div>
	
	
	
	
<?php else: ?>
	
	
	
	
	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>
	
	<h1>Manager Statistics</h1>
	
	<p>When your team is live you will be able to see statistics related to the amount of traffic your fan-page application recieves, your venues & guest lists, and your promoters.</p>




<?php endif; ?>

<div class="clearboth"></div>

