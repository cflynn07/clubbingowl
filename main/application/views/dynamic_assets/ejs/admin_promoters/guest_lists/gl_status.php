<div class="ui-widget">
	
	<div class="ui-widget-header">
		<span>"<%= pgla_name %>" Status</span>
		
		<span style="float:right;color:grey;">Last Updated: 
			<span id="glas_last_updated"><% if(typeof glas_human_date !== 'undefined'){ %>
				<%= glas_human_date %>
			<% }else{ %>
				&nbsp;&nbsp;&nbsp;
			<% } %>
			</span>
		</span>
		
	</div>
	
	<div id="current_status" class="ui-widget-content" style="padding:5px; padding-left:20px; background:#CCC;">
		
		<% if(typeof glas_status !== 'undefined'){ %>
			<span style="color:blue;"><%= glas_status %></span>
		<% } %>
		
	</div>
	<div style="padding:10px;" class="ui-widget-content">
		<textarea style="width:100%; height:40px; border:1px dashed #CCC; resize:none;" id="insert_new_status"></textarea>
		<a href="#" style="background:blue; border-color:blue; float:right;" data-action="update-status" class="button_link">Update Status</a>
	</div>
	<div style="clear:both"></div>
</div>

<hr>
<div style="clear:both"></div>