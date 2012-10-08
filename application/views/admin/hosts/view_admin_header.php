<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<title><?= $central->title ?></title>

<?php 
	//includes all karma/global js and css files + meta tags
	$this->load->view('admin/_common/view_common_header'); 
?>

<?php $this->load->view('admin/_common/pusher/view_global_host_pusher_notifications'); ?>

</head>
<body>
<div id="fb-root"></div>
<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js?g=facebook_sdk' ?>"></script>

<?php $this->load->view('admin/_common/view_common_admin_chat'); ?>
	
	<div id="container">
		<div id="bgwrap">
			<div id="primary_left" style="position: absolute; top: 0px;">
        
				<div id="logo">
					
					<a href="<?=$central->promoter_admin_link_base ?>" title="Dashboard">
						<img style="width:200px; margin-left:10px; margin-bottom:10px;" src="<?=$central->front_assets?>images/logo.png" alt="" />
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
			
				<div id="menu"> <!-- navigation menu -->
					<ul>
						
						<li <?= (!$this->uri->rsegment(3) || $this->uri->rsegment(3) == 'dashboard') ? 'class="current"' : '' ?>>
							<a href="<?= $central->manager_admin_link_base ?>" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/dashboard.png" alt="" />
								<span class="current">Dashboard</span>
							</a>
						</li>
						
						<li <?= ($this->uri->rsegment(3) == 'logout') ? 'class="current"' : '' ?>>
							<a href="<?=$central->karma_link_base ?>">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_2/logout.png" alt="" />
								<span>Logout</span>
							</a>
						</li>
									
					</ul>
				</div> <!-- navigation menu end -->
			</div> <!-- sidebar end -->

			<div id="primary_right">
				<div class="inner">

<?php 
/*
 * This code block allows for events to create a universal noficiation 
 * that will appear everywhere throught the application on any page
 * using flashdata.
 * */ 
 ?>
<?php /* ----------------------- universal notifications ----------------------- */ ?>

<?php if($notifications = $this->session->flashdata('admin_notifications')): ?>

	<?php foreach($notifications as $item): ?>
		<div class="notification <?=$item->type?>" style="cursor: auto; "> 
			<span></span>
			<div class="text">
				<p><strong><cufon class="cufon cufon-canvas" alt="<?=$item->status?>" style="width: 80px; height: 22px; "><canvas width="98" height="23" style="width: 98px; height: 23px; top: -1px; left: -1px; "></canvas><cufontext><?=$item->status?></cufontext></cufon></strong><?=$item->message?></p> 
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php /* ----------------------- end universal notifications ----------------------- */ ?>