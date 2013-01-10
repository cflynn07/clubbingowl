<h1>Marketing</h1>

<div style="width:1050px; margin-bottom:40px;">
	<?php Kint::dump($data); ?>
	
	<div class="full_width last">
		
		<h2>Email Campaign History</h2>
		
		<a class="button_link ajaxify" href="<?= $central->manager_admin_link_base . 'marketing_new/' ?>">New Campaign</a><br><br>
		
		<table class="normal tablesorter" style="width:100%;">
			<thead>
				<tr>
					<th class="header">Date</th>
					<th class="header">Author</th>
					<th class="header">Subject Header</th>
					<th class="header">Recipients</th>
					<th class="header">Cost</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data->campaigns as $c): ?>
					<tr>
						<td><?= strftime('%a, %B %e, %G', $c->send_time) ?></td>
						<td><?= $c->full_name ?></td>
						<td><?= $c->campaign_title ?></td>
						<td><?= count($c->recipients) ?></td>
						<td>$<?= ceil(count($c->recipients) * 0.03) ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	
	
	<br style="clear:both">
	
	
	
	<div class="one_half">
		<h2>Clientelle Summary</h2>
		<table class="normal tablesorter" style="width:100%;">
			<thead>
				<tr>
					<th class="header">Clients</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Your team (promoters & venues) have a database of <strong style="text-decoration:underline;"><?= count($data->clients) ?></strong> clients that have no opted out of recieving emails from ClubbingOwl.</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="one_half last">
		<?php
			$month_start = strtotime('this month',strtotime(date('m/01/y')));
			
			$month_email_total = 0;
			$month_invoice_total = 0;
			
			foreach($data->campaigns as $c){
				
				if($c->send_time < $month_start)
					continue;
				
				$month_email_total += count($c->recipients);
				$month_invoice_total += ceil(count($c->recipients) * 0.03);
			}
			
		?>
		<h2>Invoice Summary</h2>
		<table class="normal tablesorter" style="width:100%;">
			<thead>
				<tr>
					<th class="header">Month</th>
					<th class="header"># of Campaigns</th>
					<th class="header"># of Deliveries</th>
					<th class="header">Invoice Amt</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= strftime('%B', time()) ?></td>
					<td><?= count($data->campaigns) ?></td>
					<td><?= $month_email_total ?></td>
					<td>$<?= $month_invoice_total ?></td>
				</tr>
			</tbody>
		</table>
		<span>Emails are billed at a rate of $0.03 per delivery.</span>
	</div>
	
	
	
	
	<br style="clear:both">
	
	
	
	
	<div class="one_half last">
		<h2>About this Feature</h2>
		<p>ClubbingOwl gives you tools to more effectively reach your clients.</p>
	</div>
	
</div>