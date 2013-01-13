<% if(typeof pglr_id !== 'undefined'){ %>
	
	<div style="background:#CCC;" data-top_min style="cursor:move;" class="ui-widget-header ui-corner-all">
		<span><%= u_full_name %></span>
	</div>
	
	<div data-collapse_me style="width: 100%; border-radius: 5px; text-align: center; background: #474D6A !important; color: #FFF; padding: 10px 0 10px 0;">
		
		<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + up_profile_image + '_t.jpg' %>" />
		<h2 style="color:#FFF;"><%= u_full_name %></h2>
				
	</div>

<% }else{ %>
	
	
	<div style="background:#CCC;" data-top_min style="cursor:move;" class="ui-widget-header ui-corner-all">
		<span>House Guest List</span>
	</div>
	<div data-collapse_me style="width: 100%; border-radius: 5px; text-align: center; background: #474D6A !important; color: #FFF; padding: 10px 0 10px 0;">
	
		<img src="<%= window.module.Globals.prototype.s3_uploaded_images_base_url + 'venues/banners/' + tv_image + '_t.jpg' %>" />
		<h2 style="color:#FFF;">House Guest List</h2>
	
	</div>
	
	
	
<% } %>

<table data-collapse_me style="width:100%;" class="normal reservations_holder">
	<thead>
		<tr>
			<th>Checkin Status</th>
			<th>Head User</th>
			<th>Guest List</th>
			<th>Messages</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>