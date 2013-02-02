<div class="ui-widget-header">
	<span>Filter Options</span>
</div>
<div class="ui-widget-content">
	
	<h2>Date Range:</h2>
	<div>
		<label>From:</label><br/>
		<input name="start_date" type="text" />
		<label>To:</label><br/>
		<input name="end_date" 	 type="text" />
	</div>
	<br/>
	
	
	<h2>Promoters</h2>
	<table style="margin:0;">
		<tbody>
		<% for(var i in promoters){ var p = promoters[i]; %>
			
			<tr>
				<td style="vertical-align:top;">
					<input data-up_id="<%= p.up_id %>" type="checkbox" class="iphone" checked>
				</td>
				<td>
					<img style="height:60px; border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + p.up_profile_image + '_t.jpg' %>" />
					<p><%= p.u_full_name %></p>
				</td>
			</tr>
		
		<% } %>
		</tbody>
	</table>
	
	<h2>Venues</h2>
	<table style="margin:0;">
		<tbody>
		<% for(var i in team_venues){ var tv = team_venues[i]; %>
			
			<tr>
				<td style="vertical-align:top;">
					<input data-tv_id="<%= tv.tv_id %>" type="checkbox" class="iphone" checked>
				</td>
				<td>
					<img style="width:100px; border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'venues/banners/' + tv.tv_image + '_t.jpg' %>" />
					<p><%= tv.tv_name %></p>
				</td>
			</tr>
		
		<% } %>
		</tbody>
	</table>
	
</div>