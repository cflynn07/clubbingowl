<li>
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
</li>