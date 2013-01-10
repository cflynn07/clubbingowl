<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->users = $users;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

	<?php if($team_venues): ?>
		
		<h1>Team Clients</h1>
		
		<div id="main_loading_indicator">
			<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		</div>
		
		<div id="tabs" style="display:none;">
			
			<div class="ui-widget-header">
				<span>Venue Clients</span>
				<ul>
				<?php foreach($team_venues as $key => $venue): ?>
					<li><a href="#tabs-<?= $key ?>"><?= $venue->tv_name ?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
			
			<?php foreach($team_venues as $key => $venue): ?>
				<div id="tabs-<?= $key ?>">
											
						<div class="clients_content">
							
							<div class="one_fourth">
								
								<?php if(false): ?>
								Might want to place an image of the venue here
								<img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $promoter->up_profile_image . '_p.jpg' ?>" alt="" style="margin-left: 35px;"/>
								<?php endif; ?>
								
							</div>
							
							<div class="three_fourth last column lists_container">
								
								<table class="clients_list normal">
									<thead>
										<th>Name</th>
										<th style="width: 200px;">Picture</th>
									</thead>
									<tbody>
										<?php foreach($venue->clients as $client): ?>
										<tr>
											<td style="text-wrap: none;"><div class="name_<?= $client->tglr_user_oauth_uid ?>"></div></td>
											<td style="width: 200px;"><div class="pic_big_<?= $client->tglr_user_oauth_uid ?>"></div></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
									<tfoot>
										<th>Name</th>
										<th style="width: 200px;">Picture</th>
									</tfoot>
								</table>
								
							</div>
							
						</div>
						
					<div style="clear:both;"></div>
				</div>
			<?php endforeach; ?>
		</div>
	
	<?php else: ?>
		
		<p>You do not have any active venues</p>
		
	<?php endif; ?>
	
<?php else: ?>
	
	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>
	
	<h1>Team Clients</h1>
	
	<p>When your team is live this page will detail all of your 'clients' - ClubbingOwl users that have requested to be included on one of your team guest lists.</p>
	
<?php endif; ?>