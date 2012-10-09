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
			<th>Team</th>
			<th>Venue</th>
			<th>Weekday</th>
			<th>Guest List Name</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 	$count = 0;
			foreach($promoters_guest_lists as $guest_list): ?>
			<tr <?= ($count % 2) ? '' : 'class="odd"' ?> name=<?=$guest_list->pgla_id?>>
				<?php $count++; ?>
				<td><?= $guest_list->t_name ?></td>
				<td><?= $guest_list->tv_name ?></td>
				<td><?= ucfirst($guest_list->pgla_day) ?></td>
				<td><?= $guest_list->pgla_name ?></td>
				<td><?= date("F j, Y, g:i a", $guest_list->pgla_create_time) ?></td>
				<td><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($guest_list->pgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td class="pgla_id" style="display:none"><?= $guest_list->pgla_id ?></td>
				<td>
					
					<a style="display:inline-block;" class="tooltip guest_list_delete delete_guest_list_button ajaxify" title="Delete this Guest List" href="#">
						<img src="<?=$central->admin_assets?>images/icons/actions_small/Trash.png" />
					</a>
					
					<a style="display:inline-block;" class="tooltip edit_guest_list ajaxify" title="Edit Guest List" href="<?= $central->promoter_admin_link_base ?>manage_guest_lists_edit/<?= $guest_list->pgla_id ?>/">
						<img src="<?= $central->admin_assets ?>images/icons/actions_small/Pencil.png" />
					</a>
					
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if(!count($promoters_guest_lists)): ?>
			<tr>
				<td>You do not have any guest lists.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<table>
	<tr>
		<td>
			<a href="<?=$central->promoter_admin_link_base?>manage_guest_lists_new/" class="ajaxify" style="text-decoration:none">
				<input class="button" type="submit" value="New Guest List" id="new_guest_list" />
			</a>
		</td>
	</tr>
</table>