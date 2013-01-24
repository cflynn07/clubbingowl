<!DOCTYPE HTML>
<html>
<head>
<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"> 
<title>
	<?= $page_data->team->team_name ?>
	<?= (isset($header_custom->title_prefix)) ? $header_custom->title_prefix : ' | ClubbingOwl on Facebook | ' ?>
	<?= $central->title ?>
</title>

<?php if(ENVIRONMENT == 'production'): ?>
	<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
	<script type="text/javascript">console={};console.log = function(){};</script>
<?php endif; ?>

<?php # ------------------------ Begin META tags ------------------------ # ?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php # ------------------------ End META tags ------------------------ # ?>



<?php if(MODE == 'local'): ?>
	
	<link  href="<?= $central->static_assets_domain . 'assets/css/facebook_app_base?cache=' . $central->cache_global_css ?>" rel="stylesheet" type="text/css" />
	<script src="<?= $central->static_assets_domain . 'assets/js/facebook_app_base?cache=' . $central->cache_global_js ?>"  type="text/javascript"></script>
	
<?php else: ?>
	
	<link 	href="<?= $central->static_assets_domain . 'vcweb2/assets/all_facebook_app_base_' . $central->cache_global_css . '.css' ?>" rel="stylesheet" type="text/css" />
	<script  src="<?= $central->static_assets_domain . 'vcweb2/assets/all_facebook_app_base_' . $central->cache_global_js . '.js' ?>" 	type="text/javascript"></script>
	
<?php endif; ?>


<link rel="shortcut icon" href="<?= $central->global_assets ?>images/fav_v_2.jpg"/>

<script type="text/javascript">
var re_name_tag='<?= ($central->vc_user) ? ($central->vc_user->last_name . ', ' . $central->vc_user->first_name) : 'unauth' ?>';
document.write(unescape("%3Cscript src='<?= $central->global_assets_nocdn . 'js/reinvigorate/' ?>re_.js?<?= $central->cache_global_js ?>' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
	reinvigorate.track("k9096-0e37qc95ai");
} catch(err) {}
</script>

<?php if(extension_loaded('newrelic')): ?>
	<?= newrelic_get_browser_timing_header(); ?>
<?php endif; ?>
</head>

<body style="overflow:hidden;">
<div id="fb-root"></div>

<?php if(MODE == 'local'): ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js/facebook_sdk_facebook?cache=' . $central->cache_global_js ?>"></script>
<?php else: ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'vcweb2/assets/all_facebook_sdk_facebook_' . $central->cache_global_js . '.js' ?>"></script>
<?php endif; ?>


<div id="loading_modal" style="display:none;">
	<div id="loading_modal_inner">
		<p><?= $this->lang->line('ad-loading') ?>...</p>
		<img style="border:0;" src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
	</div>
</div>



<?= $this->load->view('front/_common/view_individual_global_pusher_channels', '', true); ?>

<div id="fb_style_inject">
<style type="text/css">
	#fb-root{
		position: absolute; 
		top: 0px;
	}
</style>
</div>