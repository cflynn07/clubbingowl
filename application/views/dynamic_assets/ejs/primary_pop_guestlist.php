<li>
	<% 
		var image = image_insert(window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/' + gla_image + '_t.jpg', {});
		var path;
		if(gl_type == 'promoter'){
			path = 'promoters/' + c_url_identifier + '/' + up_public_identifier + '/guest_lists/' + gla_name.replace(/ /g, '_');
		}else{
			path = 'venues/' + c_url_identifier + '/' + tv_name.replace(/ /g, '_') + '/guest_lists/' + gla_name.replace(/ /g, '_');
		}
	%>
		<%= inline_link(path, image, {style: 'display:inline-block;width:auto;float:left;'}) %>
	<a href="<%=path%>" style="display:inline-block; width:86%">
		<div style="display:inline-block;padding-left:8px;width:72%;vertical-align:top;">
			<span><%= gla_name %></span><br>
			<span class="subtext">@ <%= tv_name %></span><br>
			<span class="subtext"><%= c_name + ', ' + c_state %></span>
		</div>
		<div style="display:inline-block;width:25%; float:right; text-align:right; padding-right:5px;">
			<span class="subtext" style="font-weight:500"><%= oauth_uid_count %> <?= $this->lang->line('m-friends') ?></span><br>
			<span class="subtext"><%= occurance_day %></span><br>
			<span class="subtext"><%= occurance_date %></span>
		</div>
		<div style="clear:both;"></div>
	</a>
</li>