<?php 
	$page_obj = new stdClass;
	$page_obj->users 				= json_decode($users);
	$page_obj->weekly_guest_lists 	= $weekly_guest_lists;
	$page_obj->clients 				= $clients;
	$page_obj->promoter				= $this->library_promoters->promoter;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<div style="display:none;" id="manual_add_modal"></div>


<div id="guest_list_content" class="tabs" style="display:block; width:1050px">
	
	<div class="ui-widget-header">
		<span>Promoter Guest Lists</span>
	</div><br>
	
	<div id="left_menu" class="one_fourth">
		
		<div style="width:100%; text-align:center; margin-bottom:20px; border-bottom:0;">
			<a href="<?= $central->promoter_admin_link_base . 'manage_guest_lists/' ?>" class="ajaxify button_link btn-action">Edit Guest Lists</a>	
		</div>
		
		<div style="width:100%; text-align:center; margin-bottom:10px; border-bottom:1px solid #000;">
			<img id="left_menu_gl_img" 		src="" alt="" /><br/>
			<img id="left_menu_venue_img" 	src="" alt="" />
		</div>
				
		<ul class="sitemap" style="cursor: default; text-decoration:none !important;"></ul>

	</div>
	
	<div id="list_status" class="three_fourth last"></div>
	
	<br/>
	<br/>
	<br/>
	
	<div class="one_fourth"></div>
	<div id="lists_container" class="three_fourth last"></div>
	
	<div style="clear:both"></div>
</div>

