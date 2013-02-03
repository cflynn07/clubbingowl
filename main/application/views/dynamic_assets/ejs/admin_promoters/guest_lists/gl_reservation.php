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
		
		<span style="white-space:nowrap;"><%= pglr_supplied_name %></span>
		
	<% }else{ %>
		
		<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + head_user + '/' %>"><span style="white-space:nowrap;" data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span></a>
		
	<% } %>
		
		
		
	<% if(u_phone_number){ %>
		<br/><span style="white-space:nowrap;"><%= u_phone_number.replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3') %></span>
	<% } %>
		
		
		
		
	<% if(hc_id != null){ %>
		
		
		<div>
			<table style="margin:0;">
				<tbody>
					<tr>
						<td style="padding:0; color:green;"><span style="color: green; 	white-space:nowrap; width: 100%; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/OK.png' %>" /></span></td>
						<td style="padding:0;">&nbsp;<span style="color:green;">$<%= hcd_checkin_amount %></span>&nbsp;|&nbsp;<span>+<%= hcd_additional_guests %></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		
	<% } %>
	<% if(hc_id == null){ %>
			
		<% if(collapsed){ %>
			<br/><input <%= ((pglr_checkin_notify == '1') ? 'checked="checked"' : '') %> class="checkin_notify" type="checkbox" />
		<% }else{ %>
			<br/>
			
			<span style="white-space:nowrap;">Check-in Notify:</span><br/>
			<input <%= ((pglr_checkin_notify == '1') ? 'checked="checked"' : '') %> class="checkin_notify" type="checkbox" />
		
		<% } %>
			
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
		<span style="color:green;">Yes</span><br/>
		<span style="color:black; white-space:nowrap;">Min:</span><br/>
		<span style="color:green; white-space:nowrap;">$<%= table_min_spend %></span>
	<% }else{ %>
		<span style="color:red;">No</span>
	<% } %>
</td>
<td class="actions">
	
	
	
	
	<% if(pglr_approved == '1'){ %>
		<span style="color: green;">Approved</span>
		
		<% if(pglr_table_request == '1'){ %>
			<br/>
			<% if(pglr_manager_table_approved == '1'){ %>
				<span style="color: green; 	white-space:nowrap; width: 100%; border-top: 1px dashed #CCC; border-bottom: 1px dashed #CCC; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/OK.png' %>" /> Manager Approved</span>
			<% }else if(pglr_manager_table_approved == '-1'){ %>
				<span style="color: red; 	white-space:nowrap; width: 100%; border-top: 1px dashed #CCC; border-bottom: 1px dashed #CCC; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/No.png' %>" /> Manager Declined</span>
			<% }else{ %>
				<span style="white-space:nowrap; width: 100%; border-top: 1px dashed #CCC; border-bottom: 1px dashed #CCC; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/Question.png' %>" /> Pending Manager Approval</span>
			<% } %>
		<% } %>
		
		
		
		
	<% }else if(pglr_approved == '-1'){ %>
		<span style="color: red;">Declined</span>
	<% }else{ %>
		<a href="#" style="position:relative; top:10px;" data-action="request-respond" class="button_link btn-action">Respond</a><br/><br/>
	<% } %>
	
	<% if(pglr_manual_add == '1'){ %>
		<br/>
		<span style="white-space:nowrap;">Manually Added</span>
	<% } %>
	
	
	
</td>


<td style="<% if(!collapsed){ %> width:244px; <% } %>">
	<% if(!entourage_users.length){ %>
		
		
		<span> --- </span>
		
		
	<% }else{ %>
		
		<% if(!collapsed){ %>
			
			<table style="margin:0; width:100%;">
				<thead>
					<tr>
					</tr>
				</thead>
				<tbody>
					
					<% for(var i in entourage_users){ %>
						<tr class="<%= (i % 2) ? 'odd' : '' %>">
							<td>
								
								<% if(entourage_users[i].pglre_oauth_uid == null){ %>

									<span><%= entourage_users[i].pglre_supplied_name %></span>

								<% }else{ %>
									
									<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + entourage_users[i].pglre_oauth_uid + '/' %>">
										<span data-oauth_uid="<%= entourage_users[i].pglre_oauth_uid %>" data-name="<%= entourage_users[i].pglre_oauth_uid %>"></span>
									</a>
									
								<% } %>
								
								
								<% if(entourage_users[i].hc_id != null){ %>
								
									<div>
										<table style="margin:0;">
											<tbody>
												<tr>
													<td style="padding:0; color:green;"><span style="color: green; 	white-space:nowrap; width: 100%; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/OK.png' %>" /></span></td>
													<td style="padding:0;">&nbsp;<span style="color:green;">$<%= entourage_users[i].hcd_checkin_amount %></span>&nbsp;|&nbsp;<span>+<%= entourage_users[i].hcd_additional_guests %></span></td>
												</tr>
											</tbody>
										</table>
									</div>
								
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
									
									<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + entourage_users[i].pglre_oauth_uid + '/' %>">
										<span data-oauth_uid="<%= entourage_users[i].pglre_oauth_uid %>" data-name="<%= entourage_users[i].pglre_oauth_uid %>"></span>
									</a>
									
								<% } %>
								
								<% if(entourage_users[i].hc_id != null){ %>
									
									<div>
										<table style="margin:0;">
											<tbody>
												<tr>
													<td style="padding:0; color:green;"><span style="color: green; 	white-space:nowrap; width: 100%; padding: 3px;"><img style="vertical-align:middle; width:15px;" src="<%= window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/OK.png' %>" /></span></td>
													<td style="padding:0;">&nbsp;<span style="color:green;">$<%= entourage_users[i].hcd_checkin_amount %></span>&nbsp;|&nbsp;<span>+<%= entourage_users[i].hcd_additional_guests %></span></td>
												</tr>
											</tbody>
										</table>
									</div>
									
								<% } %>
								
							</td>
						</tr>
						
					<% } %>
					
				</tbody>
			</table>
			
		<% } %>
	<% } %>
</td>