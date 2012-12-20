<span style="color: red; font-weight: bold;"><%= day_title %></span><br>

<% for(var i=0; i < day_lists.length; i++){ %>
	
	<% var day_list = day_lists[i]; console.log(day_list); %>
		
	<li style="margin-left:15px;text-decoration:none;">
		<span data-tgla_id="<%= day_list.tgla_id %>" style="text-decoration:underline;"><%= day_list.tgla_name %></span> (<span class="wgl_groups_count"><%= (typeof day_list.current_list.groups !== 'undefined') ? day_list.current_list.groups.length : 0 %></span>)
	</li><br>
<% } %>