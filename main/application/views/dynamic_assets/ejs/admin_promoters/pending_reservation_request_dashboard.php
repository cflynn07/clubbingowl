<tr style="display:none;">
	
	<td class="pglr_id hidden hidden" style="display:none"><?= $group->id ?></td>
	<td class="pglr_head_user hidden" style="display:none"><?= $group->head_user ?></td>
	
	<td><div class="name_<?= $group->head_user ?>"></div></td>
	<td><div class="pic_square_<?= $group->head_user ?>"></div></td>
	<td><?= $wgl->tv_name ?></td>
	<td><?= $wgl->pgla_name ?></td>
	<td><?= date('l m/d/y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></td>
	<td><?= (strlen($group->pglr_request_msg)) ? $group->pglr_request_msg : ' - ' ?></td>
	<td style="white-space:nowrap; width:244px;">
		<?php if(!count($group->entourage_users)): ?>
			<p>No Entourage</p>
		<?php else: ?>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Picture</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($group->entourage_users as $key2 => $ent_user): ?>
					<tr <?= ($key2 % 2) ? 'class="odd"' : '' ?>>
						<td><div class="name_<?= $ent_user ?>"></div></td>
						<td><div class="pic_square_<?= $ent_user ?>"></div></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</td>
	
	<td><?= ($group->pglr_table_request == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
	<td>$<?= $group->table_min_spend ?></td>
	<td style="white-space:nowrap;"><?= preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $group->u_phone_number) ?></td>
	<td><span style="color:blue;text-decoration:underline;">Respond</span></td>	
</tr>