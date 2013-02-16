<h1>Manage your Guest Lists</h1>
<p>
	Create, update and delete your available guest lists for each venue you're authorized to represent.
</p>

<div id="delete_list_dialog" style="display:none">
	<h2>Are you sure?</h2>
	<p>If you delete this guest list, you will have to make a new one if you want to replace it.</p>
</div>

<table class="normal tablesorter fullwidth">
	<thead>
		<tr>
			<th>Venue</th>
			<th>Weekday</th>
			<th>Guest List Name</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		
		
		
		
				
		<?php $count = 0; ?>
		<?php foreach($team_venues as $key => $tv): ?>
			
			<?php foreach($tv->tv_gla as $gla): ?>
			<tr <?= ($count % 2) ? '' : 'class="odd"' ?>>
				<?php $count++ ?>
				<td><?= $tv->tv_name ?></td>
				<td><?= ucfirst($gla->tgla_day) ?></td>
				<td><?= $gla->tgla_name ?></td>
				<td><?= date("F j, Y, g:ia", $gla->tgla_create_time) ?></td>
				<td><input data-tgla_id="<?= $gla->tgla_id ?>" type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($gla->tgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td style="padding:5px 0 0 5px;">
					
					
					<a data-action="delete" data-tgla_id="<?= $gla->tgla_id ?> href="#" class="button_link guest_list_delete delete_guest_list_button">DELETE</a>
					<a href="<?= $central->manager_admin_link_base ?>settings_guest_lists_edit/<?= $gla->tgla_id ?>/" class="button_link btn-action ajaxify">EDIT</a>
					
					
					<?php if(false): ?>				
						<a data-action="delete" data-tgla_id="<?= $gla->tgla_id ?>" style="display:inline-block;" class="tooltip guest_list_delete delete_guest_list_button ajaxify" title="Delete this Guest List" href="#">
							<img src="<?= $central->admin_assets ?>images/icons/actions_small/Trash.png" />
						</a>
															
						<a style="display:inline-block;" class="tooltip edit_guest_list ajaxify" title="Edit Guest List" href="<?= $central->manager_admin_link_base ?>settings_guest_lists_edit/<?= $gla->tgla_id ?>/">
							<img src="<?= $central->admin_assets ?>images/icons/actions_small/Pencil.png" />
						</a>
					<?php endif; ?>
					
				</td>
				
			</tr>
			<?php endforeach; ?>
			
		<?php endforeach; ?>
		
		
		<?php if($count === 0): ?>
			<tr>
				<td colspan="6">You do not have any guest lists.</td>
			</tr>
		<?php endif; ?>	
				
				
				
				
				
				
	</tbody>
</table>

<table>
	<tr>
		<td>
			
			<a id="new_guest_list" href="<?=$central->manager_admin_link_base?>settings_guest_lists_new/" class="button_link btn-action ajaxify">New Weekly List / Event</a>
			
			<?php if(false): ?>
			<a href="<?=$central->manager_admin_link_base?>settings_guest_lists_new/" class="ajaxify" style="text-decoration:none">
				<input class="button" type="submit" value="New Guest List" id="new_guest_list" />
			</a>
			<?php endif; ?>
		</td>
	</tr>
</table>