<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<title><?= $central->title ?></title>

<?php 
	//includes all karma/global js and css files + meta tags
	$this->load->view('admin/_common/view_common_header'); 
?>

<?php //$this->load->view('admin/_common/pusher/view_global_host_pusher_notifications'); ?>


</head>
<body>
<?= $this->load->view('admin/_common/view_load_admin_fb_sdk', '', true) ?>

<script type="text/javascript">
	

	window.admin_users_oauth_uid = '<?= $users_oauth_uid ?>';

</script>

<?php $this->load->view('admin/_common/view_common_admin_chat'); ?>
	
	<div id="container">
		<div id="bgwrap">
			
			
			
			
			
			
			
			
						
			<div id="primary_left" style="position:absolute; top:0px;">
        
				<div id="logo">
					
					<a href="<?=$central->promoter_admin_link_base ?>" title="Dashboard">
						<img style="width:200px; margin-left:10px; margin-bottom:10px;" src="<?= $central->global_assets ?>images/ClubbingOwlLogoHeader.png" alt="" />
					</a>
					
					<p style="text-align:center; color:#BBB;">
						<?= $data->team->team->team_name ?>
					</p>
					
					<span style="margin-left:auto;margin-right:auto;text-align:center;color:#FFF">
						<p>Host Admin Panel
							<br>
							<span id="admin_master_time" style="color:grey; font-size:11px;"><?= date('l m/d/Y', time()) ?></span>
						</p>
					</span>
					
					<div id="display_user" style="display:none; width:180px; margin-left:auto; margin-right:auto; color:#FFF">
						<img class="pic_square" src="" alt="" style="display:inline-block; float:left; margin-right:10px;"/>
						<span class="name" style="display:inline-block; vertical-align:middle; margin-top:14px;"></span>
					</div>
					<script type="text/javascript">
						window.admin_display_user();
					</script>
					
				</div> <!-- logo end -->
				
				<style>
					span.menu_dates {
						padding-top: 0 !important;
						font-size: 12px !important;
					}
				</style>
				
				<div id="menu"> <!-- navigation menu -->
					<ul> 
						
						<li>
							<a class="ajaxify" href="<?=$central->front_link_base ?>admin/hosts/">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/dashboard.png" alt="" />
								<span>Dashboard</span>
							</a>
						</li>
						
						<li>
							<a href="<?=$central->front_link_base ?>">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_2/logout.png" alt="" />
								<span>Logout</span>
							</a>
						</li>
									
					</ul>
				</div> <!-- navigation menu end -->
			</div> <!-- sidebar end -->










			<div style="" id="primary_right">
				<div style="" class="inner">
					


