<hr>

<span style="color: red; font-weight: bold;">Events</span><br>

<% for(var i=0; i < events.length; i++){ %>
	<li style="margin-left:15px;text-decoration:none;">
		<span data-pgla_id="<%= events[i].pgla_id %>" style="text-decoration:underline;"><%= events[i].pgla_name + ' [' + events[i].pgla_event_date + ']' %></span> (<span class="wgl_groups_count"><%= events[i].groups.length %></span>)
	</li><br>
<% } %>