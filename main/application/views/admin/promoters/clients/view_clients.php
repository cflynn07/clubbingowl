<?php 
	$page_obj = new stdClass;
	$page_obj->clients 			= $clients;
	$page_obj->users_oauth_uid 	= $users_oauth_uid;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<h1>ClubbingOwl Clients</h1>
<p>View & Manage your ClubbingOwl Client Database</p>



<div class="full_width last" style="width:1050px;">
	
	
	
	
	<div id="all_clients" class="full_width last">
		
		<h3>All Clients</h3>
		
		<div style="display:none;" id="clients_export_hidden">
			<img style="vertical-align:middle;margin-right:5px;" src="<?= $central->admin_assets ?>images/icons/small_icons_3/Box_Download.png" alt="" /><span><a data-action="clients_export" href="#">Export Clients</a></span>
		</div>
			
		<p id="p_clients_export" 		style="display:none; margin-top:10px; margin-bottom:0px;">Copy & Paste CSV-formatted Client Database</p>
		<textarea id="clients_export" 	style="display:none; min-width:600px; min-height:200px; margin-top:10px; margin-bottom:10px;"></textarea>
		
		<br/>
		
		<img id="loading_indicator" src="<?= $central->global_assets ?>images/ajax.gif" alt="loading..." />
		<table style="width:100%; display:none; margin-bottom:5px;" class="normal">
			<thead>
				<tr>
					<th>Name</th>
					<th>Gender</th>
					<th>Friend Status</th>
					<th>Phone Number</th>
					<th>Email</th>
				</tr>
			</thead>
		</table>

	</div>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<?php if(false): ?>
		
	<div class="one_half">
		
		<h3>Top Referring Clients</h3>
		
		<div class="ui-widget" style="width:100%;">
			<div class="ui-widget-header">
				<span>Referring Clients</span>
			</div>
			
			<div class="ui-widget-content">
				<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			</div>
			
		</div>
	</div>
	
	<div class="one_half last">
		
		<h3>Top Guest-List Clients</h3>
		
		<div class="ui-widget" style="width:100%;">
			<div class="ui-widget-header">
				<span>Guest List Clients</span>
			</div>
			
			<div class="ui-widget-content">
				<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			</div>
			
		</div>
		
	</div>
	
	
	
	<div style="clear:both"></div>
	<hr>
	
	
	
	<div class="one_half">
		
		<h3>Top Table Reservation Clients</h3>
		
		<div class="ui-widget" style="width:100%;">
			<div class="ui-widget-header">
				<span>Table Reservation Clients</span>
			</div>
			
			<div class="ui-widget-content">
				<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			</div>
		</div>
	</div>
	
	<div class="one_half last">
		
		<h3>Top Spending Clients</h3>
		
		<div class="ui-widget" style="width:100%;">
			<div class="ui-widget-header">
				<span>Spending Clients</span>
			</div>
			
			<div class="ui-widget-content">
				<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			</div>
		</div>
		
	</div>
	
	
	<div style="clear:both"></div>
	<hr>
	
	
	<div class="full_width last">
		
		<h3>All Clients</h3>
		
		<div class="ui-widget-content">
			<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			
			<table id="all_clients" class="normal tablesorter stdtable">
				<thead>
					<tr>
						<th>Name</th>
						<th>Picture</th>
						<th>Gender</th>
						<th>Friend Status</th>
						<th>Clience Since</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($clients as $key => $cli): ?>
						<tr>
							<td><div class="name_<?= $cli ?>"></div></td>
							<td><div class="pic_square_<?= $cli ?>"></div></td>
							<td><div class="gender_<?= $cli ?>"></div></td>
							<td class="friend_status friend_status_<?= $cli ?> no_friend">
								<img class="loading_indicator_friend" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
							</td>
							<td></td>
						</td>
					<?php endforeach; ?>
				</tbody>
			</table>
			<style type="text/css">
			div.dataTables_wrapper{
				border: 1px solid #000;
				padding: 2px;
			}
			div.dataTables_wrapper td.uid{
				display: none;
			}
			div.dataTables_wrapper span.fb_friends{
				color: green;
			}
			div.dataTables_wrapper span.fb_no_friends{
				color: blue;
				cursor: pointer;
			}
			div.dataTables_wrapper div.dataTables_filter input{
				border: 1px solid #000;
			}
			table#all_clients{
				margin-left: auto;
				margin-right: auto;
				width: 99%;
				position: relative;
				top: 10px;
			}
			</style>
		</div>
		
	</div>	
	
	<?php endif; ?>
	
</div>