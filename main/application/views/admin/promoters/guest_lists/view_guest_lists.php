<?php 
	$page_obj = new stdClass;
	$page_obj->users = json_decode($users);
	$page_obj->weekly_guest_lists = $weekly_guest_lists;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<?php if(false): ?>
<div id="loading_indicator">
	<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
</div>
<?php endif; ?>



<div id="guest_list_content" class="tabs" style="display:block; width:1050px">
	
	<div class="ui-widget-header">
		<span>Promoter Guest Lists</span>
	</div><br>
	
	<div id="left_menu" class="one_fourth">
		
			<div style="width:100%; text-align:center; margin-bottom:10px; border-bottom:1px solid #000;">
				<img id="left_menu_gl_img" 		src="" alt="" /><br/>
				<img id="left_menu_venue_img" 	src="" alt="" />
			</div>
			
		
		
			<ul class="sitemap" style="cursor: default; text-decoration:none !important;">
				
				<?php if(false): ?>
				
										<?php foreach(array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays') as $weekday): 
												$day_displayed = false;
										?>
										
											<?php foreach($weekly_guest_lists as $wgl): ?>
												
												<?php if($wgl->pgla_day == $weekday): ?>
													
													<?php if(!$day_displayed): ?>
														<span style="color: red; font-weight: bold;"><?= ucfirst($weekday) ?></span><br>
														<?php $day_displayed = true; ?>
													<?php endif; ?>
													
													<li data-plga_id="<?= $wgl->pgla_id ?>" class="<?= $wgl->pgla_id ?>" style="margin-left:15px;text-decoration:none;"><span style="text-decoration:underline;"><?= $wgl->pgla_name ?></span> (<span class="wgl_groups_count"><?= count($wgl->groups) ?></span>)<span class="pgla_id" style="display:none"><?= $wgl->pgla_id ?></span></li><br>
											
												<?php endif; ?>
												
											<?php endforeach; ?>
											
										<?php endforeach; ?>
				
				<?php endif; ?>
				
			</ul>
	
			
			<?php if(false): ?>
			<div class="datepicker"></div>
			<?php endif; ?>		
		
	</div>

	<div id="lists_container" class="three_fourth last">
		
		
		
		
		
		<?php if(false): ?>
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
		
		




		
		
		<?php endif; ?>
	</div>
	
	<div style="clear:both"></div>
	
</div>

