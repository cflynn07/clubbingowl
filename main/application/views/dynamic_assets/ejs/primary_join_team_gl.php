<li>
	
	<table style="width:100%; margin-bottom:10px; margin-top:-5px;">
		<tr>
			<td style="vertical-align:top; width:50px;">
				
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
			  					<%= inline_link('friends/' + u_third_party_id, '<h2 style="margin:0;">' + u_full_name + '</h2>', {}) %>
			  				</td>
			  			</tr>
			  			<tr>		  				
			  				<td style="min-width:45px; vertical-align:top;">
			  					<% 
						    		var data = jQuery.parseJSON(un_notification_data); 
						    		if(data.tgla_image){ 
						    	%>
						    	
						    	
						    	<%
				  					var guest_list_name = '<a class="ajaxify_t3" href="' + window.module.Globals.prototype.front_link_base + 'venues/' + guest_list_data.c_url_identifier + '/' + pi_link_convert(guest_list_data.tv_name) + '/guest_lists/' + pi_link_convert(guest_list_data.tgla_name) + '/">' + guest_list_data.tgla_name + '</a>';
				  					var venue_name 		= '<a class="ajaxify_t3" href="' + window.module.Globals.prototype.front_link_base + 'venues/' + guest_list_data.c_url_identifier + '/' + pi_link_convert(guest_list_data.tv_name) + '/">' + guest_list_data.tv_name + '</a>';
				  				%>
				  				
						    		<% var path = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + data.tgla_image + '_t.jpg'; %>
						    		<% var image =  image_insert(path, {}) %>
						    		<%= inline_link('venues/' + guest_list_data.c_url_identifier + '/' + pi_link_convert(guest_list_data.tv_name) + '/guest_lists/' + pi_link_convert(guest_list_data.tgla_name), image, {class: 'list'}) %>
						    	
						    	<% } %>
			  				</td>
			  				<td style="vertical-align:top;">
			  					<p style="margin:0;"><?= $this->lang->line('ha-jtgl_m') ?></p>
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