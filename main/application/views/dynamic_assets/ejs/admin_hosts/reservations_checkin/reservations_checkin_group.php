<div style="background:#474D6A; cursor:pointer !important; " data-top_min class="ui-widget-header ui-corner-all">
	
	<span data-iphone_font style="font-size:16px; text-shadow:none; color:#FFF; text-align:center; width:100%;">		
			
		<span style="float:left; position:relative; top:3px;" class="ui-icon ui-icon-circle-triangle-n"></span>
			
		<% if(typeof pglr_id !== 'undefined'){ %>
			
			<%= u_full_name %>
			
		<% }else{ %>
		
			House Guest List
		
		<% } %>
	
		<span style="float:right; position:relative; top:3px;" class="ui-icon ui-icon-circle-triangle-n"></span>
	
	</span>
	
</div>
	
	

<table style="width:100%;" class="normal reservations_holder">
	<thead>
		<tr>
			<th>Status</th>
			<th>Data</th>
			<th>Head User</th>
			<th data-iphone_hide>Guest List</th>
			<th data-iphone_hide>Messages</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>