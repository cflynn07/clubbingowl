<li>
		
	<table style="width:100%; margin-bottom:10px; margin-top:-5px;">
		<tr>
			<td style="vertical-align:top; width:50px;">
				
				
				
				<div class="avatar venue-image" style="height:72px;border-radius:6px;">
					<%
						var image 	= image_insert(window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + un_notification_data.tgla.tgla_image + '_t.jpg', {alt: 'Guest List Banner', style: 'max-width:50px; border-radius:5px;', class: 'ajaxify_t3'});
						var link 	= inline_link('venues/' + un_notification_data.tgla.c_url_identifier + '/' + un_notification_data.tgla.tv_name.replace(/ /g, '_') + '/guest_lists/' + un_notification_data.tgla.tgla_name.replace(/ /g, '_'), image, {alt: 'User', class: 'ajaxify_t3'})
					%>
					<%= link %>
				</div>
				
				
				
			</td>
			<td style="vertical-align:top;">
				
				<div class="info">
			  	
			  		<table>
			  			<tr>
			  				<td>
			  					<%= inline_link('venues/' + un_notification_data.tgla.c_url_identifier + '/' + un_notification_data.tgla.tv_name.replace(/ /g, '_') + '/guest_lists/' + un_notification_data.tgla.tgla_name.replace(/ /g, '_'), '<h2 style="margin:0;">' + un_notification_data.tgla.tgla_name + '</h2>', {class: 'ajaxify_t3'}) %>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td style="min-width:45px;vertical-align:top;">
    							<p style="margin:0; padding:0;"><%= un_notification_data.tgla.tv_name %> updated the status of "<%= un_notification_data.tgla.tgla_name %>"</p><br/>
    							<p style="margin:0; font-weight: 400; font-size: 19px; padding-left:0; padding-bottom:0; color:#474D6A; border-bottom:1px solid #CCC;"><%= un_notification_data.status %></p>
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