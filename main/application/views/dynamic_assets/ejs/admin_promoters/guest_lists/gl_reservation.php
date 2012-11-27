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
		<span><%= pglr_supplied_name %></span>
	<% }else{ %>
		<span data-name="<%= head_user %>"></span>
	<% } %>
	
</td>
<td>
	<% if(collapsed){ %>
		
		<span> --- </span>
		
	<% }else{ %>
		
		<table class="user_messages" style="width:152px; text-wrap: unrestricted;">
			<tr>
				<td class="message_header">Request Message:</td>
			</tr>
			<tr>
				<td><%= (pglr_request_msg.length) ? pglr_request_msg : ' - ' %></td>
			</tr>
			<tr>
				<td class="message_header">Response Message:</td>
			</tr>
			<tr>
				<td class="response_message"><%= (pglr_response_msg.length) ? pglr_response_msg : ' - ' %></td>
			</tr>
			<tr>
				<td class="message_header">Host/Manager Notes:</td>
			</tr>
			<tr style="max-width:122px;">
				<td class="host_notes" style="max-width:122px;">
					<div class="edit" style="display:none;">
						<textarea></textarea>
						<br>
						<span class="message_remaining"></span>
						<a href="#" style="position:relative; top:10px; text-decoration:none;" data-action="update-notes" class="button_link btn-action">Update</a><br/><br/>
					</div>
					<span class="original">
						<%= (pglr_host_message.length) ? pglr_host_message : '<span style="font-weight: bold;">Edit Notes</span>' %>
					</span>
					<img class="message_loading_indicator" style="display:none;" src="<%= window.module.Globals.prototype.global_assets + 'images/ajax.gif' %>" alt="loading..." />
				</td>
			</tr>
		</table>
	<% } %>
</td>
<td>
	<% if(pglr_table_request == '1'){ %>
		<span style="color:green;">Yes</span>
	<% }else{ %>
		<span style="color:red;">No</span>
	<% } %>
</td>
<td class="actions">
	<% if(pglr_approved == '1'){ %>
		<span style="color: green;">Approved</span>
	<% }else if(pglr_approved == '-1'){ %>
		<span style="color: red;">Declined</span>
	<% }else{ %>
		<a href="#" style="position:relative; top:10px;" data-action="request-respond" class="button_link btn-action">Respond</a><br/><br/>
	<% } %>
	
	<% if(pglr_manual_add == '1'){ %>
		<br/>
		<span style="">Manually Added</span>
	<% } %>
	
</td>


<td style="white-space:nowrap; <% if(!collapsed){ %> width:244px; <% } %>">
	<% if(!entourage_users.length){ %>
		
		
		<span> --- </span>
		
		
	<% }else{ %>
		
		<% if(!collapsed){ %>
			
			<table style="margin:0;">
				<thead>
					<tr>
						<th>Name</th>
						<th>Picture</th>
					</tr>
				</thead>
				<tbody>
					
					<% for(var i in entourage_users){ %>
						<tr class="<%= (i % 2) ? 'odd' : '' %>">
							<td>
								
								<% if(entourage_users[i].pglre_oauth_uid == null){ %>
									<span><%= entourage_users[i].pglre_supplied_name %></span>
								<% }else{ %>
									<span data-name="<%= entourage_users[i].pglre_oauth_uid %>"></span>
								<% } %>
								
							</td>
							<td>

								<% if(entourage_users[i].pglre_oauth_uid == null){ %>
									<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
								<% }else{ %>
									<img src="https://graph.facebook.com/<%= entourage_users[i].pglre_oauth_uid %>/picture?width=50&height=50" />
								<% } %>
								
							</td>
						</tr>
					<% } %>
					
				</tbody>
			</table>
			
		<% }else{ %>
			
			<table style="margin:0;">
				<tbody>
					
					<% for(var i in entourage_users){ %>
						
						<tr class="<%= (i % 2) ? 'odd' : '' %>">
							<td>
								<% if(entourage_users[i].pglre_oauth_uid == null){ %>
									<span><%= entourage_users[i].pglre_supplied_name %></span>
								<% }else{ %>
									<span data-name="<%= entourage_users[i].pglre_oauth_uid %>"></span>
								<% } %>
							</td>
						</tr>
						
					<% } %>
					
				</tbody>
			</table>
			
		<% } %>
	<% } %>
</td>