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
		
		<div id="tabs" style="width: 1050px; display:none;">
			
			<div class="ui-widget-header">
				<span>Promoter Clients</span>
				
				<div style="display: inline-block; float: right;">
					Select Promoter: 
					<select class="promoter_select">
						<?php foreach($promoters as $key => $promoter): ?>
							<option value="<?= $key ?>"><?= $promoter->u_full_name ?></option>
						<?php endforeach; ?>
							<option value="<?= count($promoters) ?>"><span style="font-weight: bold;">All Promoters</span></option>
					</select>
				</div>
				
				<ul>
				<?php foreach($promoters as $key => $promoter): ?>
					<li><a href="#tabs-<?= $key ?>"><?= $promoter->u_full_name ?></a></li>
				<?php endforeach; ?>
					<li><a href="#tabs-<?= count($promoters) ?>">All Promoters</a></li>
				</ul>
			</div>
			
			<?php foreach($promoters as $key => $promoter): ?>
				<div id="tabs-<?= $key ?>">
					<?php if($promoter->up_banned == '1'): ?>
						
						<p>This promoter has been banned from VibeCompass</p>
						
					<?php else: ?>
											
						<div class="client_list_content">
							
							<div class="one_fourth">
								
								<h3><?= $promoter->u_full_name ?></h3>
								
								<img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $promoter->up_profile_image . '_p.jpg' ?>" alt="" style="margin-left: 35px;"/>
								
							</div>
							
							<div class="three_fourth last column lists_container">
								
								
								
								<table class="clients_list normal">
									<thead>
										<th>Name</th>
										<th style="width: 200px;">Picture</th>
									</thead>
									<tbody>
										<?php foreach($promoter->clients as $client): ?>
										<tr>
											<td style="text-wrap: none;"><div class="name_<?= $client->pglr_user_oauth_uid ?>"></div></td>
											<td style="width: 200px;"><div class="pic_big_<?= $client->pglr_user_oauth_uid ?>"></div></td>
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
						
					<?php endif; ?>
					<div style="clear:both;"></div>
				</div>
			<?php endforeach; ?>
				<div id="tabs-<?= count($promoters) ?>">
					<div class="client_list_content">
							
							<div class="last column lists_container">
								<table class="clients_list normal">
									<thead>
										<th>Name</th>
										<th style="width: 200px;">Picture</th>
									</thead>
									<tbody>
										<?php foreach($users as $user): ?>
										<tr>
											<td style="text-wrap: none;"><div class="name_<?= $user ?>"></div></td>
											<td style="width: 200px;"><div class="pic_big_<?= $user ?>"></div></td>
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
				</div>
		</div>
	
	<?php else: ?>
		
		<p>You do not have any active promoters</p>
		
	<?php endif; ?>
	
<?php else: ?>

	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>

	<h1>Promoters' Clients</h1>
	
	<p>When your team is live this page will detail each of your promoters 'clients' - VibeCompass users that have requested to join a promoter's guest list or event. You can add promoters to your team once your are live.</p>

<?php endif; ?>