<div style="margin-bottom:40px;" id="admin_manager_settings_payments_wrapper">
	
	<h1>Payment Information</h1>
	
	<div id="on_file_payment">
		
		<?php if($mt_live_status): ?>
			<p><img src="<?= $central->admin_assets . 'images/icons/small_icons/OK.png' ?>" style="vertical-align:middle;" />&nbsp;Your payment information is on-file and valid. Click <a id="show_pay_info" href="#">here</a> to update your information.</p>
			<p>Card Data: <?= $card_data->type ?> - <?= $card_data->last4 ?></p>
		<?php else: ?>
			<p><img src="<?= $central->admin_assets . 'images/icons/small_icons/Delete.png' ?>" style="vertical-align:middle;">&nbsp;Your payment information needs to be updated. Click <a id="show_pay_info" href="#">here</a> to update your information.</p>
		<?php endif; ?>
		
	</div>
	
	
	<div style="display:none;" id="update">
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
					<input type="text" size="2" class="card-expiry-month"/><span> / </span><input type="text" size="4" class="card-expiry-year"/>
				</p>
			</fieldset>
		</form>
					
		<a id="submit" class="button_link" href="#">Update Payment Info</a><br /><br />
		
		<img id="loading" style="display:none;" src="<?= $central->global_assets ?>images/ajax.gif" alt="loading..." />
		<p style="color:red;" id="payment_errors"></p>
		
	</div>

	<div style="display:none;" id="stripe_pub_key"><?= $this->config->item('stripe_key_' . ((MODE == 'local') ? 'test' : 'live' ) . '_public') ?></div>
	
</div>