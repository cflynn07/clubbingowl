<div class="tabs ui-tabs ui-widget ui-widget-content ui-corner-all" style="width:1050px;">
		
	<div class="ui-widget-header">
		<span>Guest Lists</span>
		
		<select id="venue_select" style="float:right;" class="venue_select">
			<% for(var i in team_venues){ %>
				<option value="<%= team_venues[i].tv_id %>"><%= team_venues[i].tv_name %> (<span class="team_gl_groups_count"><%= '0' %></span>)</option>
			<% } %>
		</select>
		<span style="float:right;">Select Venue: </span>
		
	</div>
	
	<div id="active_venue_wrapper" class="ui-widget-content"></div>
	
</div>