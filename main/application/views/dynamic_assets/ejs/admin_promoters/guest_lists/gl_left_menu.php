<span style="color: red; font-weight: bold;"><%= day_title %></span><br>

<% for(var i=0; i < day_lists.length; i++){ %>
	
	<% var day_list = day_lists[i]; %>
		
	<li style="margin-left:15px;text-decoration:none;">
		<span data-pgla_id="<%= day_list.pgla_id %>" style="text-decoration:underline;"><%= day_list.pgla_name %></span> (<span class="wgl_groups_count"><%= day_list.groups.length %></span>)
	</li><br>
<% } %>