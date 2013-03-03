<h1>Manage your Guest Lists</h1>
<p>
	Create, update and delete your available guest lists for each venue you're authorized to represent.
</p>

<div id="delete_list_dialog" style="display:none">
	<h2>Are you sure?</h2>
	<p>If you delete this guest list, you will have to make a new one if you want to replace it.</p>
</div>

<?php //Kint::dump($promoters_guest_lists); ?>

<h3>Weekly Guest Lists</h3>

<table class="normal tablesorter fullwidth">
	<thead>
		<tr>
			<th>Venue</th>
			<th>Weekday</th>
			<th>Name</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		
		
		<?php 	$count = 0;
		foreach($promoters_guest_lists as $guest_list): ?>
		
			<?php if($guest_list->pgla_event == '1') continue; ?>
		
			<tr <?= ($count % 2) ? '' : 'class="odd"' ?> name=<?=$guest_list->pgla_id?>>
				<?php $count++; ?>
				<td><?= $guest_list->tv_name ?></td>
				<td><?= ucfirst($guest_list->pgla_day) ?></td>
				<td style="min-width:200px;"><?= $guest_list->pgla_name ?></td>
				<td><?= date("F j, Y, g:ia", $guest_list->pgla_create_time) ?></td>
				<td><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($guest_list->pgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td class="pgla_id" style="display:none"><?= $guest_list->pgla_id ?></td>
				<td style="padding:5px 0 0 5px;">
					
					<a href="#" class="button_link guest_list_delete delete_guest_list_button">DELETE</a>
					<a href="<?= $central->promoter_admin_link_base ?>manage_guest_lists_edit/<?= $guest_list->pgla_id ?>/" class="button_link btn-action edit_guest_list ajaxify">EDIT</a>
					
					
					<?php if(false): ?>
					<a style="display:inline-block;" class="tooltip guest_list_delete delete_guest_list_button ajaxify" title="Delete this Guest List" href="#">
						<img src="<?=$central->admin_assets?>images/icons/actions_small/Trash.png" />
					</a>
					
					<a style="display:inline-block;" class="tooltip edit_guest_list ajaxify" title="Edit Guest List" href="<?= $central->promoter_admin_link_base ?>manage_guest_lists_edit/<?= $guest_list->pgla_id ?>/">
						<img src="<?= $central->admin_assets ?>images/icons/actions_small/Pencil.png" />
					</a>
					
					<?php endif; ?>
					
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if(!$count): ?>
			<tr>
				<td colspan="6">You do not have any weekly guest lists.</td>
			</tr>
		<?php endif; ?>
		
		
	</tbody>
</table>






<h3>Events</h3>

<table class="normal tablesorter fullwidth">
	<thead>
		<tr>
			<th>Venue</th>
			<th>Date</th>
			<th>Name</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		
		
		<?php 	$count1 = 0;
		foreach($promoters_guest_lists as $guest_list): ?>
		
			<?php if($guest_list->pgla_event == '0') continue; ?>
		
			<tr <?= ($count1 % 2) ? '' : 'class="odd"' ?> name=<?=$guest_list->pgla_id?>>
				<?php $count1++; ?>
				<td><?= $guest_list->tv_name ?></td>
				<td><?= $guest_list->pgla_event_date ?></td>
				<td style="min-width:200px;"><?= $guest_list->pgla_name ?></td>
				<td><?= date("F j, Y, g:ia", $guest_list->pgla_create_time) ?></td>
				<td><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($guest_list->pgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td class="pgla_id" style="display:none"><?= $guest_list->pgla_id ?></td>
				<td style="padding:5px 0 0 5px;">
					
					<a href="#" class="button_link guest_list_delete delete_guest_list_button">DELETE</a>
					<a href="<?= $central->promoter_admin_link_base ?>manage_guest_lists_edit/<?= $guest_list->pgla_id ?>/" class="button_link btn-action edit_guest_list ajaxify">EDIT</a>
					
					
					<?php if(false): ?>
					<a style="display:inline-block;" class="tooltip guest_list_delete delete_guest_list_button ajaxify" title="Delete this Guest List" href="#">
						<img src="<?=$central->admin_assets?>images/icons/actions_small/Trash.png" />
					</a>
					
					<a style="display:inline-block;" class="tooltip edit_guest_list ajaxify" title="Edit Guest List" href="<?= $central->promoter_admin_link_base ?>manage_guest_lists_edit/<?= $guest_list->pgla_id ?>/">
						<img src="<?= $central->admin_assets ?>images/icons/actions_small/Pencil.png" />
					</a>
					
					<?php endif; ?>
					
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if(!$count1): ?>
			<tr>
				<td colspan="6">You do not have any events.</td>
			</tr>
		<?php endif; ?>
		
		
	</tbody>
</table>










<table>
	<tr>
		<td>
			
			<a id="new_guest_list" href="<?=$central->promoter_admin_link_base?>manage_guest_lists_new/" class="button_link btn-action ajaxify">New Weekly List / Event</a>
			 
			<?php if(false): ?>
			<a href="<?=$central->promoter_admin_link_base?>manage_guest_lists_new/" class="ajaxify" style="text-decoration:none">
				<input class="button" type="submit" value="New Guest List" id="new_guest_list" />
			</a>
			<?php endif; ?>
		
		</td>
	</tr>
</table>