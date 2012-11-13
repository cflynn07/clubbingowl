<% if(results && results.length){ %>
	<% for(var i=0; i < results.length; i++){ %>
		
		<table>
			<tr>
				<td class="user_pic" rowspan="2">
					<img src="https://graph.facebook.com/<%= results[i].pr_users_oauth_uid %>/picture?width=50&height=50" />
				</td>
				<td class="user_name">
					<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'friends/' + results[i].u_third_party_id + '/' %>"><%= results[i].u_full_name %></a>
					
					<a class="ajaxify" href="<%= window.module.Globals.prototype.front_link_base + 'friends/' + results[i].u_third_party_id + '/' %>">
						<div style="margin-top:5px; margin-bottom:5px;">
							
							<%
								var checked_string = 'checked="checked"';
							%>
							
							<input name="star_<%= i %>" type="radio" class="star" disabled="disabled" <%= (results[i].pr_score == '1') ? checked_string : '' %> />
							<input name="star_<%= i %>" type="radio" class="star" disabled="disabled" <%= (results[i].pr_score == '2') ? checked_string : '' %> />
							<input name="star_<%= i %>" type="radio" class="star" disabled="disabled" <%= (results[i].pr_score == '3') ? checked_string : '' %> />
							<input name="star_<%= i %>" type="radio" class="star" disabled="disabled" <%= (results[i].pr_score == '4') ? checked_string : '' %> />
							<input name="star_<%= i %>" type="radio" class="star" disabled="disabled" <%= (results[i].pr_score == '5') ? checked_string : '' %> />
							
						</div>
					</a>
				</td>
			</tr>
			<tr>
				<td class="user_comment"><%= results[i].pr_review %></td>
			</tr>
		</table>
		
	<% } %>
<% }else{ %>

	<table>
		<tr>
			
			<td style="width:20%; padding-top:10px; vertical-align:top;">
				<img id="toro" src="<%= window.module.Globals.prototype.front_assets + 'images/ClubbingOwlBackgroundWeb.png' %>" />
			</td>
			<td style="width:80%; color:#FFF; font-size:14px; padding-left:4px; padding-top:10px; font-weight:300;">Sorry! It looks like none of your friends have reviewed <strong><%= jQuery('div#up_first_name').html() %></strong> yet! You can be the first by joining one of <%= jQuery('div#up_first_name').html() %>'s <a href="<%= window.module.Globals.prototype.front_link_base + 'promoters/' + jQuery('div#up_public_identifier').html() + '/guest_lists/' %>">guest-lists</a>.</td>
			
		
		</tr>
	</table>

<% } %>