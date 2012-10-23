<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
 
<title><?= $central->title ?></title>

<?php if(ENVIRONMENT == 'production'): ?>
<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
<script type="text/javascript">console={};console.log=function(){};</script>
<?php endif; ?>

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
					
					<span style="margin-left:auto;margin-right:auto;text-align:center;color:#FFF"><p>Super Admin Panel</p></span>
					
				</div> <!-- logo end -->
				<div id="menu"> <!-- navigation menu -->
					<ul>
						
						<li id="li_dashboard">
							<a href="<?=$central->super_admin_link_base?>" class="dashboard">
								<img src="<?=$central->admin_assets?>images/icons/small_icons_3/dashboard.png" alt="" />
								<span class="current">Dashboard</span>
							</a>
						</li>
						
						<li id="li_settings">
							<a href="<?=$central->super_admin_link_base?>settings">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/Wrench.png" alt="" />
								<span>Settings</span>
							</a>
						</li>
						
						<li id="li_settings">
							<a href="<?=$central->super_admin_link_base?>impersonate">
								<img src="<?=$central->admin_assets?>images/icons/small_icons/Wrench.png" alt="" />
								<span>Impersonate</span>
							</a>
						</li>
						
						<li id="li_logout">
							<a href="<?=$central->super_admin_link_base?>logout">
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