<div style="display:none;" class="notification <% if(response != 'approved'){ %>negative<% } %>">
	
	<div class="center">
		<% if(notif_type == 'promoter'){ %>
			<?= $this->lang->line('ad-request_response_promoter') ?>
		<% }else if(notif_type == 'team') { %>
			<?= $this->lang->line('ad-request_response_team') ?>
		<% } %>
		
		<span class="notification_id" style="display:none;"><%= id %></span>
		
		<a class="notification_close" href="javascript: void(0);"><?= $this->lang->line('ad-request_response_close') ?></a>
		<br>
		<?= $this->lang->line('ad-request_response_resp_msg') ?> <%= response_message %>
	</div>
	
</div>