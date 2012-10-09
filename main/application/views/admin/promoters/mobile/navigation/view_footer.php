<div data-role="footer" data-position="fixed" class="ui-footer ui-bar-a ui-footer-fixed">
	
	<div data-role="navbar" class="ui-navbar ui-navbar-noicons" role="navigation">
		<ul class="ui-grid-b">
			
			<li>
				<a style="font-size:5px;" href="<?= $central->promoter_admin_link_base ?>mobile/" data-transition="none" class="<?= ($active == 'Requests') ? 'ui-btn-active ui-state-persist' : '' ?>">Requests</a>
			</li>
						
			<li>
				<a href="<?= $central->promoter_admin_link_base ?>mobile/guest_lists/" data-transition="none" class="<?= ($active == 'Guest Lists') ? 'ui-btn-active ui-state-persist' : '' ?>">Guest Lists</a>
			</li>			
			
			<li>
				<a href="<?= $central->promoter_admin_link_base ?>mobile/tables/" data-transition="none" class="<?= ($active == 'Tables') ? 'ui-btn-active ui-state-persist' : '' ?>">Tables</a>
			</li>			
			
			<li>
				<a href="<?= $central->promoter_admin_link_base ?>mobile/chat/" data-transition="none" class="<?= ($active == 'Chat') ? 'ui-btn-active ui-state-persist' : '' ?>">Chat</a>
			</li>
			
		</ul>
	</div><!-- /navbar -->
	
</div> 

<style type="text/css">
div.ui-navbar span.ui-btn-text{
	font-size: 10px;
}
</style>