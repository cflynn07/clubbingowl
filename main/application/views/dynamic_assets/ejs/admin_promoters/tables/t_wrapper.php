<h3>"<%= tv_name %>" Table Layout</h3>
			
<% if(display_slider){ %>	
	<div class="ui2">
		<div data-function="tv_size_slider" id="slider-<%= tv_id %>"></div>
	</div>
<% } %>

<div id="layout"></div>