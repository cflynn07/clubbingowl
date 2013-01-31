<li style="overflow:visible; clear:both; width:100%;">
	
	<% if(is_app_user){ %>
		
		<a href="<%= window.module.Globals.prototype.front_link_base %>friends/<%=third_party_id%>/">
        	<%= image_insert(pic_square, {alt: (name + '\'s Avatar'), class: 'venue-image', style: 'float:left;'}) %>
        	<span style="float:left; margin:4px 0 0 10px; text-decoration:underline;">
        		<%= name %>
        	</span>
        </a>
		
	<% }else{ %>
		
		<a data-oauth_uid="<%= '' %>" href="javascript: void(0);">
        	<%= image_insert(pic_square, {alt: name + '\'s Avatar', class: 'venue-image', style: 'float:left;'}) %>
        	
        	<span style="float:left; margin:4px 0 0 10px;">
        		
        		<span style="float:left;"><%= name %></span>
        		<div style="clear:both;"></div>
				<span data-invite-oauth_uid="<%= uid %>" style="margin:0; padding-left:10px; background-image:url('/vcweb2/assets/web/images/ClubbingOwlInviteButton.png');" href="javascript: void(0);" class="invite">Invite</span>
			   
        	</span>
 	
        </a>
		
	<% } %>
		
</li>