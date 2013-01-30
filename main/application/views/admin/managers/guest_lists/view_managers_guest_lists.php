<?php
	$page_obj 				= new stdClass;
	$page_obj->team_venues 	= $team_venues;
	$page_obj->users 		= $users;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>










<div style="display:none;" id="manual_add_modal"></div>


<div id="guest_list_content" class="tabs" style="display:block; width:980px;">
	
	<div class="ui-widget-header">
		<span>Team Guest Lists</span>
	</div><br>
	
	
	
	
	<div id="left_menu" class="one_fourth">
		
		<div style="width:100%; text-align:center; margin-bottom:20px; border-bottom:0;">
			<a href="<?= $central->manager_admin_link_base . 'settings_guest_lists/' ?>" class="ajaxify button_link btn-action">Edit Guest Lists</a>	
		</div>
		
		<div style="width:100%; text-align:center; margin-bottom:10px; border-bottom:1px solid #000;">
			<img id="left_menu_gl_img" 		src="" alt="" /><br/>
			<img id="left_menu_venue_img" 	src="" alt="" />
		</div>
				
		<ul class="sitemap" style="cursor: default; text-decoration:none !important;"></ul>

	</div>
	
	
	
	
	
	<div id="list_status" class="three_fourth last"></div>
	
	<br/><br/><br/>
	
	<div class="one_fourth"></div>
	<div id="lists_container" class="three_fourth last"></div>
	
	<div style="clear:both"></div>
	
	
	
