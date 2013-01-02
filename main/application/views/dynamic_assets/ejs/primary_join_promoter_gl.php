<li>
	
	<table style="width:100%; margin-bottom:10px; margin-top:-5px;">
		<tr>
			<td style="vertical-align:top;">
				<div class="avatar venue-image" style="height:52px; border-radius:6px;">
					<%
						var image 	= image_insert(pic_square, {alt: 'User', style: 'border-radius:5px;'});
						var link 	= inline_link('friends/' + u_third_party_id, image, {alt: 'User'})
					%>
					<%= link %>
				</div>
			</td>
			<td style="vertical-align:top;">
				<div class="info">
			  	
			  		<table>
			  			<tr>
			  				<td colspan="2">
			  					<%= inline_link('friends/' + u_third_party_id, '<h2>' + u_full_name + '</h2>', {}) %>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td style="min-width:45px;vertical-align:top;">
			  					<% 
						    		var data = jQuery.parseJSON(un_notification_data); 
						    		if(data.pgla_image){ 
						    	%>
						    		<% var path = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + data.pgla_image + '_t.jpg'; %>
						    		<% var image =  image_insert(path, {}) %>
						    		<%= inline_link('promoters/' + guest_list_data.up_public_identifier + '/guest_lists/' + pi_link_convert(guest_list_data.pgla_name), image, {class: 'list'}) %>
						    	<% } %>
			  				</td>
			  				<td style="vertical-align:top;">
			  					<p style="margin:0;"><?= $this->lang->line('ha-jpgl_m') ?></p>
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