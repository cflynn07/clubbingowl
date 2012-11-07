<li>
	
	<table style="width:100%;">
		<tr>
			<td style="vertical-align:top; max-width:50px;">
				
				<div class="avatar">
					<%
						var image 	= image_insert(pic_square, {alt: 'User'});
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
				
			  	<p class="news_item_date"><%= occurance_date %></p>
			  	
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
    	<p><?= $this->lang->line('ha-jvc_m') ?></p>
  	</div>
  	
  	<div class="time">
  		<p class="news_item_date"><%= occurance_date %></p>
  	</div>
  	<?php endif; ?>
  	
  	
</li>