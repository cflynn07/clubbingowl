<li>
		
	<table style="width:100%; margin-bottom:10px; margin-top:-5px;">
		<tr>
			<td style="vertical-align:top; width:50px;">
				
				<div class="avatar">
					<%
						var image 	= image_insert(window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + un_notification_data.pgla.up_profile_image + '_t.jpg', {alt: 'Promoter Profile Picture', style: 'max-width:50px;', class: 'ajaxify_t3'});
						var link 	= inline_link('promoters/' + un_notification_data.pgla.up_public_identifier + '/guest_lists/' + un_notification_data.pgla.pgla_name.replace(' ', '_'), image, {alt: 'User', class: 'ajaxify_t3'})
					%>
					<%= link %>
				</div>
				
			</td>
			<td style="vertical-align:top;">
				
				<div class="info">
			  	
			  		<table>
			  			<tr>
			  				<td>
			  					<%= inline_link('promoters/' + un_notification_data.pgla.up_public_identifier + '/guest_lists/' + un_notification_data.pgla.pgla_name.replace(' ', '_'), '<h2 style="margin:0;">' + un_notification_data.pgla.pgla_name + '</h2>', {class: 'ajaxify_t3'}) %>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td style="min-width:45px;vertical-align:top;">
    							<p style="margin:0; padding:0;"><%= un_notification_data.pgla.u_full_name %> updated the status of "<%= un_notification_data.pgla.pgla_name %>"</p>
    							<p style="margin:0; font-weight: 400; font-size: 19px; padding-left: 10px;"><%= un_notification_data.status %></p>
			  				</td>
			  			</tr>
			  		</table>  		
			  	
			  	</div>
			  	
			</td>
			
			<td style="vertical-align:top;min-width:60px;">
				
				
				<div class="time">
			  		<p style="white-space: nowrap;" class="news_item_date"><%= occurance_date %></p>
			  	</div>
			  	
			  	
			</td>
		</tr>
	</table>

</li>