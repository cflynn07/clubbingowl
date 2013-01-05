<%
	var data = jQuery.parseJSON(un_notification_data);
%>

<li>
	<% if(typeof data.tgla_image !== 'undefined'){ %>
		<%= image_insert(window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + data.tgla_image + '_t.jpg', {class: 'venue-image'}) %>
	<% } %>
	
	<p><?= $this->lang->line('fr-rec_act_join_tgl') ?></p>
</li>