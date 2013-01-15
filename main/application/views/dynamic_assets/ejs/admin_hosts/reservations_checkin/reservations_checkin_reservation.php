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
	<span style="font-weight:bold; text-decoration:underline;" data-mobile_font="18px"><%= guest_list_name %></span>
	<br/>
	
	<% if(typeof pglr_id !== 'undefined'){ %>
		<span style="color:#474D6A;" data-mobile_font="10px"><%= u_full_name %></span>
	<% }else{ %>
		<span style="color:#474D6A;" data-mobile_font="10px">House List</span>
	<% } %>
	
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