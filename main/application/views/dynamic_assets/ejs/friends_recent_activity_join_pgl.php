<%
	var data = jQuery.parseJSON(un_notification_data);
%>

<li>
	<% if(typeof data.pgla_image !== 'undefined'){ %>
		<%= image_insert(window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + data.pgla_image + '_t.jpg', {}) %>
	<% } %>
	
	<p><?= $this->lang->line('fr-rec_act_join_pgl') ?></p>
</li>