<td>
	<img src="https://graph.facebook.com/<%= head_user %>/picture?width=50&height=50" />
	<p data-name="<%= head_user %>"></p>
	<span><%= u_phone_number %></span>
</td>
<td>
	<img style="width:100px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>venues/banners/<%= tv_image %>_t.jpg" />
	<p><%= tv_name %></p>
</td>
<td>
	<table>
		<tr>
			<td style="padding:0;"><img style="width:40px;" src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url %>guest_lists/<%= pgla_image %>_t.jpg" /></td>
			<td style="padding:0 0 0 5px;"><p><%= pgla_name %></p></td>
		</tr>
	</table>
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
			<tr>
				<td style="padding:0;">
					<img src="https://graph.facebook.com/<%= ent_user %>/picture?width=25&height=25" />
				</td>
				<td style="padding:0 0 0 5px;">
					<p data-name="<%= ent_user %>"></p>
				</td>
			</tr>
			
			<% } %>
			
		</table>
	<% }else{ %>
	
		<p> - </p>
	
	<% } %>
</td>
<td>
	<% if(pglr_table_request == 1){ %>
		<span style="color:green;">Yes</span>
		<p><%= table_min_spend %></p>
	<% }else{ %>
		<span style="color:red;">No</span>
	<% } %>
</td>
<td style="vertical-align:top;padding-top:15px;">
	<a href="#" style="margin:0; background:blue; border-color:blue;" data-action="request-respond" class="button_link">Respond</a><br/><br/>
</td>