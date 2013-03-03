<hr>

<span style="color: red; font-weight: bold;">Upcoming Events</span><br>

<% for(var i=0; i < events.length; i++){ if(events[i].upcoming === false){ continue; } %>
	<li style="margin-left:15px;text-decoration:none;">
		<span data-tgla_id="<%= events[i].tgla_id %>" style="text-decoration:underline;"><%= events[i].tgla_name %></span> (<span class="wgl_groups_count"><%= (typeof events[i].current_list.groups === 'undefined') ? '0' : events[i].current_list.groups.length %></span>)
	</li><br>
<% } %>

<span style="color: red; font-weight: bold;">Past Events</span><br>

<% for(var i=0; i < events.length; i++){ if(events[i].upcoming === true){ continue; } %>
	<li style="margin-left:15px;text-decoration:none;">
		<span data-tgla_id="<%= events[i].tgla_id %>" style="text-decoration:underline;"><%= events[i].tgla_name %></span> (<span class="wgl_groups_count"><%= (typeof events[i].current_list.groups === 'undefined') ? '0' : events[i].current_list.groups.length %></span>)
	</li><br>
<% } %>