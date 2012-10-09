<?php
	$page_obj = new stdClass;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>



<div id="admin_manager_settings_venues_wrapper">
	
	<div id="venue_delete_dialog" style="display:none;">
		<p>Are you sure you want to remove this venue from your team?</p>
	</div>
		
	<h1>Venue Settings</h1>
	
	<h2>My team venues</h2>
	
	<table class="normal tablesorter fullwidth">
		<thead>
			<tr>
				<th class="header">No</th>
				<th class="header">Name</th>
				<th class="header">Description</th>
				<th class="header">Street Address</th>
				<th class="header">City</th>
				<th class="header">State</th>
				<th class="header">Zip</th>
				<th class="header">Map</th>
				<th class="header">Image</th>
				<th class="header">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($settings_venues->team_venues as $key => $venue): ?>
			<tr <?= ($key % 2) ? '' : 'class="odd"' ?>>
				<td><?= $key ?></td>
				<td><?= $venue->tv_name ?></td>
				<td><?= $venue->tv_description ?></td>
				<td><?= $venue->tv_street_address ?></td>
				<td><?= $venue->tv_city ?></td>
				<td><?= $venue->tv_state ?></td>				
				<td><?= $venue->tv_zip ?></td>				
				<td><img src="http://maps.googleapis.com/maps/api/staticmap?size=150x150&maptype=roadmap&markers=color:red|<?= urlencode($venue->tv_street_address . ' ' . $venue->tv_city . ', ' . $venue->tv_state . ' ' . $venue->tv_zip) ?>&sensor=false" alt="Location Map"></td>
				<td>
					<?php if($venue->tv_image): ?>
					<img src="<?= $central->s3_uploaded_images_base_url . 'venues/banners/' . $venue->tv_image . '_t.jpg' ?>" style="width:150px;" alt="Venue Image">
					<?php endif; ?>
				</td>
				<td style="white-space:nowrap; padding:0px; width:99px;">
					<span style="display:none;"><?= $venue->tv_id ?></span>
					<a style="display:inline-block;" class="tooltip table_icon delete_venue" title="Delete venue" href="#"><img alt="" src="<?= $central->admin_assets ?>images/icons/actions_small/Trash.png"></a>
					<a style="display:inline-block;" class="ajaxify tooltip table_icon edit_venue" title="Edit venue" href="<?= $central->manager_admin_link_base ?>settings_venues_edit/<?= $venue->tv_id ?>/"><img alt="" src="<?= $central->admin_assets ?>images/icons/actions_small/Pencil.png"></a>
					<a style="display:inline-block;" class="ajaxify tooltip table_icon edit_venue_floorplan" title="Edit floorplan" href="<?= $central->manager_admin_link_base ?>settings_venues_edit_floorplan/<?= $venue->tv_id ?>/"><img alt="" src="<?= $central->admin_assets ?>images/icons/actions_small/Preferences.png"></a>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php if(!$settings_venues->team_venues): ?>
				<td>You currently have no venues</td>
			<?php endif; ?>
		</tbody>
	</table>
	
	<a id="add_venue" class="ajaxify button_link" href="<?= $central->manager_admin_link_base ?>settings_venues_new/">Add Venue</a>

</div>
<div style="height: 40px;" class="clearboth"></div> 