</div>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php if(false): ?>
							
													
							<h1>Team Guest Lists</h1>
							
							<?php if($team_venues): ?>
								<div id="main_loading_indicator">
									<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
								</div>
								
								<div id="tabs" style="width: 1050px; display:none;">
									
									<div class="ui-widget-header">
										<span>Team Guest Lists</span>
										
										<div class="div_venue_select" style="display: inline-block; float: right;">
											Select Venue: 
											<select class="venue_select">
												<?php foreach($team_venues as $key => $venue): ?>
													<?php
														//find total count of guest list reservations for all guest lists at venue
														$res_count = 0;
														foreach($venue->tv_gla as $gla){
															($gla->current_list) ? $res_count += count($gla->current_list->groups) : 0;
														}
													?>
													<option class="<?= $venue->tv_id ?>" value="<?= $key ?>">
														<?= $venue->tv_name ?> (<?= $res_count ?>)
													</option>
												<?php endforeach; ?>
											</select>
											<?php foreach($team_venues as $key => $venue): ?>
												<div class="<?= $venue->tv_id ?> hidden" style="display:none;">
													<div class="index"><?= $key ?></div>
													<div class="name"><?= $venue->tv_name ?></div>
													<div class="count"><?= $res_count ?></div>
												</div>
											<?php endforeach; ?>
										</div>
										
										<ul>
										<?php foreach($team_venues as $key => $venue): ?>
											<?php
												//find total count of guest list reservations for all guest lists at venue
												$res_count = 0;
												foreach($venue->tv_gla as $gla){
													($gla->current_list) ? $res_count += count($gla->current_list->groups) : 0;
												}
											?>
											<li><a href="#tabs-<?= $key ?>"><?= $venue->tv_name ?> (<span class="team_gl_groups_count"><?= $res_count ?></span>)</a></li>
										<?php endforeach; ?>
										</ul>
									</div>
									
									<?php foreach($team_venues as $key => $venue): ?>
										<div id="tabs-<?= $key ?>">
												
												<div class="guest_list_content">
													
													<div class="one_fourth">
														
														<h3><?= $venue->tv_name ?></h3>
																						
														<?php if($venue->tv_image): ?>
														<img src="<?= $central->s3_uploaded_images_base_url . 'venues/banners/' . $venue->tv_image . '_t.jpg' ?>" alt="" style="width:220px;"/>
														<?php endif; ?>
														
														<br><br>
																			
														<ul class="sitemap ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="cursor: default;">
															<?php foreach(array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays') as $weekday): 
																	$day_displayed = false;
															?>
																<?php foreach($venue->tv_gla as $gla): ?>
																	
																	<?php if($gla->tgla_day == $weekday): ?>
																	
																		<?php if(!$day_displayed): ?>
																			<span style="color: red; font-weight: bold;"><?= ucfirst($weekday) ?></span><br>
																			<?php $day_displayed = true; ?>
																		<?php endif; ?>
																	
																			<li class="<?= $gla->tgla_id ?>" style="margin-left:15px;text-decoration:none;"><span style="text-decoration:underline;"><?= $gla->tgla_name ?></span> (<span class="count_tgla_id"><?= ($gla->current_list) ? (count($gla->current_list->groups)) : 0 ?></span>)<span class="tgla_id" style="display:none"><?= $gla->tgla_id ?></span></li><br>
																	
																	<?php endif; ?>
													
																<?php endforeach; ?>
															<?php endforeach; ?>
														</ul>
														
														<br><hr><br>
																						
														<div class="datepicker"></div>
														
														<br>
														
													</div>
													
													<div id="lists_container" class="three_fourth last">
									
														<?php foreach($venue->tv_gla as $gla): ?>
															
															
															
															<div class="gl_status gl_status_<?= $gla->tgla_id ?>" style="display:none;">
																<div class="ui-widget">
																	<div class="ui-widget-header">
																		<span>"<?= $gla->tgla_name ?>" Status</span>
																		<span style="float:right;color:grey;">Last Updated: Monday April 12, 2012</span>
																	</div>
																	
																	<div class="ui-widget-content" style="padding:5px;padding-left:20px;">
																		<span style="color:blue;text-decoration:underline;">Line is gonna be super long! Make sure to get here by 11! Get wild.</span>
																	</div>
																</div>
															
																<hr>
																<div style="clear:both"></div>
															</div>
															
															
															
															<div class="list tabs" id="tgla_<?= $gla->tgla_id ?>" style="display:none">
															<div class="ui-widget-header">
																<span>"<?= $gla->tgla_name ?>" @ <span style="font-weight: bold;"><?= $gla->tgla_name ?></span></span>
																<span style="float:right;"><?= date('l F j, Y', strtotime(rtrim($gla->tgla_day, 's'))) ?></span>
															</div>			
																<table class="normal tablesorter guestlists" style="width: 100%">
																	<thead>
																		<tr>
																			<th>Guest</th>
																			<th>Picture</th>
																			<th>Messages</th>
																			<th>Table</th>
																			<th>Status</th>
																			<th>Entourage</th>
																		</tr>
																	</thead>
																	<tbody id="tgla_id_<?= $gla->tgla_id ?>">
																		<tr style="display:none;"><td class="tgla_id"><?= $gla->tgla_id ?></td></tr>
																		<tr style="display:none;"><td class="tv_id"><?= $venue->tv_id ?></td></tr>
																		<tr style="display:none;"><td class="venue_name"><?= $venue->tv_name ?></td></tr>
																		<tr style="display:none;"><td class="date"><?= date('l F j, Y', strtotime(rtrim($gla->tgla_day, 's'))) ?></td></tr>
																		<?php if($gla->current_list): ?>
																			<?php foreach($gla->current_list->groups as $key1 => $group): ?>
																			<tr>
																				
																				<td class="request_type" style="display:none;">manager</td>
																				<td class="table" style="display:none;"><?= $group->tglr_table_request ?></td>
																				<td class="tglr_id" style="display:none;"><?= $group->tglr_id ?></td>
																				<td class="tglr_head_user" style="display:none;"><?= $group->tglr_user_oauth_uid ?></td>										
																				<td class="tv_id" style="display:none;"><?= $venue->tv_id ?></td>	
																				<td class="venue_name" style="display:none;"><?= $venue->tv_name ?></td>		
																				<td class="date" style="display:none;"><?= date('l m/d/y', strtotime(rtrim($gla->tgla_day, 's'))) ?></td>		
																
																				<?php //fields not shown on GL page but shown on dash page ?>
																				<td class="venue" style="display:none"><?= $venue->tv_name ?></td>
																				<td class="promoter" style="display:none"> - </td>
																				<td class="min_spend" style="display:none">$500</td>
																
																				<td class="user_name"><div class="name_<?= $group->tglr_user_oauth_uid ?>"></div></td>
																				<td class="user_pic"><div class="pic_square_<?= $group->tglr_user_oauth_uid ?>"></div></td>
																				<td>
																					<table class="user_messages" style="width:152px; text-wrap: unrestricted;">
																						<tr><td class="message_header">Request Message:</td></tr>
																						<tr><td class="request_msg"><?= (strlen($group->tglr_request_msg)) ? $group->tglr_request_msg : ' - ' ?></td></tr>
																						<tr><td class="message_header">Response Message:</td></tr>
																						<tr><td class="response_message"><?= (strlen($group->tglr_response_msg)) ? $group->tglr_response_msg : ' - ' ?></td></tr>
																						<tr><td class="message_header">Host Notes:</td></tr>
																						<tr style="max-width:122px;">
																							<td class="host_notes" style="max-width:122px;">
																								<div class="edit" style="display:none;">
																									<textarea></textarea>
																									<br>
																									<span class="message_remaining"></span>
																								</div>
																								<span class="original">
																									<?= (strlen($group->tglr_host_message)) ? $group->tglr_host_message : '<span style="font-weight: bold;">Edit Message</span>' ?>
																								</span>
																								<img class="message_loading_indicator" style="display:none;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
																							</td>
																						</tr>
																				</table>
																				</td>
																				<td><?= ($group->tglr_table_request == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
																				<td class="actions">
																					
																					<?php if($group->tglr_approved == '1'): ?>
																						<span style="color: green;">Approved</span>
																					<?php elseif($group->tglr_approved == '-1'): ?>
																						<span style="color: red;">Declined</span>
																					<?php else: ?>
																						<span class="app_dec_action" style="font-weight: bold;  text-decoration: underline; cursor: pointer; color: blue;">Requested</span>
																					<?php endif; ?>
																					
																				</td>
																				<td class="entourage" style="white-space:nowrap; width:244px;">
																					<?php if(!count($group->entourage)): ?>
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
																							<?php foreach($group->entourage as $key2 => $ent_user): ?>
																								<tr <?= ($key2 % 2) ? 'class="odd"' : '' ?>>
																									<td><div class="name_<?= $ent_user->tglre_oauth_uid ?>"></div></td>
																									<td><div class="pic_square_<?= $ent_user->tglre_oauth_uid ?>"></div></td>
																								</tr>
																							<?php endforeach; ?>
																						</tbody>
																					</table>
																					<?php endif; ?>
																				</td>
																			</tr>
																			<?php endforeach; ?>
																		<?php endif; ?>
																		<?php if(!$gla->current_list): ?>
																			<tr class="no_reservations"><td colspan=7>This weeks guest list does not have any reservations yet.</td></tr>
																		<?php endif; ?>
																		
																			<tr>
																				<div class="pgla_id" style="display:none;"><?= $gla->tgla_id ?></div>
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
													
												</div>
												
											<div style="clear:both;"></div>
										</div>
									<?php endforeach; ?>
								</div>
							
							<?php else: ?>
								
								<p>You do not have any venues</p>
								
							<?php endif; ?>

			

					</div>
										
<?php endif; ?>