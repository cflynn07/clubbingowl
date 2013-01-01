<?php 
	$page_obj = new stdClass;
	$page_obj->clients 			= $clients;
	$page_obj->client 			= $client;
	$page_obj->users			= $data->users;
	$page_obj->oauth_uid		= $oauth_uid;
?>
<?php Kint::dump($data); ?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<div id="clients_individual_wrapper" class="full_width last">
	
	<div class="ui-widget">
		
		<div class="ui-widget-header">
			<span data-client-name=""></span>
		</div>
		
		<div style="width:100%;" class="">
			
			<div style="min-height:200px;" class="one_fifth">
				<img style="border:1px solid #CCC;" id="client_pic" />
			</div>
			<div class="four_fifth last">
				
				<div class="ui-widget-header">
					<span>Client Information</span>
				</div>
				
				<table class="normal" style="width:100%;">
					<tbody>
						<tr>
							<td class="key">Phone Number:</td>
							<td data-phone-number=""></td>
						</tr>
						<tr>
							<td class="key">Email:</td>
							<td data-email=""></td>
						</tr>
						
						<?php if($client === false): ?>
							
							<tr>
								<td colspan="2"><p style="color:red; margin:0;"><span data-client-name=""></span> has not joined one of your guest-lists. You will not be able to see his/her email address and phone number until then.</p></td>
							</tr>
							
						<?php endif; ?>
						
					</tbody>
				</table>
			</div>
			
		</div>
		
		<hr style="border:1px solid #CCC; "/>
				
				
				
		<div style="width:45%; float:left;" class="ui-widget one_half">
			
			<div class="ui-widget-header">
				<span>My Notes</span>
			</div>
			
			<div style="clear:both;" >
				<h2 style="color:#000; text-decoration:underline;">My Private Notes</h2>
				<p class="subnote">These notes are private to you and will not be shared with your manager or other members of your team.</p>
				<textarea id="private_notes" class="notes"><?= ($data->my_client_notes) ? $data->my_client_notes->private_notes : '' ?></textarea>
			</div>
			<a href="#" style="float:right; margin-right:13px;" data-action="save" class="button_link btn-action">Save</a>	
			<img style="display:none; float:right; margin-right:13px;" class="loading_indicator" src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
			
			
			
			
			<div style="clear:both;">
				<h2 style="color:#000; text-decoration:underline;">My Public Notes</h2>
				<p class="subnote">These notes are public. Share information about this client with your managers and other promoters on your team.</p>
				<textarea id="public_notes" class="notes"><?= ($data->my_client_notes) ? $data->my_client_notes->public_notes : '' ?></textarea>
			</div>
			<a href="#" style="float:right; margin-right:13px;" data-action="save" class="button_link btn-action">Save</a>	
			<img style="display:none; float:right; margin-right:13px;" class="loading_indicator" src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
	
	
	
		</div>
				
		
		
		
		<div style="width:45%; float:right;" class="ui-widget one_half last">
			
			<div class="ui-widget-header">
				<span>Team Notes</span>
			</div>
			
			<?php if(!$data->client_notes_team): ?>
				
				<p>No team members have created any notes on <span data-client-name=""></span> yet.</p>
				
			<?php endif; ?>
			
			
			<?php foreach($data->client_notes_team as $cnt): ?>
				
				<div class="full_width last" style="clear:both;">
					<table style="border-bottom:1px dashed #CCC; width:100%;">
						<tr>
							<td style="width:50px;margin-right:10px;">
								<img style="border:1px solid #CCC;" src="https://graph.facebook.com/<?= $cnt->user_oauth_uid ?>/picture" alt="" />
							</td>
							<td style="vertical-align:top; padding-left:5px;">
								<h4 style="margin-bottom:5px;" data-name="<?= $cnt->user_oauth_uid ?>"></h4>
								<p style=""><?= $cnt->public_notes ?></p>
							</td>
						</tr>
					</table>
				</div>		
				
			<?php endforeach; ?>
			
		</div>





		<?php if(false): ?>
		
		<div class="full_width last" style="clear:both;">
			<h2>History</h2>
		</div>
		
		<?php endif; ?>
	
	
	<div style="clear:both;"></div>	
	</div>
		
</div>