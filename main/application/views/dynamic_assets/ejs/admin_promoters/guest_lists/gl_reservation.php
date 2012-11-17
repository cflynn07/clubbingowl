<td>
	<% if(!collapsed){ %>
		<img src="https://graph.facebook.com/<%= head_user %>/picture?width=50&height=50" /><br/>
	<% } %>
	<span data-name="<%= head_user %>"></span>
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
				<td class="message_header">Host Notes:</td>
			</tr>
			<tr style="max-width:122px;">
				<td class="host_notes" style="max-width:122px;">
					<div class="edit" style="display:none;">
						<textarea></textarea>
						<br>
						<span class="message_remaining"></span>
					</div>
					<span class="original">
						<%= (pglr_host_message.length) ? pglr_host_message : '<span style="font-weight: bold;">Edit Message</span>' %>
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
		<a href="#" style="position:relative; top:10px; background:blue; border-color:blue;" data-action="request-respond" class="button_link">Respond</a><br/><br/>
	<% } %>
</td>


<td style="white-space:nowrap; <% if(!collapsed){ %> width:244px; <% } %>">
	<% if(!entourage_users.length){ %>
		<p>No Entourage</p>
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
							<td><span data-name="<%= entourage_users[i] %>"></span></td>
							<td>
								<img src="https://graph.facebook.com/<%= entourage_users[i] %>/picture?width=50&height=50" />
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
							<td><span data-name="<%= entourage_users[i] %>"></span></td>
						</tr>
					<% } %>
					
				</tbody>
			</table>
			
		<% } %>
	<% } %>
</td>






<?php if(false): ?>
<td><?= ($group->pglr_table_request == '1') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>' ?></td>
<td class="actions">
	
	<?php if($group->pglr_approved == '1'): ?>
		<span style="color: green;">Approved</span>
	<?php elseif($group->pglr_approved == '-1'): ?>
		<span style="color: red;">Declined</span>
	<?php else: ?>
		<span class="app_dec_action" style="font-weight: bold; text-decoration: underline; cursor: pointer; color: blue;">Requested</span>
	<?php endif; ?>
	
</td>
<td style="white-space:nowrap; width:244px;">
	<?php if(!count($group->entourage_users)): ?>
		<p>No Entourage</p>
	<?php else: ?>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Picture</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($group->entourage_users as $key2 => $ent_user): ?>
				<tr <?= ($key2 % 2) ? 'class="odd"' : '' ?>>
					<td><div class="name_<?= $ent_user ?>"></div></td>
					<td><div class="pic_square_<?= $ent_user ?>"></div></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</td>
<?php endif; ?>