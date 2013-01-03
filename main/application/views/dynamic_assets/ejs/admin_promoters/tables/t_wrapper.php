<h3>
	"<strong><%= tv_name %></strong>" Table Layout<% if(typeof floorplan_human_date !== 'undefined'){ %>
	<span style="color:#474D6A; font-size:22px; float:right; display:inline-block;"><%= floorplan_human_date %><% } %></span>
</h3>
			
<% if(display_slider){ %>	
	<div class="ui2">
		<div data-function="tv_size_slider" id="slider-<%= tv_id %>"></div>
	</div>
<% } %>

<div id="layout"></div>