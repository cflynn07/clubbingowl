<table class="normal tablesorter">
	<thead>
		<tr>
			<td>Statistic</td>
			<td>Value</td>
		</tr>
	</thead>
	<tbody>
		<tr class="odd">
			<td>Number of Venues</td><td></td>
		</tr>
		<tr>
			<td>Number of Promoters</td><td><?= $statistics->num_promoters->count ?></td>
		</tr>
		<tr class="odd">
			<td>Number of VibeCompass Users</td><td><?= $statistics->num_vc_users->count ?></td>
		</tr>
		<tr>
			<td>Number of Guest List Reservations</td><td></td>
		</tr>
		<tr class="odd">
			<td>Number of New VibeCompass Users (past 3 days)</td><td><?= $statistics->joins_past_3_days->count ?></td>
		</tr>
		<tr>
			<td>Number of New VibeCompass Users (past 7 days)</td><td><?= $statistics->joins_past_7_days->count ?></td>
		</tr>
		<tr class="odd">
			<td>Number of New VibeCompass Users (past 14 days)</td><td><?= $statistics->joins_past_14_days->count ?></td>
		</tr>
	</tbody>
</table>