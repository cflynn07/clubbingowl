<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->promoters = $promoters;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>
	
	<h1>Promoters' Statistics</h1>
	
	<?php if($promoters): ?>
		
		<div id="main_loading_indicator">
			<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		</div>
		
		<div id="tabs" style="width: 1050px; display: none;">
			
			<div class="ui-widget-header">
				<span>Promoter Statistics</span>
				
				<div style="display: inline-block; float: right;">
					Select Promoter: 
					<select class="promoter_select">
						<?php foreach($promoters as $key => $promoter): ?>
							<option value="<?= $key ?>"><?= $promoter->u_full_name ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<ul>
				<?php foreach($promoters as $key => $promoter): ?>
					<li><a href="#tabs-<?= $key ?>"><?= $promoter->u_full_name ?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<?php foreach($promoters as $key => $promoter): ?>
				<div id="tabs-<?= $key ?>">
					<?php if($promoter->up_banned == '1'): ?>
						
						<p>This promoter has been banned from VibeCompass</p>
						
					<?php else: ?>
											
						<div class="stats_content">
							
							<div class="one_fourth">
								
								<h3><?= $promoter->u_full_name ?></h3>
								
								<img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $promoter->up_profile_image . '_p.jpg' ?>" alt="" />
								
							</div>
							
							<div class="three_fourth last column lists_container">
	
								<div class="tabs">
									<div class="ui-widget-header">
										<span>Website Statistics</span>
										<ul>
											<li><a href="#tabs-1-<?= $promoter->up_id ?>">Profile Views</a></li>
											<li><a href="#tabs-2-<?= $promoter->up_id ?>">Reservation History</a></li>
										</ul>
									</div>
									
									<div id="tabs-1-<?= $promoter->up_id ?>">
										<div id="table_stats_up_<?= $promoter->up_id ?>" class="table_stats">
											<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
										</div>
									</div> <!-- end of first tab -->
									
									<div class="trailing_requests" id="tabs-2-<?= $promoter->up_id ?>">
										<table class="" style="display:none;">

											<thead>
											  <tr>
												<td></td>
												<?php foreach($promoter->statistics->trailing_weekly_guest_list_reservation_requests as $key => $value): ?>
													<th scope="col"><?= $key ?></th>
												<?php endforeach; ?>
											  </tr>
											</thead>
								
											<tbody>
											  <tr>
												<th scope="row">Requests</th>
												<?php foreach($promoter->statistics->trailing_weekly_guest_list_reservation_requests as $key => $value): ?>
													<td><?= $value ?></td>
												<?php endforeach; ?>
											  </tr>
											 
											</tbody>
											
										</table>
									</div>
																	
								</div>
								
								<hr>
								
								<table class="normal tablesorter">
									<thead>
										<tr>
											<th>Statistic</th>
											<th>Value</th>
										</tr>
									</thead>
									<tbody>
								
										<tr>
											<td>Upcoming guest list reservation requests</td>
											<td><?= $promoter->statistics->num_upcoming_guest_list_reservations->count ?></td>
										</tr>
										
										<tr class="odd">
											<td>Total all-time guest list reservation requests</td>
											<td><?= $promoter->statistics->num_total_guest_list_reservations->count ?></td>
										</tr>
										
										<tr>
											<td>Clients</td>
											<td><?= $promoter->statistics->num_clients[0]->count_clients ?></td>
										</tr>		
									</tbody>
								</table>

								<br><br>
								
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
	
	<h1>Promoters' Statistics</h1>
	
	<p>When your team is live this page will detail each of your promoters statistics; such as how often their profiles are viewed, their user reviews, and how many clients they have. You can add promoters to your team once your are live.</p>
	
	
<?php endif; ?>