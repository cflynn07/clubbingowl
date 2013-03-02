<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"> 
<title><?= $central->title ?></title>

<?php 
	//includes all karma/global js and css files + meta tags
	$this->load->view('admin/_common/view_common_header'); 
?>

<?php if(false): ?>
<script>
window.EventHandlerObject.addListener('pusher_init', function(){
	window.vc_page_scripts.global_promoter_pusher_notifications(<?= $users_oauth_uid ?>);
});
</script>
<?php endif; ?>


</head>
<body>
<?= $this->load->view('admin/_common/view_load_admin_fb_sdk', '', true) ?>


<?php $this->load->view('admin/_common/view_common_promoter_admin_user_stats'); ?>
<?php $this->load->view('admin/_common/view_common_admin_chat'); ?>


<script type="text/javascript">
	window.team_fan_page_id = '<?= $team_fan_page_id ?>';
	window.admin_users_oauth_uid = '<?= $users_oauth_uid ?>';
	window.module.Globals.prototype.user_oauth_uid = window.admin_users_oauth_uid;
</script>





<div id="dialog_actions" style="display: none;">

	<span>
		<img data-pic="" src="" class="pic_square" style="float: left; margin-right: 5px; height:50px; width:50px;" alt="picture" />
		<span data-name="" class="name"></span>'s reservation request.
	</span>
	
	<div style="clear: both;"></div>
	<br>

	<form>
		<fieldset>
			<label for="message">Send <span data-name="" class="name"></span> a message: (optional)</label>
			<textarea rows="5" style="resize:none; width:100%; border:1px solid #333;" name="message"></textarea>
			<br><br>
			<span id="dialog_actions_message_remaining"></span>
		</fieldset>
	</form>
	
	<p id="table_message" style="display:none; color:red;">Note: All table requests must also be approved by a manager.</p>
	
	
	<div id="dialog_actions_loading_indicator" style="text-align: center; display: none;">
		<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
	</div>

</div>








	<div id="container">
		<div id="bgwrap">
			<div id="primary_left" style="position: fixed; top: 0px;">
        
				<div id="logo">
					
					<a href="<?=$central->promoter_admin_link_base?>" title="Dashboard">
						<img style="width:200px; margin-left:10px; margin-bottom:10px;" src="<?= $central->global_assets ?>images/ClubbingOwlLogoHeader.png" alt="" />
					</a>

					<p style="text-align:center;">
						<a style="color:#BBB;" target="_new" href="http://www.facebook.com/pages/@/<?= $team_fan_page_id ?>"><?= $promoter->team->t_name ?></a>	
					</p>

					<span style="margin-left:auto;margin-right:auto;margin-bottom:0px;text-align:center;color:#FFF">
						<p>Promoter Admin Panel
							<br>
							<span id="admin_master_time" style="color:grey; font-size:11px;"><?= date('l m/d/Y', time()) ?></span>
						</p>
					</span>
				
					<div id="display_user" style="display:none; width:180px; margin-left:auto; margin-right:auto; margin-top:0px; color:#FFF">
						<img class="pic_square" src="" alt="" style="display:inline-block; float:left; margin-right:10px;"/>
						<span class="name" style="display:inline-block; vertical-align:middle; max-width:120px; word-wrap:break-word;"></span>
					</div>

					<script type="text/javascript">window.admin_display_user();</script>
				
				</div> <!-- logo end -->
				
				
				
				<div id="menu"> <!-- navigation menu -->
					<ul>
						
						<li class="li_dashboard">
							<a href="<?=$central->promoter_admin_link_base?>" class="ajaxify">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/dashboard.png" alt="" />
								<span>Dashboard</span>
							</a>
						</li>
						
						<li class="li_guest_lists">
							<a href="<?=$central->promoter_admin_link_base?>guest_lists/" class="ajaxify">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/List.png" alt="" />
								<span>Guest Lists</span>
							</a>
						</li>
						
						<li class="li_tables">
							<a href="<?=$central->promoter_admin_link_base ?>tables/" class="ajaxify">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/table_icon.png" alt="" />
								<span>Tables</span>
							</a>
						</li>
						
						<li class="li_clients">
							<a href="<?=$central->promoter_admin_link_base?>clients/" class="ajaxify">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/users.png" alt="" />
								<span>Clients</span>
							</a>
						</li>
						
						<li class="li_reports">
							<a href="#" onclick="return false;">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/chart.png" alt="" />
								<span>Reports</span>
							</a>
							<ul>
															
							<?php if(true): ?>	
								<li><a class="ajaxify" href="<?=$central->promoter_admin_link_base?>reports_guest_lists/">Guest Lists</a></li>
								<li><a class="ajaxify" href="<?=$central->promoter_admin_link_base?>reports_sales/">Sales</a></li>
								<li><a class="ajaxify" href="<?=$central->promoter_admin_link_base?>reports_clients/">Clients</a></li>
							<?php endif; ?>
								
							</ul>
						</li>
						
						<li class="li_settings">
							<a href="#" onclick="return false;">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/Wrench.png" alt="" />
								<span>Settings</span>
							</a>
							<ul>
								<li><a class="ajaxify" href="<?=$central->promoter_admin_link_base?>my_profile/">Profile</a></li>
								<li><a class="ajaxify" href="<?=$central->promoter_admin_link_base?>manage_guest_lists/">Guest Lists / Events</a></li>
							</ul>
						</li>
						
						<li class="li_support">
							<a class="ajaxify" href="<?=$central->promoter_admin_link_base?>support/">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/Help.png" alt="" />
								<span>Support</span>
							</a>
						</li>
						
						<li class="li_logout">
							<a href="<?=$central->karma_link_base?>">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_2/logout.png" alt="" />
								<span>Logout</span>
							</a>
						</li>
						
					</ul>
				</div> <!-- navigation menu end -->
				
				
				
			</div> <!-- sidebar end -->

			<div id="primary_right">
				<div class="inner">
					