<li>
	
	<table style="width:100%;">
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
				
				
				<%= inline_link('friends/' + u_third_party_id, '<h2 style="margin:0;">' + u_full_name + '</h2>', {}) %>
    			<p style="margin:0;margin-bottom:10px;"><?= $this->lang->line('ha-jvc_m') ?></p>
			  	
			  	
			</td>
			
			<td style="vertical-align:top;min-width:60px;">
				
			  	<p style="white-space: nowrap; margin:0;" class="news_item_date"><%= occurance_date %></p>
			  	
			</td>
		</tr>
	</table>
	
</li>