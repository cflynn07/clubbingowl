<%
	
	var type,
		head_user,
		supplied_name,
		u_phone_number,
		request_message,
		response_message,
		host_notes,
		table_request,
		table_min_spend,
		guest_list_image,
		entourage,
		date;
	
	if(typeof pglr_id !== 'undefined'){
		
		type				= 'promoter';
		head_user 			= pglr_user_oauth_uid;
		supplied_name		= pglr_supplied_name;
		u_phone_number		= ''; //TODO
		request_message 	= pglr_request_msg;
		response_message 	= pglr_response_msg;
		host_notes			= pglr_host_message;	
		table_request		= pglr_table_request;
		table_min_spend		= ''; //TODO
		guest_list_image 	= pgla_image;
		guest_list_name		= pgla_name;
		entourage 			= entourage;
		date 				= pgl_date;
	
	}else{
		
		type				= 'team';
		head_user 			= tglr_user_oauth_uid;
		supplied_name		= tglr_supplied_name;
		u_phone_number		= ''; //TODO
		request_message 	= tglr_request_msg;
		response_message	= tglr_response_msg;
		host_notes			= tglr_host_message;
		table_request		= tglr_table_request;
		table_min_spend		= ''; //TODO
		guest_list_image 	= tgla_image;
		guest_list_name		= tgla_name;
		entourage 			= entourage
		date 				= tgl_date;
		
	}


%>

<td style="white-space:nowrap;">
	<span><%= jQuery.datepicker.formatDate('DD MM d, yy', new Date(date + ' 00:00:00')) %></span>
</td>




<td>
	
	<% if(!collapsed){ %>
	
		<img style="border:1px solid #CCC; width:150px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'venues/banners/' + tv_image + '_t.jpg' %>" />
		<br/>
		
	<% } %>
	
	<span><%= tv_name %></span>
	
</td>




<td>
	
	<% if(!collapsed){ %>
		
		<% if(head_user == null){ %>
			<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
		<% }else{ %>
			<img src="https://graph.facebook.com/<%= head_user %>/picture?width=50&height=50" />
		<% } %>
		
		<br/>
		
	<% } %>		
			
		<% if(head_user == null){ %>
			<span><%= supplied_name %></span>
		<% }else{ %>
			
			<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + head_user + '/' %>"><span data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span></a>
			
		<% } %>
			
	<% if(u_phone_number){ %>
		<br/><span style="white-space:nowrap;"><%= u_phone_number.replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3') %></span>
	<% } %>
	
</td>

<td>
	
	<% if(type == 'promoter'){ %>
		
		<% if(!collapsed){ %>
			<img style="border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + up_profile_image + '_t.jpg' %>" />
			<br/>
		<% } %>
	
		<span><%= u_full_name %></span>
		
	<% }else{ %>
		
		<span>None</span>
		
	<% } %>
	
</td>



<td>
	
	<% if(!collapsed){ %>
		<img style="border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + guest_list_image + '_t.jpg' %>" />
		<br/>
	<% } %>
	<span><%= guest_list_name %></span>
	
</td>



<td>
	
	<% if(collapsed){ %>
		
		<span> --- </span>
		
	<% }else{ %>
		<table class="user_messages" style="width:152px;">
			<tbody>
				<tr>
					<td class="message_header">Request Message:</td>
				</tr>
				<tr>
					<td><%= (request_message.length) ? request_message : ' - ' %></td>
				</tr>
				<tr>
					<td class="message_header">Response Message:</td>
				</tr>
				<tr>
					<td class="response_message"><%= (response_message.length) ? response_message : ' - ' %></td>
				</tr>
				<tr>
					<td class="message_header">Host/Manager Notes:</td>
				</tr>
				<tr>
					<td class="response_message"><%= (host_notes.length) ? host_notes : ' - ' %></td>
				</tr>
			</tbody>
		</table>
	<% } %>
		
	
</td>
<td>
	<% if(table_request == '1'){ %>
		<span style="color:green;">Yes</span><br/>
		<span style="color:black; white-space:nowrap;">Min:</span><br/>
		<span style="color:green; white-space:nowrap;">$<%= table_min_spend %></span>
	<% }else{ %>
		<span style="color:red;">No</span>
	<% } %>
</td>

<td style="white-space:nowrap; <% if(!collapsed){ %> width:244px; <% } %>">
	
		
	<% if(entourage && !entourage.length){ %>
		
		
		<span> --- </span>
		
		
	<% }else{ %>
		
		<% if(!collapsed){ %>
			
			<table style="margin:0; float:right; width:100%;">
				<thead>
					<tr>
						<th>Name</th>
						<th>Picture</th>
					</tr>
				</thead>
				<tbody>
					
					
					<% for(var i in entourage){ %>
						<tr class="<%= (i % 2) ? 'odd' : '' %>">
							<td>
								
								<% if(entourage[i].oauth_uid == null){ %>
									<span><%= ((typeof entourage[i].pglre_supplied_name !== 'undefined') ? entourage[i].pglre_supplied_name : entourage[i].tglre_supplied_name ) %></span>
								<% }else{ %>
									
									<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + entourage[i].oauth_uid + '/' %>">
										<span data-oauth_uid="<%= entourage[i].oauth_uid %>" data-name="<%= entourage[i].oauth_uid %>"></span>
									</a>
									
								<% } %>
								
							</td>
							<td>

								<% if(entourage[i].oauth_uid == null){ %>
									<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
								<% }else{ %>
									<img src="https://graph.facebook.com/<%= entourage[i].oauth_uid %>/picture?width=50&height=50" />
								<% } %>
								
							</td>
						</tr>
					<% } %>					
					
					
					
				</tbody>
			</table>
			
		<% }else{ %>
			
			<table style="margin:0; float:right; width:100%;">
				<tbody>
					
											
					<% for(var i in entourage){ %>
						
						<tr class="<%= (i % 2) ? 'odd' : '' %>">
							<td>
								<% if(entourage[i].oauth_uid == null){ %>
									
									<span><%= ((typeof entourage[i].pglre_supplied_name !== 'undefined') ? entourage[i].pglre_supplied_name : entourage[i].tglre_supplied_name ) %></span>
									
								<% }else{ %>
									
									<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + entourage[i].oauth_uid + '/' %>">
										<span data-oauth_uid="<%= entourage[i].oauth_uid %>" data-name="<%= entourage[i].oauth_uid %>"></span>
									</a>
									
								<% } %>
							</td>
						</tr>
						
					<% } %>
					
					
				</tbody>
			</table>
			
		<% } %>
	<% } %>
	

</td>