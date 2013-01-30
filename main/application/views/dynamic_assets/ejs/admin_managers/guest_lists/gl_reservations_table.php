
<div>
	<p style="margin:0;"><strong>Public Location</strong>: 
		<a target="_new" href="<%= window.module.Globals.prototype.front_link_base + 'venues/' + c_url_identifier + '/' + tv_name.replace(/ /g, '_') + '/guest_lists/' + tgla_name.replace(/ /g, '_') + '/#guestlist' %>">
			<%= window.module.Globals.prototype.front_link_base + 'venues/' + c_url_identifier + '/' + tv_name.replace(/ /g, '_') + '/guest_lists/' + tgla_name.replace(/ /g, '_') + '/' %>
		</a>
	</p>
	<p style="margin:0;"><strong>Facebook Location</strong>: 
		<a target="_new" href="https://www.facebook.com/pages/@/<%= tv_team_fan_page_id + '?sk=app_' + '<?= $this->config->item('facebook_app_id') ?>' %>">
			https://www.facebook.com/pages/@/<%= tv_team_fan_page_id + '?sk=app_' + '<?= $this->config->item('facebook_app_id') ?>' %>
		</a>
	</p>
</div><br/>


<div class="ui-widget-header">
	<span>"<%= tgla_name %>" @ <span style="font-weight: bold;"><%= tv_name %></span></span>
	<span style="float:right;">
		<input type="text" class="guest_list_datepicker" value="<%= human_date %>" style="height:10px; margin-right:-5px;"/>
	</span>
</div>	

<br/>
<a href="#" data-action="expand-collapse-all" class="button_link btn-action">Expand/Collapse All</a>

<% if(current_week){ %>
	<a href="#" data-action="manually-add" class="button_link btn-action">Manually Add Clients</a>
<% }else{ %>
	<a href="#" data-action="return-current-week" class="button_link btn-action">Return to Current Week</a>
<% } %>

<br/><br/>

<table class="normal tablesorter guestlists" style="width: 770px;">
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
