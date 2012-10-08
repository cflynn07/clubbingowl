<?php Kint::dump($events); ?>
<h1>Manage your Special Events</h1>
<p>
	Create, update, and delete 'Special Events' at each venue you're authorized to represent
</p>

<div id="delete_list_dialog" style="display:none">
	<h2>Are you sure?</h2>
	<p>If you delete this guest list, you will lose your clients.</p>
</div>

<h3>Current Events</h3>

<table class="normal tablesorter fullwidth">
	<thead>
		<tr>
			<th>Team</th>
			<th>Venue</th>
			<th>Date</th>
			<th>Event Name</th>
			<th>Event Image</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 	
			foreach($events->current_events as $key => $event): ?>
			<tr <?= ($key % 2) ? '' : 'class="odd"' ?> >
				
				<td><?= $event->t_name ?></td>
				<td><?= $event->tv_name ?></td>
				<td><?= date('Y-m-d', strtotime($event->pgla_event_date)) ?></td>
				<td><?= $event->pgla_name ?></td>
				<td><img src="<?= $central->s3_uploaded_images_base_url . 'events/' . $event->pgla_image . '_t.jpg' ?>" alt="event image" /></td>
				<td><?= date("F j, Y, g:i a", $event->pgla_create_time) ?></td>
				<td><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($event->pgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td class="pgla_id" style="display:none"><?= $event->pgla_id ?></td>
				<td>
					<a href="#" class="delete_guest_list_button" title="Delete this Guest List" class="tooltip table_icon guest_list_delete">
						<img src="<?=$central->admin_assets?>images/icons/actions_small/Trash.png" alt="" />
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if(!count($events->current_events)): ?>
			<tr>
				<td>You do not have any current events.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<br><hr><br>

<h3>Previous Events</h3>

<table class="normal tablesorter fullwidth">
	<thead>
		<tr>
			<th>Team</th>
			<th>Venue</th>
			<th>Date</th>
			<th>Event Name</th>
			<th>Creation Date</th>
			<th>Auto Approve</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 	
			foreach($events->expired_events as $key => $event): ?>
			<tr <?= ($key % 2) ? '' : 'class="odd"' ?> >
				
				<td><?= $event->t_name ?></td>
				<td><?= $event->tv_name ?></td>
				<td></td>
				<td><?= $event->pgla_name ?></td>
				<td><?= date("F j, Y, g:i a", $event->pgla_create_time) ?></td>
				<td><input type="checkbox" class="iphone" name="guest_list_auto_approve" <?= ($event->pgla_auto_approve == '1') ? 'checked="checked"' : '' ?>/></td>
				<td class="pgla_id" style="display:none"><?= $event->pgla_id ?></td>
				<td>
					<a href="#" class="delete_guest_list_button" title="Delete this Guest List" class="tooltip table_icon guest_list_delete">
						<img src="<?=$central->admin_assets?>images/icons/actions_small/Trash.png" alt="" />
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if(!count($events->expired_events)): ?>
			<tr>
				<td>You do not have any previous events.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<table>
	<tr>
		<td>
			<a href="<?=$central->promoter_admin_link_base?>manage_events_new/" style="text-decoration:none">
				<input class="button" type="submit" value="New Event" id="new_event" />
			</a>
		</td>
	</tr>
</table>

<!-- ui dialog -->
<div id="dialog-confirm" title="Delete Guest List?" style="display:none">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>You will not be able to create another guest list at this venue until next week. Are you sure?</p>
</div>
<!-- end ui dialog -->