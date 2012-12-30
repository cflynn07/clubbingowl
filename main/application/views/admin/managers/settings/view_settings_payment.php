<?php $card_data_obj = json_encode($card_data); ?>
<script type="text/javascript">window.card_data=<?= $card_data_obj ?>;</script>

<div style="margin-bottom:40px;" id="admin_manager_settings_payments_wrapper">
	
	<h1>Payment Information</h1>
	
	<div id="on_file_payment">
		
		<?php if($mt_live_status): ?>
			<p><img src="<?= $central->admin_assets . 'images/icons/small_icons/OK.png' ?>" style="vertical-align:middle;" />&nbsp;Your payment information is on-file and valid.</p>
			
			<table>
				<tr>
					<td>Card Data:</td>
					<td style="padding-left:10px;"><?= $card_data->type ?> - <?= $card_data->last4 ?></td>
				</tr>
				<tr>
					<td>Next Billing Cycle:</td>
					<td style="padding-left:10px;"><?= date('l F j, Y', strtotime('first day of +1 month')); ?></td>
				</tr>
			</table>

		<?php else: ?>
			<p><img src="<?= $central->admin_assets . 'images/icons/small_icons/Delete.png' ?>" style="vertical-align:middle;">&nbsp;Your payment information needs to be updated.</p>
		<?php endif; ?>
		
	</div>
	
	
	<div id="update">
		<form action="" method="POST" id="payment-form">
			<fieldset>
				<legend>Credit Card</legend>
				<p>
					
					<label>Card Number</label>
					<input type="text" size="24" autocomplete="off" class="card-number"/>
					
				</p>
				<p>
					<label>CVC</label>
					<input type="text" size="4" autocomplete="off" class="card-cvc"/>
				</p>
				<p>
					<label>Expiration (MM/YYYY)</label>
					<input type="text" size="2" class="card-expiry-month"/><span>&nbsp;/&nbsp;&nbsp;&nbsp;</span><input type="text" size="4" class="card-expiry-year"/>
				</p>
			</fieldset>
		</form>
					
		<a id="submit" class="button_link" href="#">Update Payment Info</a><br /><br />
		
		<img id="loading" style="display:none;" src="<?= $central->global_assets ?>images/ajax.gif" alt="loading..." />
		<p style="color:red;" id="payment_errors"></p>
		
	</div>

	<div style="display:none;" id="stripe_pub_key"><?= $this->config->item('stripe_key_' . ((MODE == 'local') ? 'test' : 'live' ) . '_public') ?></div>
	
	
	
	
	
	<h1>Invoice History</h1>
	<table id="invoice_history" class="stdtable normal one_half last">
		<thead>
			<tr>
				<th>Month</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			
			
			
		<?php if(false): ?>
			<tr>
				<td>November 2012</td>
				<td>$533.00</td>
			</tr>
			<tr>
				<td>December 2012</td>
				<td>$533.00</td>
			</tr>
		<?php endif; ?>
			
			
			
		</tbody>
	</table>
	
	
	
	
	
	
	<h1>Current Subscription</h1>
	<p>*Accounts are automatically downgraded (if applicable) after one billing cycle.</p>
	<table class="stdtable normal one_half last">
		<thead>
			<tr>
				<th>Rate ($USD / Month)</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			
			<tr <?= ($card_data->billing_tier == 0) ? 'style="background-color:lightyellow;"' : '' ?>>
				<td style="text-align:center;">$150</td>
				<td>
					Up to 5 Accounts
				</td>
			</tr>
			<tr <?= ($card_data->billing_tier == 1) ? 'style="background-color:lightyellow;"' : '' ?>>
				<td style="text-align:center;">$450</td>
				<td>
					Up to 10 Accounts
				</td>
			</tr>
			<tr <?= ($card_data->billing_tier == 2) ? 'style="background-color:lightyellow;"' : '' ?>>
				<td style="text-align:center;">$800</td>
				<td>
					Up to 15 Accounts
				</td>
			</tr>
			<tr <?= ($card_data->billing_tier == 3) ? 'style="background-color:lightyellow;"' : '' ?>>
				<td style="text-align:center;">$1600</td>
				<td>
					Unlimited Accounts
				</td>
			</tr>
			
		</tbody>
	</table>
	
	
	
	
	
	
	
</div>