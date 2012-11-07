<li>
	
	
	<table style="width:100%; margin-bottom:10px; margin-top:-5px;">
		<tr>
			<td style="vertical-align:top;">
				
				<div class="avatar">
					<%
						var image 	= image_insert(pic_square, {alt: 'User'});
						var link 	= inline_link('friends/' + u_third_party_id, image, {alt: 'User'})
					%>
					<%= link %>
				</div>
				
			</td>
			<td style="vertical-align:top;">
				
				
				<div class="info">
			  	
			  		<table>
			  			<tr>
			  				<td>
			  					<%= inline_link('friends/' + u_third_party_id, '<h2 style="margin:0;">' + u_full_name + '</h2>', {}) %>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td style="min-width:45px;vertical-align:top;">
    							<p style="margin:0;"><?= $this->lang->line('ha-jtgl_m') ?></p>
			  				</td>
			  			</tr>
			  		</table>  		
			  	
			  	</div>
			  	
			  	
			</td>
			
			<td style="vertical-align:top;min-width:60px;">
				
				
				<div class="time">
			  		<p class="news_item_date"><%= occurance_date %></p>
			  	</div>
			  	
			  	
			</td>
		</tr>
	</table>
	
	
	
	
	
	
	
	
	
	
	<?php if(false): ?>
	<div class="avatar">
		<%
			var image 	= image_insert(pic_square, {alt: 'User'});
			var link 	= inline_link('friends/' + u_third_party_id, image, {alt: 'User'})
		%>
		<%= link %>
	</div>
	
  	<div class="info">
    	<%= inline_link('friends/' + u_third_party_id, '<h2>' + u_full_name + '</h2>', {}) %>
    	<p><?= $this->lang->line('ha-jtgl_m') ?></p>
  	</div>
    <div class="time">
  		<p class="news_item_date"><%= occurance_date %></p>
  	</div>
  	<?php endif; ?>
  	
</li>