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
		head_user 			= oauth_uid;
		supplied_name		= pglre_supplied_name;
		
		
		entourage_head_user 		= pglr_user_oauth_uid;
		entourage_supplied_name		= pglr_supplied_name;
		
		
		u_phone_number		= ''; //TODO
		request_message 	= pglr_request_msg;
		response_message 	= pglr_response_msg;
		host_notes			= pglr_host_message;	
		table_request		= pglr_table_request;
		table_min_spend		= ''; //TODO
		guest_list_image 	= pgla_image;
		guest_list_name		= pgla_name;
		
	
	}else{
		
		type				= 'team';
		head_user 			= oauth_uid;
		supplied_name		= tglre_supplied_name;
		
		
		entourage_head_user 		= tglr_user_oauth_uid;
		entourage_supplied_name		= tglr_supplied_name;
		
		
		
		u_phone_number		= ''; //TODO
		request_message 	= tglr_request_msg;
		response_message	= tglr_response_msg;
		host_notes			= tglr_host_message;
		table_request		= tglr_table_request;
		table_min_spend		= ''; //TODO
		guest_list_image 	= tgla_image;
		guest_list_name		= tgla_name;
		
	}


%>

<td data-mobile_font="18px" style="width:15%;" class="ui4">
	
	<input type="checkbox" class="checkin_button" id="button_<%= reservation_iterator %>" name="button_<%= reservation_iterator %>"  <%= ((hc_id != null) ? 'checked="checked"' : '') %> />
	<label data-iphone_font for="button_<%= reservation_iterator %>">Arrived</label>

</td>



<td style="padding:5px; width:15%;">
	
	
	<div class="additional_checkin_info" style="<%= ((hc_id == null) ? 'opacity:0.4;' : '')  %>">
		<table>
			<tbody>
				<tr>
					<td style="font-size:14px;padding-right:0;"><label for="category">Category: </label></td>
					<td>
						
						
						<select <%= ((hc_id == null) ? 'disabled="disabled"' : '')  %> data-mobile_font="18px" name="category">
						
							<% for(var i in window.page_obj.checkin_categories){ %>
							
								<% var category = window.page_obj.checkin_categories[i]; %>
								<option data-category_value="<%= category.hcc_amount %>" value="<%= category.hcc_id %>">$<%= category.hcc_amount %> - <%= category.hcc_title %></option>
							
							<% } %>
						
						</select>
						
						
					</td>
				</tr>
				<tr>
					<td style="font-size:14px;padding-right:0;"><label for="additional_friends">Friends: </label></td>
					<td>
						
						
						<select <%= ((hc_id == null) ? 'disabled="disabled"' : '')  %> data-mobile_font="18px" name="additional_guests">
							
							
							<% for(var i=0; i < 21; i++){ %>
								<option <%= ((hcd_additional_guests == i) ? 'selected="selected"' : '') %> value="<%= i %>"><%= i %></option>
							<% } %>
					
					
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
		<span data-iphone_font style="margin-bottom:5px;" data-mobile_font="18px"><%= supplied_name %></span>
	<% }else{ %>
		<span data-iphone_font style="margin-bottom:5px;" data-mobile_font="18px" data-oauth_uid="<%= head_user %>" data-name="<%= head_user %>"></span>
	<% } %>
	
	
	
	
	<% if(jQuery.isIphone()){ %>
		
		<br/>
		<span style="font-weight:bold;">Host Notes:</span>
		<span><%= (host_notes.length) ? host_notes : ' - ' %></span>
		
		
	<% } %>
	
	
	
	<div data-iphone_hide style="padding-left:10px; font-size:10px;">
		
		<span style="text-decoration:underline;">Entourage</span>:&nbsp;			
			
		<% if(entourage_head_user == null){ %>

			<% if(!collapsed){ %>
				<br/>
				<img style="display:inline-block; margin:0 5px 5px 0; vertical-align:top; width:30px; height:30px;" src="<%= window.module.Globals.prototype.admin_assets %>images/unknown_user.jpeg" />						
			<% } %>
			<span style="margin-bottom:5px;"><%= entourage_supplied_name %></span>
			

		<% }else{ %>

			
			<% if(!collapsed){ %>
				<br/>
				<img style="display:inline-block; margin:0 5px 5px 0; vertical-align:top; width:30px; height:30px;" src="https://graph.facebook.com/<%= entourage_head_user %>/picture?width=50&height=50" />				
			<% } %>
			<span style="margin-bottom:5px;" data-oauth_uid="<%= entourage_head_user %>" data-name="<%= entourage_head_user %>"></span>
			
			
		<% } %>		
		
			
	</div>
	
	
	
		
</td>








<td data-iphone_hide>
	
	<% if(!collapsed){ %>
		<img style="border:1px solid #CCC;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + guest_list_image + '_t.jpg' %>" />
		<br/>
	<% } %>
	<span style="font-weight:bold; text-decoration:underline;" data-mobile_font="18px"><%= guest_list_name %></span>
	<br/>
	
	<% if(typeof pglr_id !== 'undefined'){ %>
		<span style="color:#474D6A;" data-mobile_font="14px"><%= u_full_name %></span>
	<% }else{ %>
		<span style="color:#474D6A;" data-mobile_font="14px">House List</span>
	<% } %>
	
</td>



<td data-iphone_hide>
	
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