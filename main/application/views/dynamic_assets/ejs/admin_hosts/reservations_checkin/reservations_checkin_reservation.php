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
		entourage;
	
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
		
	}


%>

<td data-mobile_font="18px" style="width:15%;" class="ui4">
	
	<% var rand = Math.random(); %>
	<input class="checkbox1" type="checkbox" id="<%= head_user || rand %>" name="<%= head_user || rand %>" />
	<label for="<%= head_user || rand %>">Arrived</label>

</td>



<td style="padding:5px; width:15%;">
	
	
	<div class="additional_checkin_info" style="opacity:0.4;">
		<table>
			<tbody>
				<tr>
					<td style="font-size:14px;padding-right:0;"><label for="category">Category: </label></td>
					<td>
						<select disabled="disabled" data-mobile_font="18px" name="category">
							<option val="1">Full - $25</option>
							<option val="2">Reduced - $15</option>
							<option val="3">Comped - $0</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="font-size:14px;padding-right:0;"><label for="additional_friends">Friends: </label></td>
					<td>
						<select disabled="disabled" data-mobile_font="18px" name="additional_friends">
								<option selected="selected" value="0">0</option>
							<?php for($i=1; $i<11; $i++): ?>
								<option value="<?= $i ?>"><?= $i ?></option>
							<?php endfor; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
	
	
</td>



<td data-mobile_font="18px">
	
	<% if(!collapsed){ %>
		
		<% if(head_user == null){ %>
			<img style="display:inline-block; margin:0 5px 5px 0; vertical-align:top;" src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />
		<% }else{ %>
			<img style="display:inline-block; margin:0 5px 5px 0; vertical-align:top;" src="https://graph.facebook.com/<%= head_user %>/picture?width=50&height=50" />
		<% } %>		
		
		<br/>
		
	<% } %>		
		
	
	
		
	<% if(head_user == null){ %>
		<span style="margin-bottom:5px;" data-mobile_font="18px"><%= supplied_name %></span>
	<% }else{ %>
		<span style="margin-bottom:5px;" data-mobile_font="18px" data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span>
	<% } %>
	
	
		
</td>








<td>
	
	<% if(!collapsed){ %>
		<img style="border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + guest_list_image + '_t.jpg' %>" />
		<br/>
	<% } %>
	<span data-mobile_font="18px"><%= guest_list_name %></span>
	
</td>



<td>
	
	<% if(collapsed){ %>
		
		<span><%= (host_notes.length) ? host_notes : ' - ' %></span>
		
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
















<?php if(false): ?>


<td>
	<% if(table_request == '1'){ %>
		<span style="color:green;">Yes</span><br/>
		<span style="color:black; white-space:nowrap;">Min: </span>
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
								
								
								
								
								<% if(collapsed){ %>
									
									<input type="checkbox" />
									
								<% } %>
								
								
								
								
								
								<% if(entourage[i].oauth_uid == null){ %>
									<span><%= ((typeof entourage[i].pglre_supplied_name !== 'undefined') ? entourage[i].pglre_supplied_name : entourage[i].tglre_supplied_name ) %></span>
								<% }else{ %>
									
									<span data-oauth_uid="<%= entourage[i].oauth_uid %>" data-name="<%= entourage[i].oauth_uid %>"></span>
									
								<% } %>
								
								
								
								
								
								<% if(!collapsed){ %>
									
									<br/>
									<input type="checkbox" />
									
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
								
								
								<% if(collapsed){ %>
									
									<input type="checkbox" />
									
								<% } %>
								
								
								
								
								
								<% if(entourage[i].oauth_uid == null){ %>
									
									<span><%= ((typeof entourage[i].pglre_supplied_name !== 'undefined') ? entourage[i].pglre_supplied_name : entourage[i].tglre_supplied_name ) %></span>
									
								<% }else{ %>
									
									<span data-oauth_uid="<%= entourage[i].oauth_uid %>" data-name="<%= entourage[i].oauth_uid %>"></span>
									
								<% } %>
								
								
								
								
								
								
								<% if(!collapsed){ %>
								
									<br/>
									<input type="checkbox" />
									
								<% } %>
								
								
								
								
								
								
								
							</td>
						</tr>
						
					<% } %>
					
					
				</tbody>
			</table>
			
		<% } %>
	<% } %>
	

</td>


<?php endif; ?>