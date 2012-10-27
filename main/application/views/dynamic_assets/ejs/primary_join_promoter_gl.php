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
  		
    	<% 
    		var data = jQuery.parseJSON(un_notification_data); 
    		if(data.pgla_image){ 
    	%>
    		<% var path = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + data.pgla_image + '_t.jpg'; %>
    		<% var image =  image_insert(path, {}) %>
    		<%= inline_link('promoters/' + data.c_url_identifier + '/' + guest_list_data.up_public_identifier + '/guest_lists/' + pi_link_convert(guest_list_data.pgla_name), image, {class: 'list'}) %>
    	<% } %>
    	<p><?= $this->lang->line('ha-jpgl_m') ?></p>
  	</div>
  	<div class="time">
  		<p class="news_item_date"><%= occurance_date %></p>
  	</div>
</li>