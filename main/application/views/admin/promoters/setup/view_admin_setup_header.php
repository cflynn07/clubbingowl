<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xmlns:fb="http://www.facebook.com/2008/fbml">

<head>
<title><?= $central->title ?></title>
<?php 
	//includes all karma/global js and css files + meta tags
	$this->load->view('admin/_common/view_common_header'); 
?>



</head>

<body>
<?= $this->load->view('admin/_common/view_load_admin_fb_sdk', '', true) ?>

	<div id="container">
		<div id="bgwrap">
			<div id="primary_left">
        
				<div id="logo">
					<a href="<?=$central->promoter_admin_link_base?>" title="Dashboard">
						<img style="width:200px; margin-left:10px; margin-bottom:10px;" src="<?= $central->global_assets ?>images/ClubbingOwlLogoHeader.png" alt="" />
					</a>
					<span style="margin-left:auto;margin-right:auto;text-align:center;color:#FFF"><p>Promoter Admin Panel</p></span>
				
					
					<div id="display_user" style="display:none; width:180px; margin-left:auto; margin-right:auto; color:#FFF">
						<img class="pic_square" src="" alt="" style="display:inline-block; float:left; margin-right:10px;"/>
						<span class="name" style="display:inline-block; vertical-align:middle; margin-top:14px;"></span>
					</div>
					<script type="text/javascript">
						window.admin_display_user();
					</script>
					
				</div> <!-- logo end -->
				<div id="menu"> <!-- navigation menu -->
					<ul>
						<li id="li_dashboard" class="current">
							<a href="<?=$central->promoter_admin_link_base?>dashboard" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/dashboard.png" alt="" />
								<span class="current">Admin Setup</span>
							</a>
						</li>

						<li id="li_logout">
							<a href="<?=$central->promoter_admin_link_base?>logout">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_2/logout.png" alt />
								<span>Logout</span>
							</a>
						</li>
					</ul>
				</div> <!-- navigation menu end -->
			</div> <!-- sidebar end -->

			<div id="primary_right">
				<div class="inner">
