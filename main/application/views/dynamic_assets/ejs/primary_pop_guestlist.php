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
	<a href="<%=path%>">
		
		<table style="width:100%;">
			<tr>
				<td>
					
					<table>
						<tr>
							<td style="min-width:45px;">
								<%= inline_link(path, image, {style: 'display:inline-block;width:auto;float:left;'}) %>
							</td>
							<td>
								<div style="display:inline-block;vertical-align:top;">
									<span><%= gla_name %></span><br>
									<span class="subtext">@ <%= tv_name %></span><br>
									<span class="subtext"><%= c_name + ', ' + c_state %></span>
								</div>
								<div style="clear:both;"></div>
							</td>
						</tr>
					</table>
					
					
				</td>
				<td>
					<div style="float:right; text-align:right;">
						<span class="subtext" style="font-weight:500"><%= oauth_uid_count %> <?= $this->lang->line('m-friends') ?></span><br>
						<span class="subtext"><%= occurance_day %></span><br>
						<span class="subtext"><%= occurance_date %></span>
					</div>
					<div style="clear:both;"></div>
				</td>
			</tr>
		</table>
		<div style="clear:both"></div>
		
	</a>
	
	
	<?php if(false): ?>
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
	<?php endif; ?>
	
</li>