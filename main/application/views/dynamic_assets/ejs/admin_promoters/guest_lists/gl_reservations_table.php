
<div>
	<p><strong>Public Location</strong>: 
		<a target="_new" href="<%= window.module.Globals.prototype.front_link_base + 'promoters/' + window.page_obj.promoter.up_public_identifier + '/guest_lists/' + pgla_name.replace(/ /g, '_') + '/#guestlist' %>">
			<%= window.module.Globals.prototype.front_link_base + 'promoters/' + window.page_obj.promoter.up_public_identifier + '/guest_lists/' + pgla_name.replace(/ /g, '_') + '/' %>
		</a>
	</p>
</div>


<div class="ui-widget-header">
	<span style="font-size:14px;">"<%= pgla_name %>" @ <span style="font-weight: bold;"><%= tv_name %></span></span>
	
	
	<% if(pgla_event == '0'){ %>
		
		<span style="float:right;">
			<input type="text" class="guest_list_datepicker" value="<%= human_date %>" style="height:10px; margin-right:-5px;"/>
		</span>
		
	<% }else{ %> 
		
		<span style="float:right;">
			<span style="font-size:14px; height:10px; margin-right:-5px;"><%= pgla_event_date %></span>
		</span>
		
	<% } %>
	
	
	
</div>	

<br/>
<a href="#" data-action="expand-collapse-all" class="button_link btn-action">Expand/Collapse</a>



<% if(pgla_event == '0'){ %>
	
	<% if(current_week){ %>
		<a href="#" data-action="manually-add" class="button_link btn-action">Add Clients</a>
	<% }else{ %>
		<a href="#" data-action="return-current-week" class="button_link btn-action">Return to Current Week</a>
	<% } %>
	
<% }else{ %> 
	
	<% if(upcoming){ %>
		<a href="#" data-action="manually-add" class="button_link btn-action">Add Clients</a>
	<% } %>
	
<% } %>




<br/><br/>

<table class="normal tablesorter guestlists" style="width:100%;">
	<thead>
		<tr>
			<th>Guest</th>
			<th>Messages</th>
			<th>Table</th>
			<th>Status</th>
			<th>Entourage</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>
