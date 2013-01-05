<li>
	
	<% if(is_app_user){ %>
		
		<a href="<%= window.module.Globals.prototype.front_link_base %>friends/<%=third_party_id%>/">
        	<%= image_insert(pic_square, {alt: name + '\'s Avatar'}) %>
        </a>
		
	<% }else{ %>
		
		<a href="javascript: void(0);">
        	<%= image_insert(pic_square, {alt: name + '\'s Avatar', class: 'venue-image'}) %>
        </a>
		
	<% } %>
		
</li>