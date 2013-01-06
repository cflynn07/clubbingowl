<div id="floorplan_holder"></div>

<div style="padding:5px; margin-bottom:10px;">
	
	<table style="width:90%; margin-left:auto; margin-right:auto;">
		<tbody>
			<tr>
				<td style="vertical-align:top; padding-top:15px;">
					<div style="float:left; vertical-align:top;">
						<span id="table_assignment_message">Click on an available table to assign. Drag & Drop reservations to reorganize table assignments.</span>
					</div>
				</td>
				<td>
					<div style="float:right;">
						<span style="float:right; display:none;" id="assigned_table" style="float:right;">Assigned Table:<br/><div>None</div></span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
		
	<p style="text-align:center; width:100%; color:red; background:#000; font-size:14px;" id="message"></p>
		
	<?php if(false): ?>
	<h3 style="margin-bottom:5px;">Select Minimum Table Price for <%= venue.tv_name %> on <%= tgla_day.substr(0, 1).toUpperCase() + tgla_day.substr(1) %></h3>
	
	<select id="table_min_price">
		
		<% for(var i=0; i < day_prices.length; i++){ %>
			<option value="<%= day_prices[i] %>">$<%= day_prices[i] %></option>
		<% } %>
		
	</select>
	<?php endif; ?>
	
	
</div>

<a href="#" style="float:right;" data-action="init-gl-flow" class="button_link btn-action">Create Reservation</a>