<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->team = $team;
	$page_obj->users = $users;
	$page_obj->statistics = $statistics;
	$page_obj->trailing_gl_requests_categories = array_keys($statistics->trailing_gl_requests);
	$page_obj->trailing_gl_requests_values = array_values($statistics->trailing_gl_requests);
	$page_obj->users_oauth_uid = $users_oauth_uid;
	$page_obj->team_chat_members = $team_chat_members;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>








<?php //$this->load->view('admin/_common/view_manager_dialog_reservation_respond'); ?>
<?php //$this->load->view('admin/_common/view_page_video_tutorial'); ?>




<div id="manager_dashboard_wrapper">
	<h1 style="display:inline-block;">Manager Dashboard</h1> - (<span class="page_video_tutorial"><img class="page_video_tutorial" src="<?= $central->admin_assets ?>images/icons/small_icons/Film.png" /> <span>Video Tutorial</span></span>)
	
	<h3>
		Pending Reservation Requests 
		<img class="info_icon tooltip" title="Outstanding team and promoter guest list and table reservation requests from your widgets and promoters." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
	</h3>
	<div id="pending_reservations" class="full_width last reservations" style="width:1050px;">
		<table class="normal" style="width:100%;vertical-align:text-top !important;">
			<thead>
				<tr>
					<th>Head User</th>
					<th>Picture</th>
					<th>Venue</th>
					<th>Promoter</th>
					<th>Date</th>
					<th>Entourage</th>
					<th>Request Msg</th>
					<th>Table Request</th>
					<th>Minimum Spend</th>
					<th>Contact Number</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php $no_groups = true; ?>
				<?php foreach($statistics->team_venues as $team_venue): ?>
					<?php foreach($team_venue->tv_gla as $tv_gla): ?>
						
						<?php if($tv_gla->current_list): ?>
							
							<?php foreach($tv_gla->current_list->groups as $group): ?>
								
								<?php if($group->tglr_approved == "0" || $group->tglr_approved == 0): ?>
									<?php $no_groups = false; ?>
								
									<tr style="display:none;">
										
										<td class="request_type" style="display:none;">manager</td>
										<td class="table" style="display:none;"><?= $group->tglr_table_request ?></td>
										<td class="tglr_id" style="display:none;"><?= $group->tglr_id ?></td>
										<td class="tglr_head_user" style="display:none;"><?= $group->tglr_user_oauth_uid ?></td>										
										<td class="tv_id" style="display:none;"><?= $team_venue->tv_id ?></td>		
										<td class="venue_name" style="display:none;"><?= $team_venue->tv_name ?></td>		
										<td class="date" style="display:none;"><?= date('l m/d/y', strtotime(rtrim($tv_gla->tgla_day, 's'))) ?></td>		
										
										<td class="user_name"><div class="name_<?= $group->tglr_user_oauth_uid ?>"></div></td>
										<td class="user_pic"><div class="pic_square_<?= $group->tglr_user_oauth_uid ?>"></div></td>
										<td class="venue"><?= $team_venue->tv_name ?></td>
										<td class="promoter"> - </td>
										<td><?= date('l m/d/y', strtotime(rtrim($tv_gla->tgla_day, 's'))) ?></td>
										<td class="entourage" style="white-space:nowrap; width:244px;">
											
											<?php if(!count($group->entourage)): ?>
												
												<p>No Entourage</p>
												
											<?php else: ?>
												
											<table class="normal">
												<thead>
													<tr>
														<th>Name</th>
														<th>Picture</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($group->entourage as $key2 => $ent_user): ?>
														<tr>
															<td><div class="name_<?= $ent_user->tglre_oauth_uid ?>"></div></td>
															<td><div class="pic_square_<?= $ent_user->tglre_oauth_uid ?>"></div></td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											
											<?php endif; ?>
										</td>
										<td class="request_msg">
											<?= (strlen($group->tglr_request_msg)) ? $group->tglr_request_msg : ' - ' ?>
										</td>
										<td>
											<?php if($group->tglr_table_request == '1' || $group->tglr_table_request == 1): ?>
												<span style="color:green;">Yes</span>
											<?php else: ?>
												<span style="color:red;">No</span>
											<?php endif; ?>
										</td>
										<td>$<?= $group->table_min_spend ?></td>
										<td style="white-space:nowrap;"><?= preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $group->u_phone_number) ?></td>
										<td><span class="app_dec_action">Respond</span></td>
									</tr>
									
								<?php endif; ?>
								
							<?php endforeach; ?>
							
						<?php endif; ?>

					<?php endforeach; ?>
				<?php endforeach; ?>
				
				
				<?php foreach($statistics->promoters as $promoter): ?>
					<?php foreach($promoter->weekly_guest_lists as $wgl): ?>
						
						<?php if($wgl->groups): ?>
							
							<?php foreach($wgl->groups as $group): ?>
								
								<?php if($group->pglr_approved == '1' && $group->pglr_table_request == '1'): ?>
									<?php $no_groups = false; ?>
									<tr style="display:none;">
										
										<td class="request_type" style="display:none;">promoter</td>
										<td class="table" style="display:none;"><?= $group->pglr_table_request ?></td>
										<td class="pglr_id" style="display:none;"><?= $group->id ?></td>
										<td class="pglr_head_user" style="display:none;"><?= $group->head_user ?></td>								
										<td class="tv_id" style="display:none;"><?= $wgl->tv_id ?></td>		
										<td class="venue_name" style="display:none;"><?= $wgl->tv_name ?></td>		
										<td class="date" style="display:none;"><?= date('l m/d/y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></td>							
										
										<td class="user_name"><div class="name_<?= $group->head_user ?>"></div></td>
										<td class="user_pic"><div class="pic_square_<?= $group->head_user ?>"></div></td>
										<td class="venue"><?= $wgl->tv_name ?></td>
										<td class="promoter"><?= $promoter->u_full_name ?></td>
										<td><?= date('l m/d/y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></td>
										<td class="entourage" style="width:216px;">
											<?php if(!count($group->entourage_users)): ?>
												
												<p>No Entourage</p>
												
											<?php else: ?>
												
											<table class="normal">
												<thead>
													<tr>
														<th>Name</th>
														<th>Picture</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($group->entourage_users as $key2 => $ent_user): ?>
														<tr>
															<td><div class="name_<?= $ent_user ?>"></div></td>
															<td><div class="pic_square_<?= $ent_user ?>"></div></td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											
											<?php endif; ?>
										</td>
										<td class="request_msg">
											<?= (strlen($group->pglr_request_msg)) ? $group->pglr_request_msg : ' - ' ?>
										</td>
										<td>
											<?php if($group->pglr_table_request == '1' || $group->pglr_table_request == 1): ?>
												<span style="color:green;">Yes</span>
											<?php else: ?>
												<span style="color:red;">No</span>
											<?php endif; ?>
										</td>
										<td>$<?= $group->table_min_spend ?></td>
										<td style="white-space:nowrap;"><?= preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $group->u_phone_number) ?></td>
										<td><span class="app_dec_action">Respond</span></td>
									</tr>
									
								<?php endif; ?>
								
							<?php endforeach; ?>
							
						<?php endif; ?>
						
					<?php endforeach; ?>
				<?php endforeach; ?>
				
				<?php if($no_groups): ?>
					
				<tr>
					<td colspan="11">No pending reservation requests</td>
				</tr>
				
				<?php else: ?>
					
				<tr class="loading">
					<td colspan="11"><img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." /></td>
				</tr>
				
				<?php endif; ?>
				
								
			</tbody>
		</table>
	</div>
	
	<hr>





	<div id="team_announcements" class="full_width" style="width:1050px; padding:0;">
		<h3>
			Team Announcements
			<img class="info_icon tooltip" title="Announcements for team members" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
		</h3>
		
		<img id="messages_loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..."  />
		
		<div id="team_announcements_content" style="display:none;height:320px; min-height: 200px; overflow-y: scroll; overflow-x: hidden; padding: 10px;">
			
			
			
			
			
			
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
										
											<div class="announcement_message"><span data-oauth_uid="<?= $an->manager_oauth_uid ?>" class="name_<?= $an->manager_oauth_uid ?>"</span> has updated their notes on <a class="ajaxify" href="<?= $central->manager_admin_link_base . 'clients/' . $message->client_oauth_uid . '/' ?>"><span data-oauth_uid="<?= $message->client_oauth_uid ?>" class="name_<?= $message->client_oauth_uid ?>"></span></a></div>
										
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
								
			<a data-action="create-announcement" id="create_announcement_btn" class="button_link" style="float:right;" href="#">Create Announcement</a>
		</div>

		<div id="announcement_dialog" style="display:none;">
			<p>New Staff Announcement</p>
			<textarea id="manager_announcement_textarea"></textarea>
			
			<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." style="display:none; margin-top:4px; margin-left:auto; margin-right:auto;" />
			<p id="manager_announcement_msg" style="margin-left:auto; margin-right:auto; margin-top:4px;"></p>
		
		</div>
		
	</div>
	
	
	<hr>
	
	
	<div style="clear:both;"></div>
	
	
	
	
	
	
	<div id="team_statistics" class="full_width" style="width:1050px;">
		<h3>
			Team Statistics 
			<img class="info_icon tooltip" title="Basic statistics related to your team." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
		</h3>
		<div style="clearboth"></div>
		
		<div class="tabs team_stats_tabs" style="width:1050px; display:none;">
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
	
	<div style="width:1050px;">
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

	<div style="width:1050px;">
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

