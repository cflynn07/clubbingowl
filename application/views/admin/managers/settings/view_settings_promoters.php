<?php if($team->team_completed_setup == '1'): ?>
<?php
	$page_obj = new stdClass;
	$page_obj->users = $users;
	$page_obj->team = $team;
	$page_obj->date_time = date('Y-m-d', time());
	$page_obj->filter_uids = $filter_uids;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>
	
<div id="admin_manager_settings_promoters_wrapper">

	<style>
		table.normal{
			width: 100%;
		}
	</style>

	<div id="promoter_delete_dialog" style="display:none;">
		<p>Are you sure you want to remove this promoter from your team?</p>
	</div>
	
	<h1>Promoter Settings</h1>
	
	<div style="width:1050px; margin-bottom:40px;">
		<div class="one_half">
			
			<h3>Active</h3>
			
			<table class="normal tablesorter" style="width:100%;">
				<thead>
					<tr>
						<th class="header">Image</th>
						<th class="header">Full Name</th>
						<th class="header">Completed Setup</th>
						<th class="header">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($promoters as $key => $pro): ?>
					<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
						<td>
							<?php if($pro->up_completed_setup == '1'): ?>
								<img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $pro->up_profile_image . '_t.jpg' ?>" alt="profile picture" /> 
							<?php endif; ?>
						</td>
						<td><?= $pro->u_full_name ?></td>
						<td><?= ($pro->up_completed_setup == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
						<td>
							<span style="display:none;"><?= $pro->up_id ?></span>
							<a class="tooltip table_icon delete_promoter" title="Delete this user" href="#"><img alt="" src="<?= $central->admin_assets ?>images/icons/actions_small/Trash.png"></a>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php if(!$promoters): ?>
						<td>You currently have no active promoters</td>
					<?php endif; ?>
				</tbody>
			</table>
			
		</div>
		<div class="one_half last">
			
			<h3>Inactive</h3>
	
			<table class="normal tablesorter">
				<thead>
					<tr>
						<th class="header">Full Name</th>
						<?php if(false): ?><th class="header">Actions</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($inactive_promoters as $key => $pro): ?>
					<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
						<td><?= $pro->u_full_name ?></td>					
					</tr>
					<?php endforeach; ?>
					<?php if(!$inactive_promoters): ?>
						<td>You currently have no inactive promoters</td>
					<?php endif; ?>
				</tbody>
			</table>
			
		</div>
		
		<div style="clear:both;"></div>
		<hr>
		
		<div class="full_width last">
			<h3>Invitations History</h3>
			
			<div id="main_loading_indicator">
				<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
				<br><br>
			</div>
			
			<table id="promoter_invites" class="normal tablesorter" style="display:none;">
				<thead>
					<tr>
						<th class="header">Picture</th>
						<th class="header">Full Name</th>
						<th class="header">Invitation Status</th>
						<th class="header">Date</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($invitations as $key => $invite): ?>
						<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
							<td><div class="pic_square_<?= $invite->ui_oauth_uid ?>"></div></td>
							<td><div class="name_<?= $invite->ui_oauth_uid ?>"></div></td>
							<td>
								<?php if($invite->ui_response == '0'): ?>
									<?php if(($invite->ui_invitation_time + 432000) < time()): ?>
										<span style="color:orange;">Expired</span>
									<?php else: ?>
										Invited
									<?php endif; ?>
								<?php elseif($invite->ui_response == '1'): ?>
									<span style="color:green;">Accepted</span>
								<?php else: ?>
									<span style="color:red;">Declined</span>
								<?php endif; ?>
							</td>
							<td><?= date('Y-m-d', $invite->ui_invitation_time) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<a id="invite_promoters" class="button_link" href="#">Invite Promoters</a>
		</div>
		
		<div style="clear:both;"></div>
		<hr>
		
		<div class="full_width last">
			<h3>Non-Friend Invite</h3>
			<p>If you're not Facebook friends with the people you want to add to add to your team, you can still invite them manually by copying and pasting their Facebook Profile page URL into the input box below. VibeCompass will then determine 
				their Facebook ID and create an invitation. You must tell the team member to visit vibecompass.com to recieve their invitation.</p>
			
			<h5>Examples:</h5>
			<div style="margin-left:auto; margin-right:auto; width:1006px;">
				<img style="display:inline-block; width:500px;" src="<?= $central->admin_assets?>images/fb_url_no_uid.png" />
				<img style="display:inline-block; width:500px;" src="<?= $central->admin_assets?>images/fb_url_uid.png" />
			</div>
			
			<br>
			
			<label for="user_url">Paste Facebook user's profile URL:</label>
			<input id="user_url" type="text" name="user_url" value="Paste Facebook profile url..." style="border:1px solid #000;" />
			<br><br>
			<div id="user_url_result"></div>
			<a id="invite_manual" class="button_link" style="display:none;width:45px;" href="#">Invite</a>

		</div>
		
		<br><br><br>
		
	</div>
	
</div>
<?php else: ?>
	
	<?php $this->load->view('admin/_common/view_awaiting_setup_message'); ?>
	
	<h1>Promoter Settings</h1>
	
	<p>When <b><?= $team->team_name ?></b> is approved this page will detail your current promoters, allow you to invite your Facebook friends to promote for your team, and remove existing promoters.</p>
	
<?php endif; ?>