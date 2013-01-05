<%

	var type, image, name, head_user, supplied_name;

	if(typeof pglr_id !== 'undefined'){
		type			= 'promoter';
		image 			= pgla_image;
		name			= pgla_name;
		head_user 		= pglr_user_oauth_uid;
		supplied_name	= pglr_supplied_name;
	}else{
		type			= 'team';
		image 			= tgla_image;
		name			= tgla_name;
		head_user 		= tglr_user_oauth_uid;
		supplied_name	= tglr_supplied_name;
	}

%>


<div class="ui-widget-header">
	<span>Table Reservation</span>
	<span data-actions="remove" style="float:right; color:red; text-decoration:underline;">close (x)</span>
</div>


<img style="margin-top:25px;" id="reservation_loading_indicator" src="<%= window.module.Globals.prototype.global_assets %>images/ajax.gif" />

<table id="reservation_info">
	<tbody>
		
		
		
		<tr>
			<td>Head User</td>
			<td>
				
				<% if(head_user && head_user != 'null'){ %>
					<img src="https://graph.facebook.com/<%= head_user %>/picture" />
					<span data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span>
				<% }else{ %>
					<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
					<span><%= supplied_name %></span>
				<% } %>
				
			</td>
		</tr>
		
		
		
		<tr>
			<td>Promoter</td>
			<td>
				<% if(type == 'promoter'){ %>
					
					<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + up_profile_image + '_t.jpg' %>" / alt="<%= u_full_name %>">
					<span><%= u_full_name %></span>
										
				<% }else{ %>
					---
				<% } %>
			</td>
		</tr>
		
		
		
		
		<tr>
			<td></td>
			<td>
				
				<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + image + '_t.jpg' %>" alt="<%= name %>">
				
			</td>
		</tr>
		
		
		
		<tr>
			<td></td>
			<td><p><%= name %></p></td>
		</tr>
	</tbody>
</table>