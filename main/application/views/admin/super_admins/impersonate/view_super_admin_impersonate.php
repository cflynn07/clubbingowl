<?php Kint::dump($managers); ?>
<?php Kint::dump($promoters); ?>
<?php Kint::dump($hosts); ?>

<script type="text/javascript">
jQuery(function(){
	
	jQuery('a.impersonate').bind('click', function(){
		
		var oauth_uid = jQuery(this).parents('tr').find('td.oauth_uid').html();
		var type = jQuery(this).parents('tr').find('td.type').html();
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		var data 			= {};
		data.oauth_uid 		= oauth_uid;
		data.type 			= type;
		data.ci_csrf_token 	= cct;
		data.vc_method 		= 'impersonate';
		
		switch(type){
			case 'promoter':
			
				data.up_id = jQuery(this).parents('tr').find('td.up_id').html();
				data.team_fan_page_id = jQuery(this).parents('tr').find('td.team_fan_page_id').html();
				data.up_users_oauth_uid = jQuery(this).parents('tr').find('td.up_users_oauth_uid').html();
			
				break;
			case 'manager':
				
				data.team_fan_page_id = jQuery(this).parents('tr').find('td.team_fan_page_id').html();
				data.mt_id = jQuery(this).parents('tr').find('td.mt_id').html();
				data.mt_users_oauth_uid = jQuery(this).parents('tr').find('td.mt_users_oauth_uid').html();
				data.team_name = jQuery(this).parents('tr').find('td.team_name').html();
				data.team_description = jQuery(this).parents('tr').find('td.team_description').html();
				data.team_piwik_id_site = jQuery(this).parents('tr').find('td.team_piwik_id_site').html();
				data.team_completed_setup = jQuery(this).parents('tr').find('td.team_completed_setup').html();
				data.c_id = jQuery(this).parents('tr').find('td.c_id').html();
				data.c_name = jQuery(this).parents('tr').find('td.c_name').html();
				data.c_state = jQuery(this).parents('tr').find('td.c_state').html();
			
				break;
		}
		
		var _this = this;
		
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: data,
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data);
				
				if(data.success){
					alert(data.message);
					
					if(type == 'promoter'){
						window.location = '/admin/promoters/';
					}else if(type == 'manager'){
						window.location = '/admin/managers/';
					}else if(type == 'host'){
						window.location = '/admin/hosts/';
					}
					
				}else{
					alert('error');
				}
				
			}
		});
		
		return false;
		
	});

});
</script>

<div>
	<a href="<?= $central->promoter_admin_link_base ?>"><?= $central->promoter_admin_link_base ?></a>
	<br>
	<a href="<?= $central->manager_admin_link_base ?>"><?= $central->manager_admin_link_base ?></a>
</div>

<hr>

<h1>Managers</h1>
<table class="normal">
	<thead>
		<tr>
			<th>Name</th>
			<th>Team</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($managers as $manager): ?>
		<tr>
			<td class="oauth_uid" style="display:none;"><?= $manager->mt_user_oauth_uid ?></td>
			<td class="type" style="display:none;">manager</td>
			
			
			
			<td class="mt_id" style="display:none;"><?= $manager->mt_id ?></td>
			<td class="mt_users_oauth_uid" style="display:none;"><?= $manager->mt_user_oauth_uid ?></td>
			<td class="team_name" style="display:none;"><?= $manager->t_name ?></td>
			<td class="team_fan_page_id" style="display:none;"><?= $manager->t_fan_page_id ?></td>
			<td class="team_description" style="display:none;"><?= $manager->t_description ?></td>
			<td class="team_piwik_id_site" style="display:none;"><?= $manager->t_piwik_id_site ?></td>
			<td class="team_completed_setup" style="display:none;"><?= $manager->t_completed_setup ?></td>
			<td class="c_id" style="display:none;"><?= $manager->t_city_id ?></td>
			<td class="c_name" style="display:none;"><?= 'impersonating...' ?></td>
			<td class="c_state" style="display:none;"><?= 'impersonating...' ?></td>
			
			
		
		
			
			
			<td><?= $manager->u_full_name ?></td>
			<td><?= $manager->t_name ?></td>
			
			
			
			
			<td><a data-follow="<?= $central->front_link_base . 'admin/managers/' ?>" class="impersonate" href="#">Impersonate</a></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h1>Promoters</h1>
<table class="normal">
	<thead>
		<tr>
			<th>Name</th>
			<th>Team</th>
			<th>Completed Setup</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($promoters as $promoter): ?>
		<tr>
			<td class="oauth_uid" style="display:none;"><?= $promoter->up_users_oauth_uid ?></td>
			<td class="up_users_oauth_uid" style="display:none;"><?= $promoter->up_users_oauth_uid ?></td>
			<td class="type" style="display:none;">promoter</td>
			<td class="up_id"><?= $promoter->up_id ?></td>
			<td class="team_fan_page_id"><?= $promoter->t_fan_page_id ?></td>
			
			<td><?= $promoter->u_full_name ?></td>
			<td><?= $promoter->t_name ?></td>
			<td><?= $promoter->up_completed_setup ?></td>
			<td><a data-follow="<?= $central->front_link_base . 'admin/promoters/' ?>" class="impersonate" href="#">Impersonate</a></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h1>Hosts</h1>
<table class="normal">
	<thead>
		<tr>
			<th>Name</th>
			<th>Team</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>