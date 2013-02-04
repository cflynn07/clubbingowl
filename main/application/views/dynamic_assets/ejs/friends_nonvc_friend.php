<li style="margin-bottom:30px;">
    <div class="avatar">
    	<a style="background:none; overflow:visible; margin:0;" data-uid="<%= uid %>" href="javascript: void(0);" class="invite">
    		<%=image_insert('https://graph.facebook.com/' + uid + '/picture?type=large&width=100&height=100', {alt: name + '\'s Avatar', class: 'venue-image', style: 'padding-top:0; margin:0;'})%>
    	</a>
    </div>
    <span style="margin-top:5px;" class="name"><%= name %></span>
    <a style="margin-top:5px;" data-uid="<%= uid %>" href="javascript: void(0);" class="invite">Invite</a>
</li>