<div data-uid="<%= uid %>" class="visitor">
	<img style="width:50px;height:50px;" src="<%= pic_square %>" alt="picture" />
	
	<a class="ajaxify vc_name" href="<%= window.module.Globals.prototype.front_link_base + 'admin/promoters/clients/' + uid + '/' %>" class="vc_name">
		<span class="uid"><%= uid %></span>
		<%= first_name %>
	</a>	
				
<?php if(false): ?>
	<a href="javascript:void(0);" class="vc_name"><span class="uid"><%= uid %></span><%= first_name %></a>
<?php endif; ?>
	
</div>