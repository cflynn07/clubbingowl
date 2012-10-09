<li style="text-align: left;">
  <div class="avatar">
  	
  	<%= image_insert(pic_square, {alt: 'User'}) %>
	      
  </div>
  <div class="info">
    
    <h2>
    	<%
    		var link 	= inline_link('friends/' + third_party_id, u_full_name, {});
    	%>
    	<%= link %>
    </h2>
    <p><?= $this->lang->line('p-friend_news_item') ?></p>
    
  </div>
</li>