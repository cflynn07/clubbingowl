<%

	var cn_notes = window.module.Globals.prototype.front_link_base + 'admin/' + ((window.location.href.indexOf('admin/managers') != -1) ? 'managers/' : 'promoters/') + 'clients/';

	var type, image, name, head_user, supplied_name, request_message, response_message, host_notes;
	
	if(typeof pglr_id !== 'undefined'){
		type				= 'promoter';
		image 				= pgla_image;
		name				= pgla_name;
		head_user 			= pglr_user_oauth_uid;
		supplied_name		= pglr_supplied_name;
		request_message		= pglr_request_msg;
		response_message	= pglr_response_msg;
		host_notes		 	= pglr_host_message;
	}else{
		type				= 'team';
		image 				= tgla_image;
		name				= tgla_name;
		head_user 			= tglr_user_oauth_uid;
		supplied_name		= tglr_supplied_name;
		request_message		= tglr_request_msg;
		response_message	= tglr_response_msg;
		host_notes		 	= tglr_host_message;
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
			<td>Guest</td>
			<td>
				
				<% if(head_user && head_user != 'null'){ %>
					<img style="vertical-align:top;" src="https://graph.facebook.com/<%= head_user %>/picture" />
					
					<a class="ajaxify" href="<%= cn_notes + head_user + '/' %>">
						<span data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span>
					</a>
				
				<% }else{ %>
					<img style="vertical-align:top;" src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
					<span><%= supplied_name %></span>
				<% } %>
				
			</td>
		</tr>
		
		
		
		<tr>
			<td>Promoter</td>
			<td>
				<% if(type == 'promoter'){ %>
					
					<img style="vertical-align:top;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + up_profile_image + '_t.jpg' %>" / alt="<%= u_full_name %>">
					<span><%= u_full_name %></span>
										
				<% }else{ %>
					
					<span> --- </span>
					
				<% } %>
			</td>
		</tr>
		
		
		
		
		<tr>
			<td>Guest List</td>
			<td>
				
				<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + image + '_t.jpg' %>" alt="<%= name %>">
				<%= name %>
				
			</td>
		</tr>
		
		<tr>
			<td>Messages</td>
			<td>
				<table style="width:100%; margin:0;">
					<tr>
						<td style="background:#333 !important; color:#FFF; vertical-align:middle !important; text-align:center; padding:5px 0 5px 0;">Request Message</td>
					</tr>
					<tr>
						<td style="font-weight:normal;"><%= ((request_message) ? request_message : '---') %></td>
					</tr>
					<tr>
						<td style="background:#333 !important; color:#FFF; vertical-align:middle !important; text-align:center; padding:5px 0 5px 0;">Response Message</td>
					</tr>
					<tr>
						<td style="font-weight:normal;"><%= ((response_message) ? response_message : '---') %></td>
					</tr>
					<tr>
						<td style="background:#333 !important; color:#FFF; vertical-align:middle !important; text-align:center; padding:5px 0 5px 0;">Host Notes</td>
					</tr>
					<tr>
						<td style="font-weight:normal;"><%= ((host_notes) ? host_notes : '---') %></td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<tr>
			<td>Entourage</td>
			<td>

				<% if(typeof entourage !== 'undefined' && entourage.length > 0){ %>
					
					<table style="margin:0;">
						<tbody>
							
							
							<% for(var i in entourage ){ %>
								<% var user = entourage[i]; %>
								
								<tr>
								
									<% if(type == 'team'){ %>
										
										<% if(user.tglre_oauth_uid == null){ %>
											<td style="padding-bottom:0;">
												<img style="vertical-align:top;" src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
											</td>
											<td style="padding-bottom:0;">
												<%= user.tglre_supplied_name %>
											</td>
										<% }else{ %>
											<td style="padding-bottom:0;">
												<img style="vertical-align:top;" src="https://graph.facebook.com/<%= user.tglre_oauth_uid %>/picture" />
											</td>
											<td style="padding-bottom:0;">
												<a class="ajaxify" href="<%= cn_notes + user.tglre_oauth_uid + '/' %>">
													<span data-oauth_uid="<%= user.tglre_oauth_uid %>" data-name="<%= user.tglre_oauth_uid %>"></span>
												</a>												
											</td>
										<% } %>
										
									<% }else{ %>
										
										<% if(user.pglre_oauth_uid == null){ %>
											<td style="padding-bottom:0;">
												<img style="vertical-align:top;" src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
											</td>
											<td style="padding-bottom:0;">
												<%= user.pglre_supplied_name %>
											</td>
										<% }else{ %>
											<td style="padding-bottom:0;">
												<img style="vertical-align:top;" src="https://graph.facebook.com/<%= user.pglre_oauth_uid %>/picture" />
											</td>
											<td style="padding-bottom:0;">
												<a class="ajaxify" href="<%= cn_notes + user.pglre_oauth_uid + '/' %>">
													<span data-oauth_uid="<%= user.pglre_oauth_uid %>" data-name="<%= user.pglre_oauth_uid %>"></span>
												</a>
											</td>
										<% } %>							
										
									<% } %>
								
								</tr>
								
							<% } %>
							
							
						</tbody>
					</table>
					
				<% }else{ %>
					<p>No Entourage</p>
				<% } %>
			</td>
		</tr>
		
	</tbody>
</table>