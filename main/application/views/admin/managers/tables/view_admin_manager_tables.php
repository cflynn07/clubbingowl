<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->team_venues = $team_venues;
	$page_obj->init_users = $init_users;
	$page_obj->factor = 0.58;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>



<div id="admin_managers_tables_wrapper">

	
	<div id="vlf_dialog" style="display:none;">
		<div id="vlf_dialog_floor">
		</div>
	</div>
	
	<h1>Table & Guest List Reservations</h1>
		
	<h3>
		Venue Reservations
		<img class="info_icon tooltip" title="Requests organized by venue" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
	</h3>
	
	<div id="tabs" style="display:none;margin-bottom:0px; height: auto !important;">
		<div class="ui-widget-header" style="cursor: default;">
			<span></span>
			
			<div style="display: inline-block; float: right;">
				Select Venue: 
				<select class="venue_select">
					<?php foreach($team_venues as $key => $venue): ?>
						<option data-tv_id="<?= $venue->tv_id ?>" value="<?= $key ?>"><?= $venue->tv_name ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<ul>
			<?php foreach($team_venues as $key => $venue): ?>
				<li><a href="#tabs-<?= $key ?>"><?= $venue->tv_name ?></a></li>
			<?php endforeach; ?>
			</ul>
			
		</div>

		<?php foreach($team_venues as $key => $venue): ?>
		<div class="top_lvl" id="tabs-<?= $key ?>">
			
			<div>
				
				<div class="full_width last">
					
					<div>
						<div style="float:left; display:inline-block;">
							<img 	style="border:1px solid #CCC;" src="<?= $central->s3_uploaded_images_base_url . 'venues/banners/' . $venue->tv_image . '_t.jpg' ?>" alt="<?= $venue->tv_name ?>"/>
							<h3 	style="color:red;"><?= $venue->tv_name ?></h3>
						</div>
						<div style="float:right; display:inline-block; padding-top:5px;">
							<a 		href="<?= $central->manager_admin_link_base . 'settings_venues_edit_floorplan/' . $venue->tv_id . '/' ?>" class="ajaxify button_link btn-action">Edit Floorplan</a>	
						</div>
					</div>
					
					<div style="clear:both;"></div>
					
										
					<div data-tv_id="<?= $venue->tv_id ?>" class="tabs_tables tabs_tables_tv_id_<?= $venue->tv_id ?>">
						
						
						
						
						<div style="display:none;" class="tv_id tv_id_<?= $venue->tv_id ?>"><?= $venue->tv_id ?></div>
						<div class="ui-widget-header">
							
							<span>
								<input type="text" class="table_datepicker" value="<?= date('l F j, Y', time()); ?>" />
								<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
							</span>
							
							<ul>
								<li><a href="#tabs-<?= $venue->tv_id ?>-0">Floorplan</a></li>
								<li><a href="#tabs-<?= $venue->tv_id ?>-1">Table Reservations</a></li>
								<li><a href="#tabs-<?= $venue->tv_id ?>-2">Guest List & Table Reservations</a></li>
							</ul>
							
						</div>
						
						
						
						
						<div data-clear-zone="" id="tabs-<?= $venue->tv_id ?>-0">
													
						</div>
						
						
						
						
						<div id="tabs-<?= $venue->tv_id ?>-1">
							
							<h3>Table Reservations</h3>
							
							
							
							
							
							
							
							<div class="full_width last table_reservations">
								<table class="normal" style="width:100%;">
									<thead>
										<tr>
											<th>Head User</th>
											<th>Picture</th>
											<th>Promoter</th>
											<th>Guest List</th>
											<th>Messages</th>
											<th>Minimum Spend</th>
											<th>Contact Number</th>
											<th>Entourage</th>
											<th>Table</th>
										</tr>
									</thead>
									<tbody>
										
										<?php $table_reservations = false; ?>
										
										<?php foreach($venue->venue_reservations as $vr): ?>
											
											<?php 
												if(!$vr->vlfit_id){ //skip if not a table
													continue;
												}else{
													$table_reservations = true;
												}
											?>
																			
										<tr>
											<td class="head_user_name"><span class="name_<?= isset($vr->tglr_user_oauth_uid) ? $vr->tglr_user_oauth_uid : $vr->pglr_user_oauth_uid ?>"></span></td>
											<td class="head_user_picture"><div class="pic_square_<?= isset($vr->tglr_user_oauth_uid) ? $vr->tglr_user_oauth_uid : $vr->pglr_user_oauth_uid ?>"></div></td>
											
											<td class="promoter"><?= (isset($vr->up_users_oauth_uid) ? '<span class="name_' . $vr->up_users_oauth_uid . '"></span>' : ' - ') ?></td>
											<td class="guest_list"><?= (isset($vr->tgla_name)) ? $vr->tgla_name : $vr->pgla_name ?></td>
											
											<td class="user_messages">
												<table style="width:152px; text-wrap: unrestricted;" class="user_messages">
													<tbody>
														
														<tr>
															<td class="message_header">Request Message:</td>
														</tr>
														
														<tr>
															<td class="request_msg"><?= isset($vr->tglr_request_msg) ? (($vr->tglr_request_msg) ? $vr->tglr_request_msg : ' - ') : (($vr->pglr_request_msg) ? $vr->pglr_request_msg : ' - ' ) ?></td>
														</tr>
														
														<tr>
															<td class="message_header">Response Message:</td>
														</tr>
														
														<tr>
															<td class="response_message"><?= isset($vr->tglr_response_msg) ? (($vr->tglr_response_msg) ? $vr->tglr_response_msg : ' - ') : (($vr->pglr_response_msg) ? $vr->pglr_response_msg : ' - ') ?></td>
														</tr>
														
														<tr>
															<td class="message_header">Host Notes:</td>
														</tr>
														
														<tr style="max-width:122px;">
															<td style="max-width:122px;" class="host_notes"><?= isset($vr->tglr_host_message) ? (($vr->tglr_host_message) ? $vr->tglr_host_message : ' - ') : (($vr->pglr_host_message) ? $vr->pglr_host_message : ' - ') ?></td>
														</tr>
														
													</tbody>
												</table>
											</td>
											
											<td class="min_spend">$500 USD</td>
											<td class="phone_number">1-(774)-573-4580</td>
											<td class="entourage">
												<?php if(!$vr->entourage): ?>
															
													<p>No Entourage</p>			
															
												<?php else: ?>
												
													<table class="entourage">
														<thead>
															<tr>
																<th>Name</th>
																<th>Picture</th>
															</tr>
														</thead>
														<tbody>
															<?php foreach($vr->entourage as $ent): ?>
															<tr>
																<td><span class="name_<?= $ent ?>"></span></td>
																<td><div class="pic_square_<?= $ent ?>"></div></td>
															</tr>
															<?php endforeach; ?>
														</tbody>
													</table>	
												
												<?php endif; ?>
											
											</td>
											<td class="table">
												<div style="width:100px; height:100px; background:#000;"></div>
											</td>
										</tr>				
										
										<?php endforeach; ?>
										
										<?php if(!$table_reservations): ?>
										<tr>
											<td colspan="9">No table reservations</td>
										</tr>
										<?php endif; ?>
										
										
										
									</tbody>
								</table>
							</div>
							
							
							
							
							
							
							
							
							
						</div>
						
						<div id="tabs-<?= $venue->tv_id ?>-2">
							
							<h3>All Guest List & Table Reservations</h3>
							
							<div class="full_width last all_reservations">
								<table class="normal" style="width:100%;">
									<thead>
										<tr>
											<th>Head User</th>
											<th>Picture</th>
											<th>Promoter</th>
											<th>Guest List</th>
											<th>Messages</th>
											<th>Entourage</th>
										</tr>
									</thead>
									<tbody>
										
										<?php if(!$venue->venue_reservations): ?>
											<tr>
												<td colspan="4">No reservations</td>
											</tr>
										<?php endif; ?>
										
										<?php foreach($venue->venue_reservations as $vr): ?>
																			
										<tr>
											<td class="head_user_name"><span class="name_<?= isset($vr->tglr_user_oauth_uid) ? $vr->tglr_user_oauth_uid : $vr->pglr_user_oauth_uid ?>"></span></td>
											<td class="head_user_picture"><div class="pic_square_<?= isset($vr->tglr_user_oauth_uid) ? $vr->tglr_user_oauth_uid : $vr->pglr_user_oauth_uid ?>"></div></td>
											
											<td class="promoter"><?= (isset($vr->up_users_oauth_uid) ? '<span class="name_' . $vr->up_users_oauth_uid . '"></span>' : ' - ') ?></td>
											<td class="guest_list"><?= (isset($vr->tgla_name)) ? $vr->tgla_name : $vr->pgla_name ?></td>
											
											<td class="user_messages">
												<table style="width:152px; text-wrap: unrestricted;" class="user_messages">
													<tbody>
														
														<tr>
															<td class="message_header">Request Message:</td>
														</tr>
														
														<tr>
															<td class="request_msg"><?= isset($vr->tglr_request_msg) ? (($vr->tglr_request_msg) ? $vr->tglr_request_msg : ' - ') : (($vr->pglr_request_msg) ? $vr->pglr_request_msg : ' - ' ) ?></td>
														</tr>
														
														<tr>
															<td class="message_header">Response Message:</td>
														</tr>
														
														<tr>
															<td class="response_message"><?= isset($vr->tglr_response_msg) ? (($vr->tglr_response_msg) ? $vr->tglr_response_msg : ' - ') : (($vr->pglr_response_msg) ? $vr->pglr_response_msg : ' - ') ?></td>
														</tr>
														
														<tr>
															<td class="message_header">Host Notes:</td>
														</tr>
														
														<tr style="max-width:122px;">
															<td style="max-width:122px;" class="host_notes"><?= isset($vr->tglr_host_message) ? (($vr->tglr_host_message) ? $vr->tglr_host_message : ' - ') : (($vr->pglr_host_message) ? $vr->pglr_host_message : ' - ') ?></td>
														</tr>
														
													</tbody>
												</table>
											</td>
											
											<td class="entourage">
												<?php if(!$vr->entourage): ?>
															
													<p>No Entourage</p>			
															
												<?php else: ?>
												
													<table class="entourage">
														<thead>
															<tr>
																<th>Name</th>
																<th>Picture</th>
															</tr>
														</thead>
														<tbody>
															<?php foreach($vr->entourage as $ent): ?>
															<tr>
																<td><span class="name_<?= $ent ?>"></span></td>
																<td><div class="pic_square_<?= $ent ?>"></div></td>
															</tr>
															<?php endforeach; ?>
														</tbody>
													</table>	
												
												<?php endif; ?>
											
											</td>
										</tr>				
										
										<?php endforeach; ?>
										
									</tbody>
								</table>
							</div>
							
							
						</div>
						
					</div>
					
					<div style="clear:both;"></div>
					
				</div>
				
				<div style="clear:both;"></div>
				
			</div>
			
			<div style="clear:both;"></div>
			
		</div>
		<?php endforeach; ?>
		
		<div style="clear:both;"></div>
	</div>
	
	<div style="clear:both;"></div>
	<br>
	<hr>
	
	<h3>
		All Upcoming Reservations
		<img class="info_icon tooltip" title="All upcoming table and guest list reservations" src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
	</h3>
	
	<div class="full_width last" style="margin-bottom:40px;">
				
		<table class="normal" style="width:100%;">
			<thead>
				<tr>
					<th>Head User</th>
					<th>Picture</th>
					<th>Venue</th>
					<th>Promoter</th>
					<th>Guest List</th>
					<th>Entourage</th>
					<th>Date</th>
					<th>Table</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($team_venues as $tv): ?>
					<?php foreach($tv->venue_all_upcoming_reservations as $tvaur): ?>
						<tr>
							<td class="head_user_name"><span class="name_<?= (isset($tvaur->pglr_user_oauth_uid)) ? $tvaur->pglr_user_oauth_uid : $tvaur->tglr_user_oauth_uid ?>"></span></td>
							<td class="head_user_picture"><div class="pic_square_<?= (isset($tvaur->pglr_user_oauth_uid)) ? $tvaur->pglr_user_oauth_uid : $tvaur->tglr_user_oauth_uid ?>"></div></td>
							<td class="venue"><?= $tv->tv_name ?></td>
							<td class="promoter"><?= (isset($tvaur->up_users_oauth_uid)) ? '<span class="name_' . $tvaur->up_users_oauth_uid . '"></span>' : ' - ' ?></td>
							<td class="guest_list"><?= (isset($tvaur->pgla_name)) ? $tvaur->pgla_name : $tvaur->tgla_name ?></td>
							<td class="entourage">
								<?php if($tvaur->entourage): ?>
								<table>
									<thead>
										<tr>
											<th>Name</th>
											<th>Picture</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($tvaur->entourage as $ent): ?>
										<tr>
											<td><span class="name_<?= $ent ?>"></span></td>
											<td><div class="pic_square_<?= $ent ?>"></div></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
								<?php else: ?>
									<p>No Entourage</p>
								<?php endif; ?>
							</td>
							<td class="date"><?= (isset($tvaur->pgla_day)) ? date('l F j, Y', strtotime($tvaur->pgla_day)) : date('l F j, Y', strtotime($tvaur->tgla_day)) ?></td>
							<td class="table"><?= ($tvaur->vlfit_id) ? '<div style="width:100px; height:100px; background:#000;"></div>' : '<span style="color:red;">No</span>' ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
			
	</div>

</div>

<?php else: ?>
	
	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>
	
	<h1>Venue Tables</h1>
	
	<p>When <b><?= $team->team_name ?></b> is approved this page will detail your venue floorplans, table reservations, and allow you to assign parties to tables on specific nights.</p>
	
<?php endif; ?>