<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->users = $users;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

	<h1>Promoters' Clients</h1>
	
	<?php if($promoters): ?>
		<div id="main_loading_indicator">
			<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		</div>
		
		<div id="tabs" style="width:980px; display:none;">
			
			<div class="ui-widget-header">
				<span>Promoter Clients</span>
				
				<div style="display: inline-block; float: right;">
					Select Promoter: 
					<select class="promoter_select">
						<?php foreach($promoters as $key => $promoter): ?>
							<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
							
							<option value="<?= $key ?>"><?= $promoter->u_full_name ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<ul>
				<?php foreach($promoters as $key => $promoter): ?>
					
					<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
					
					<li><a href="#tabs-<?= $key ?>"><?= $promoter->u_full_name ?></a></li>
				<?php endforeach; ?>

				</ul>
			</div>
			
			<?php foreach($promoters as $key => $promoter): ?>
				
				<?php if($promoter->up_completed_setup != '1'){ continue; } ?>
				
				<div id="tabs-<?= $key ?>">
					<?php if($promoter->up_banned == '1'): ?>
						
						<p>This promoter has been banned</p>
						
					<?php else: ?>
											
						<div class="client_list_content">
							
							<div class="one_fourth">
								
								<h3><?= $promoter->u_full_name ?></h3>
								
								<img style="left:-5px;position:relative;" src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $promoter->up_profile_image . '_p.jpg' ?>" alt="" />
								
							</div>
							
							<div class="three_fourth last column lists_container">
								
								<h3>&nbsp;</h3>
								
								<table style="width:100%;" class="clients_list normal">
									
									
									
									
									
									<thead>
										<tr>
											<th>Name</th>
											<th>Phone Number</th>
											<th>Email</th>
											<th>Facebook</th>
											<th>Email Opt-Out</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($promoter->clients as $client): ?>
										<tr>
											
											<td>
												
												<a class="ajaxify" href="/admin/managers/clients/<?= $client->u_oauth_uid ?>/">
													<?= $client->u_full_name ?>
												</a>
												
											</td>
											<td>
												<?= $client->u_phone_number ?>
											</td>
											<td>
												<?= $client->u_email ?>
											</td>
											<td>
												<a target="_new" href="http://www.facebook.com/<?= $client->u_oauth_uid ?>">Facebook</a>
											</td>
											<td>
												<?php if($client->u_opt_out_email == '1'): ?>
													<span style="color:red;">Yes</span>
												<?php else: ?>
													<span style="color:black;">No</span>
												<?php endif; ?>
											</td>
											
										</tr>
										<?php endforeach; ?>
									</tbody>
									
									
									
									
									
									
								</table>
	
							</div>
							
						</div>
						
					<?php endif; ?>
					<div style="clear:both;"></div>
				</div>
			<?php endforeach; ?>
				
				
				
				
				
		</div>
	
	<?php else: ?>
		
		<p>You do not have any active promoters</p>
		
	<?php endif; ?>
	
<?php else: ?>

	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>

	<h1>Promoters' Clients</h1>
	
	<p>When your team is live this page will detail each of your promoters 'clients' - ClubbingOwl users that have requested to join a promoter's guest list or event. You can add promoters to your team once your are live.</p>

<?php endif; ?>