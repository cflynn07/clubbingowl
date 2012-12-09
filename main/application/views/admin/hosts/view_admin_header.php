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

<?php $this->load->view('admin/_common/view_common_admin_chat'); ?>
	
	<div id="container">
		<div id="bgwrap">
			<div id="primary_left" style="position: absolute; top: 0px;">
        
				<div id="logo">
					
					<a href="<?=$central->promoter_admin_link_base ?>" title="Dashboard">
						<img style="width:200px; margin-left:10px; margin-bottom:10px;" src="<?= $central->global_assets ?>images/ClubbingOwlLogoHeader.png" alt="" />
					</a>
					
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
						
						<?php
						
							$yesterday 		= date('Y-m-d', strtotime('today -1 days'));
							$tomorrow		= date('Y-m-d', strtotime('today +1 days'));
							$today_plus_2 	= date('Y-m-d', strtotime('today +2 days'));
							
						?>
						
						
						<li data-date="<?= $yesterday ?>">
							<a class="ajaxify" href="<?= $central->front_link_base ?>admin/hosts/<?= $yesterday ?>/" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/List.png" alt="" />
								
								<span class="menu_dates">Yesterday:<br/>(<?= $yesterday ?>)</span>
															
							</a>
						</li>
						
						
						
						<li data-date="<?= $current_date ?>">
							<a class="ajaxify" href="<?= $central->front_link_base ?>admin/hosts/<?= $current_date ?>/" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/List.png" alt="" />
								
								<span class="menu_dates">Today:<br/>(<?= $current_date ?>)</span>
													
							</a>
						</li>
						
						
						
						<li data-date="<?= $tomorrow ?>">
							<a class="ajaxify" href="<?= $central->front_link_base ?>admin/hosts/<?= $tomorrow ?>/" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/List.png" alt="" />
								
								<span class="menu_dates">Tomorrow:<br/>(<?= $tomorrow ?>)</span>
														
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

			<div id="primary_right">
				<div class="inner">