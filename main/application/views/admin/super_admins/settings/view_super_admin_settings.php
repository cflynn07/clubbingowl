<?php //Kint::dump($teams_promoters_venues); ?>

<script type="text/javascript">
jQuery(function(){
	
	jQuery('input.approve_team').bind('change', function(){
		
		var team_fan_page_id = jQuery(this).parent().parent().parent().find('td.team_fan_page_id').html().trim();

		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				team_fan_page_id: team_fan_page_id,
				vc_method: 'approve_ban_team',
				completed_setup: (jQuery(this).attr('checked') == undefined) ? false : true
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data);
							
			}
		});

	});

	jQuery('input.ban_promoter').bind('change', function(){
		
		var promoter_id = jQuery(this).parent().parent().parent().find('td.promoter_id').html();
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				promoter_id: promoter_id,
				vc_method: 'approve_ban_promoter',
				banned: (jQuery(this).attr('checked') == undefined) ? false : true
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data);
							
			}
		});
		
	});
	
	jQuery('input').iphoneStyle();
	
});
</script>
<style type="text/css">
table.normal td{
	vertical-align: top !important;
}
</style>

<table class="normal">
	<thead>
		<tr>
			<th>Team Facebook ID</th>
			<th>Team Name</th>
			<th>Completed Setup</th>
			<th>Managers</th>
			<th>Venues</th>
			<th>Promoters</th>
			<th>Hosts</th>
		</tr>
	</thead>
	<tbody>
		
		<?php foreach($teams_promoters_venues as $tpv): ?>
			<tr>
				<td style="display:none;" class="team_fan_page_id">
					<?= $tpv->t_fan_page_id ?>
				</td>
				<td>
					<a href="http://www.facebook.com/pages/@/<?= $tpv->t_fan_page_id ?>?sk=app_<?= $this->config->item('facebook_app_id') ?>" target="_new"><?= $tpv->t_fan_page_id ?></a>
				</td>
				<td>
					<?= $tpv->t_name ?>
				</td>
				<td>
					<input type="checkbox" class="iphone approve_team" <?= ($tpv->t_completed_setup == '1') ? 'checked="checked"' : '' ?> />
				</td>
				<td>
					<table class="normal tablesorter">
						<thead>
							<tr>
								<th>Name</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($tpv->managers as $manager): ?>
								<tr>
									<td><?= $manager->u_full_name ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
				<td style="vertical-align: top;">
					<table class="normal tablesorter">
						<thead>
							<tr>
								<th>Venue Name</th>
								<th>Street Address</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($tpv->team_venues as $tv): ?>
							<tr>
								<td><?= $tv->tv_name ?></td>
								<td><?= $tv->tv_street_address ?></td>
								<td><?= $tv->tv_description ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
				<td style="vertical-align: top;">
					<table class="normal tablesorter">
						<thead>
							<tr>
								<th>Picture</th>
								<th>Promoter Name</th>
								<th>Completed Setup</th>
								<th>Team Banned</th>
								<th>Admin Banned</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($tpv->team_promoters as $tp): ?>
							<?php 
								if($tp->pt_quit == '1')
									continue;
							?>
							<tr>
								<td style="display:none;" class="promoter_team_id"><?= $tp->pt_id ?></td>
								<td style="display:none;" class="promoter_id"><?= $tp->up_id ?></td>
								<td><img src="<?= $central->s3_uploaded_images_base_url . 'profile-pics/' . $tp->up_profile_image . '_t.jpg' ?>" alt="" /></td>
								<td><?= $tp->u_full_name ?></td>
								<td><?= ($tp->up_completed_setup) ? 'Yes' : 'No' ?></td>
								<td><?= ($tp->pt_banned) ? 'Yes' : 'No' ?></td>
								<td><input type="checkbox" class="iphone ban_promoter" <?= ($tp->up_banned == '1') ? 'checked="checked"' : '' ?> /></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
				<td>
					<table class="normal">
						<thead>
							<tr>
								<th>Hosts</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($tpv->hosts as $host): ?>
							<tr>
								<td><?= $host->u_full_name ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
		
	</tbody>
</table>