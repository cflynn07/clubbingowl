<% if(request_type == 'promoter'){ %>
	
						<td>
							
							<% if(head_user == null){ %>
								
								<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
								<p style="margin-bottom:0;"><%= pglr_supplied_name %></p>
							
							<% }else{ %>
								
								<img src="https://graph.facebook.com/<%= head_user %>/picture?width=50&height=50" />
								<p style="margin-bottom:0;" data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></p>
								<span style="white-space:nowrap;"><%= u_phone_number.replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3') %></span>
										
							<% } %>
							
						</td>
						<td>
							<img style="width:40px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>profile-pics/<%= up_profile_image %>_t.jpg" />
							<p style="margin-bottom:0px;"><%= u_full_name %></p>
						</td>
						<td>
							<img style="width:100px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>venues/banners/<%= tv_image %>_t.jpg" />
							<p><%= tv_name %></p>
						</td>
						<td>
							<img style="width:40px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>guest_lists/<%= pgla_image %>_t.jpg" />
							<p style="margin-bottom:0;"><%= pgla_name %></p>
						</td>
						<td>
							<%= human_date %>
						</td>
						<td>
							<%= (pglr_request_msg.length) ? pglr_request_msg : ' - ' %>
						</td>
						<td>
							
							<% if(entourage_users && entourage_users.length > 0){ %>
								<table>
									
									<% for(var i=0; i < entourage_users.length; i++){ %>
										<% var ent_user = entourage_users[i]; %>
										
										
										<tr class="<%= (i % 2) ? 'odd' : '' %>">
											
											
											<td style="padding:0;">
												
												<% if(ent_user.pglre_oauth_uid == null){ %>
													<span><%= ent_user.pglre_supplied_name %></span>
												<% }else{ %>
													<span data-oauth_uid="<%= ent_user.pglre_oauth_uid %>" data-name="<%= ent_user.pglre_oauth_uid %>"></span>
												<% } %>
												
											</td>
											<td style="padding:0 0 0 5px;">
												
												<% if(ent_user.pglre_oauth_uid == null){ %>
													<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
												<% }else{ %>
													<img src="https://graph.facebook.com/<%= ent_user.pglre_oauth_uid %>/picture?width=50&height=50" />
												<% } %>
												
											</td>
											
											
										</tr>
									
									<% } %>
									
								</table>
							<% }else{ %>
							
								<p> --- </p>
							
							<% } %>
							
						</td>
						<td>
							<% if(pglr_table_request == 1){ %>
								
								<span style="color:green;">Yes</span><br/>
								<span style="color:black; white-space:nowrap;">Min:</span><br/>
								<span style="color:green; white-space:nowrap;">$<%= table_min_spend %></span>
								
							<% }else{ %>
								
								<span style="color:red;">No</span>
								
							<% } %>
						</td>
						<td style="vertical-align:top;padding-top:15px;">
							<a href="#" style="margin:0;" data-action="request-respond" class="button_link btn-action">Respond</a><br/><br/>
						</td>
						
<% }else{ %> <% // ---------------------------------------------------------------------------------------------- %>
						
						<td>
							
							<% if(tglr_user_oauth_uid == null){ %>
								
								<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
								<p style="margin-bottom:0;"><%= tglr_supplied_name %></p>
							
							<% }else{ %>
								
								<img src="https://graph.facebook.com/<%= tglr_user_oauth_uid %>/picture?width=50&height=50" />
								<p style="margin-bottom:0;" data-oauth_uid="<%= tglr_user_oauth_uid %>" data-name="<%= tglr_user_oauth_uid %>"></p>
								<span style="white-space:nowrap;"><%= u_phone_number.replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3') %></span>
										
							<% } %>
							
						</td>
						<td>
							---
						</td>
						<td>
							<img style="width:100px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>venues/banners/<%= tv_image %>_t.jpg" />
							<p><%= tv_name %></p>
						</td>
						<td>
							<img style="width:40px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>guest_lists/<%= tgla_image %>_t.jpg" />
							<p style="margin-bottom:0;"><%= tgla_name %></p>
						</td>
						<td>
							<%= request_human_date %>
						</td>
						<td>
							<%= (tglr_request_msg.length) ? tglr_request_msg : ' - ' %>
						</td>
						<td>
							
							<% if(entourage && entourage.length > 0){ %>
								<table>
									
									<% for(var i=0; i < entourage.length; i++){ %>
										<% var ent_user = entourage[i]; %>
										
										
										<tr class="<%= (i % 2) ? 'odd' : '' %>">
											
											
											<td style="padding:0;">
												
												<% if(ent_user.tglre_oauth_uid == null){ %>
													<span><%= ent_user.tglre_supplied_name %></span>
												<% }else{ %>
													<span data-oauth_uid="<%= ent_user.tglre_oauth_uid %>" data-name="<%= ent_user.tglre_oauth_uid %>"></span>
												<% } %>
												
											</td>
											<td style="padding:0 0 0 5px;">
												
												<% if(ent_user.tglre_oauth_uid == null){ %>
													<img src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
												<% }else{ %>
													<img src="https://graph.facebook.com/<%= ent_user.tglre_oauth_uid %>/picture?width=50&height=50" />
												<% } %>
												
											</td>
											
											
										</tr>
									
									<% } %>
									
								</table>
							<% }else{ %>
							
								<p> --- </p>
							
							<% } %>
							
						</td>
						<td>
							<% if(tglr_table_request == 1){ %>
								
								<span style="color:green;">Yes</span><br/>
								<span style="color:black; white-space:nowrap;">Min:</span><br/>
								<span style="color:green; white-space:nowrap;">$<%= table_min_spend %></span>
								
							<% }else{ %>
								
								<span style="color:red;">No</span>
								
							<% } %>
						</td>
						<td style="vertical-align:top;padding-top:15px;">
							<a href="#" style="margin:0;" data-action="request-respond" class="button_link btn-action">Respond</a><br/><br/>
						</td>
	
<% } %>