<?php 
	$page_obj = new stdClass;
	$page_obj->clients 			= $clients;
	$page_obj->client 			= $client;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<div id="clients_individual_wrapper" class="full_width last">
	
	<h1 data-client-name=""></h1>
	
	<div class="ui-widget">
		
		<div style="min-height:200px;" class="one_fifth">
			<img id="client_pic" />
		</div>
		<div class="four_fifth last">
			<h2>Info</h2>
			
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
				</tbody>
			</table>
			
		</div>
		
		<div class="full_width last" style="clear:both;" >
			<h2>My Private Notes</h2>
			<p class="subnote">These notes are private to you and will not be shared with your manager or other members of your team.</p>
			<textarea id="private_notes" class="notes"></textarea>
		</div>
		
		<div class="full_width last" style="clear:both;">
			<h2>My Public Notes</h2>
			<p class="subnote">These notes are public. Share information about this client with your managers and other promoters on your team.</p>
			<textarea id="public_notes" class="notes"></textarea>
		</div>
		
		
		<hr>
		
		<div>
			
			<h1>Team Notes on <span data-client-name=""></span></h1>
			
			<div class="full_width last" style="clear:both;">
				<h2>Johann Barlach's Notes</h2>
				<p style="border:1px dashed #CCC;">Welllllcome</p>
			</div>
		</div>

		
		
		<div class="full_width last" style="clear:both;">
			<h2>History</h2>
		</div>
		
	</div>
	
</div>