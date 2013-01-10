<% if(typeof pglr_id !== 'undefined'){ %>
	
	<div style="cursor:move;" class="ui-widget-header ui-corner-all">
		<span><%= u_full_name %></span>
	</div>
	
	<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + up_profile_image + '_t.jpg' %>" />
	
<% }else{ %>
	
	<div style="cursor:move;" class="ui-widget-header ui-corner-all">
		<span>House Guest List</span>
	</div>
	
	
<% } %>

<table  style="width:100%;" class="normal reservations_holder">
	<thead>
		<tr>
			<th>Head User</th>
			<th>Guest List</th>
			<th>Messages</th>
			<th>Table</th>
			<th>Entourage</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>