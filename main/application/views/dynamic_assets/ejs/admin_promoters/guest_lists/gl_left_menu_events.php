<hr>

<span style="color: red; font-weight: bold;">Upcoming Events</span><br>

<% for(var i=0; i < events.length; i++){ if(events[i].upcoming === false){ continue; } %>
	<li style="margin-left:15px;text-decoration:none;">
		<span data-pgla_id="<%= events[i].pgla_id %>" style="text-decoration:underline;"><%= events[i].pgla_name %></span> (<span class="wgl_groups_count"><%= events[i].groups.length %></span>)
	</li><br>
<% } %>

<span style="color: red; font-weight: bold;">Past Events</span><br>

<% for(var i=0; i < events.length; i++){ if(events[i].upcoming === true){ continue; } %>
	<li style="margin-left:15px;text-decoration:none;">
		<span data-pgla_id="<%= events[i].pgla_id %>" style="text-decoration:underline;"><%= events[i].pgla_name %></span> (<span class="wgl_groups_count"><%= events[i].groups.length %></span>)
	</li><br>
<% } %>