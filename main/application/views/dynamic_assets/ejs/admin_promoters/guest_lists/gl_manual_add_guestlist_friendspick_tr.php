<td>
	<% if(oauth_uid){ %>
		<img src="https://graph.facebook.com/<%= oauth_uid %>/picture?width=50&height=50" alt="" />
	<% }else{ %>
		<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" alt="" />
	<% } %>
</td>
<td style="vertical-align:middle; padding-left:10px;">
	<%= name %>
</td>
<% if(!head_user){ %>
<td>
	<a href="#" data-action="delete-entourage-user" class="button_link btn-action">Delete</a>	
</td>
<% } %>