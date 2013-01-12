<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->users = $users;
	$page_obj->promoters = $promoters;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


	<h1>Promoters' Guest Lists</h1>
	
	
	
	
	
	
	
	
	
	
	
	<?php if($promoters): ?>
		
		<div id="admin_managers_promoters_guest_list_wrapper">	
				<div id="main_loading_indicator">
					<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
				</div>
				
				<div id="tabs" style="width: 980px; display:none;">
					
					<div class="ui-widget-header" style="cursor: default;">
						<span>Promoter Guest Lists</span>
						
						<div style="display: inline-block; float: right;">
							Select Promoter: 
							<select class="promoter_select">
								<?php foreach($promoters as $key => $promoter): ?>
									
									<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
									
									<?php
										$count = 0;
										foreach($promoter->weekly_guest_lists as $wgl){
											$count += count($wgl->groups);
										}
									?>
									<option value="<?= $key ?>"><?= $promoter->u_full_name ?> (<?= $count ?>)</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<ul>
						<?php foreach($promoters as $key => $promoter): ?>
							
							<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
							
							<?php
								$count = 0;
								foreach($promoter->weekly_guest_lists as $wgl){
									$count += count($wgl->groups);
								}
							?>
							<li><a href="#tabs-<?= $key ?>"><?= $promoter->u_full_name ?> (<?= $count ?>)</a></li>
						<?php endforeach; ?>
						</ul>
					</div>
					
					<?php foreach($promoters as $key => $promoter): ?>
						
						<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
						
						<div id="tabs-<?= $key ?>">
							<?php if($promoter->up_banned == '1'): ?>
								
								<p>This promoter has been banned</p>
								
							<?php else: ?>
													
								<div class="guest_list_content">
									
									<div class="one_fourth">
										
										<h3><?= $promoter->u_full_name ?></h3>
										
										<img style="left:-5px;position:relative;" src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $promoter->up_profile_image . '_p.jpg' ?>" alt="" />
										
										<br><br>
												
										<ul class="sitemap ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="cursor: default;">
											<?php foreach(array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays') as $weekday): 
													$day_displayed = false;
											?>
												<?php foreach($promoter->weekly_guest_lists as $wgl): ?>
													
													<?php if($wgl->pgla_day == $weekday): ?>
														
														<?php if(!$day_displayed): ?>
															<span style="color: red; font-weight: bold;"><?= ucfirst($weekday) ?></span><br>
															<?php $day_displayed = true; ?>
														<?php endif; ?>
														
													<li data-pgla_id="<?= $wgl->pgla_id ?>" style="margin-left:15px; text-decoration:none;"><span style="text-decoration:underline"><?= $wgl->pgla_name ?></span> (<?= count($wgl->groups) ?>)<span class="pgla_id" style="display:none"><?= $wgl->pgla_id ?></span></li><br>
													
													<?php endif; ?>
												
												<?php endforeach; ?>
												
											<?php endforeach; ?>
										</ul>
										
										
										
										
										<?php if(false): ?>		
											<br><hr><br>				
											<div class="datepicker"></div>
										<?php endif; ?>
										
										
										<br>
										
									</div>
									
									<div class="three_fourth last column lists_container">
										
										<?php foreach($promoter->weekly_guest_lists as $wgl): ?>
											<div class="list tabs" id="pgla_<?= $wgl->pgla_id ?>" style="display:none">
											<div class="ui-widget-header">
												<span>"<?= $wgl->pgla_name ?>" @ <span style="font-weight: bold;"><?= $wgl->tv_name ?></span></span>
												<span style="float:right;"><?= date('l F j, Y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></span>
											</div>			
												<table class="normal tablesorter guestlists" style="width: 100%;">
													<thead>
														<tr>
															<th>Head User</th>
															<th>Messages</th>
															<th>Table</th>
															<th>Status</th>
															<th>Entourage</th>
														</tr>
													</thead>
													<tbody>
														<?php foreach($wgl->groups as $key1 => $group): ?>
														<tr <?= ($key1 % 2) ? 'class="odd"' : '' ?> >
															<td class="pglr_id" style="display:none"><?= $group->id ?></td>
															<td class="pglr_head_user" style="display:none"><?= $group->head_user ?></td>
															<td>
																
																<?php if($group->head_user === NULL): ?>
																	
																	<img src="<?= $central->admin_assets . 'images/unknown_user.jpeg' ?>" />
																	<div><?= $group->pglr_supplied_name ?></div>
																	
																<?php else: ?>
																	
																	<img src="https://graph.facebook.com/<?= $group->head_user ?>/picture?type=square" />
																	<div data-name="<?= $group->head_user ?>" data-oauth_uid="<?= $group->head_user ?>"  class="name_<?= $group->head_user ?>"></div>
															
																<?php endif; ?>
															
															
															</td>
															
															<td>
																<table class="user_messages" style="width:152px; text-wrap: unrestricted;">
																	<tr><td class="message_header">Request Message:</td></tr>
																	<tr><td><?= (strlen($group->pglr_request_msg)) ? $group->pglr_request_msg : ' - ' ?></td></tr>
																	<tr><td class="message_header">Response Message:</td></tr>
																	<tr><td class="response_message"><?= (strlen($group->pglr_response_msg)) ? $group->pglr_response_msg : ' - ' ?></td></tr>
																	<tr><td class="message_header">Host Notes:</td></tr>
																	<tr style="max-width:122px;">
																		<td>
																			<?= (strlen($group->pglr_host_message) > 0) ? $group->pglr_host_message : ' - ' ?>
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
																	<span class="app_dec_action" style="font-weight: bold;">Requested</span>
																<?php endif; ?>
																
																
																<?php if($group->pglr_manual_add == '1'): ?>
																	
																	<br/><span>Manually Added</span>
																	
																<?php endif; ?>
																
																
																
															</td>
															<td style="white-space: nowrap;">
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
																				
																				<?php if($ent_user->pglre_oauth_uid === NULL): ?>
																					
																					<td><div class=""><?= $ent_user->pglre_supplied_name ?></div></td>
																					<td><div class=""><img src="<?= $central->admin_assets . 'images/unknown_user.jpeg' ?>" /></div></td>
																					
																				<?php else: ?>
																					
																					<td><div data-oauth_uid="<?= $ent_user->pglre_oauth_uid ?>" data-name="<?= $ent_user->pglre_oauth_uid ?>" class="name_<?= $ent_user->pglre_oauth_uid ?>"></div></td>
																					<td><div class=""><img src="https://graph.facebook.com/<?= $ent_user->pglre_oauth_uid ?>/picture?type=square" /></div></td>
																					
																				<?php endif; ?>
																				
																			</tr>
																			
																			
																		<?php endforeach; ?>
																	</tbody>
																</table>
																<?php endif; ?>
															</td>
														</tr>
														<?php endforeach; ?>
														<?php if(!$wgl->groups): ?>
															<td colspan=7>This weeks guest list does not have any reservations yet.</td>
														<?php endif; ?>
													</tbody>
												</table>
														
											</div>
										<?php endforeach; ?>
									</div>
									
								</div>
								
							<?php endif; ?>
							<div style="clear:both;"></div>
						</div>
					<?php endforeach; ?>
				</div>
		</div>

	
	
	
	<?php else: ?>
		
		<p>You do not have any active promoters</p>
		
	<?php endif; ?>
	
	
	<br/><br/><br/>
	
	
<?php else: ?>
	
	
	
	
	
	
	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>
	
	<h1>Promoters' Guest Lists</h1>
	
	<p>When your team is live this page will detail each of your promoters guest lists and all users that are currently on each of these lists. You can add promoters to your team once your are live.</p>
	
	
	
	
<?php endif; ?>