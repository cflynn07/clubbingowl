<div id="floorplan_holder"></div>

<div style="border-radius:10px; background:#CCC; padding:5px; margin-bottom:10px;">
	
	<h3 style="margin-bottom:5px;">Select Minimum Table Price for <%= venue.tv_name %> on <%= tgla_day.substr(0, 1).toUpperCase() + tgla_day.substr(1) %></h3>
	
	<select id="table_min_price">
		
		<% for(var i=0; i < day_prices.length; i++){ %>
			<option value="<%= day_prices[i] %>">$<%= day_prices[i] %></option>
		<% } %>
		
	</select>
	
</div>

<a href="#" style="float:right;" data-action="init-gl-flow" class="button_link btn-action">Create Reservation</a>	