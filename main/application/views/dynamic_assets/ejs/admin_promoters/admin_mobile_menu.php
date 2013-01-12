<div id="mobile_menu">
	
	<span>Navigation: </span>
	<select id="mobile_menu_nav">
		<% for(var i in links){ %>
			<option value="<%= links[i].href %>"><%= links[i].title %></option>
		<% } %>
	</select>
	
	<span style="float:right;"><span data-user_name>Casey Flynn</span> - ClubbingOwl Promoter Admin</span>

</div>