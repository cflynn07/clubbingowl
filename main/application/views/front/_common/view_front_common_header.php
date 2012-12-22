<?php if(ENVIRONMENT == 'production'):
	//quick, easy, dirty way of disabling all javascript console debugging if this is production code 
	
		if(!isset($central->vc_user) || !$central->vc_user || (isset($central->vc_user->oauth_uid) && $central->vc_user->oauth_uid != '504405294')):
?>
<script type="text/javascript">console={};console.log=function(){};</script>
		<?php endif; ?>
<?php endif; ?>

<?php # ------------------------ Begin META tags ------------------------ # ?>
<meta http-equiv="Content-Type" 	content="text/html; charset=UTF-8" />
<meta name="description" 			content="<?= (isset($header_custom->page_description)) ? $header_custom->page_description : $this->lang->line('ad-description') ?>">
<meta name="keywords" content="clubbing, owl, clubbing owl, clubs, venues, guestlists, guest-lists, guest lists, night life, nightlife, promoters" />

<meta name="viewport" 				content="width=device-width">
<meta http-equiv="Content-Language" content="<?= $this->config->item('current_lang_code') ?>">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" 	content="IE=edge,chrome=1">


<meta name="apple-mobile-web-app-capable" content="yes">


<meta property="og:type" 			content="website"/>
<meta property="og:title" 			content="<?= (isset($header_custom->title_prefix)) ? $header_custom->title_prefix : '' ?><?= $central->title ?>"/>
<meta property="og:url" 			content="<?= (isset($header_custom->url)) ? $header_custom->url : 'https://' . $this->config->item('active_subdomain') . '.' . SITE . '.' . TLD . '/' ?>"/>
<meta property="og:image" 			content="<?= (isset($header_custom->page_image)) ? $header_custom->page_image : ($central->front_assets . 'images/ClubbingOwlBackgroundWeb_small2.png') ?>"/>
<meta property="og:site_name" 		content="ClubbingOwl"/>

<meta property="fb:app_id" 			content="<?= $central->facebook_app_id ?>"/>
<meta property="og:description"		content="<?= (isset($header_custom->page_description)) ? $header_custom->page_description : $this->lang->line('ad-description') ?>"/>
<link rel="apple-touch-icon-precomposed" href="<?= $central->front_assets ?>images/square_icon.png" />
<?php # ------------------------ End META tags ------------------------ # ?>


<link rel="shortcut icon" href="<?= $central->front_assets ?>images/FavIconClubbingOwl16x16.png"/>
<link rel="canonical" href="<?= (isset($header_custom->url)) ? $header_custom->url : 'http://' . $this->config->item('active_subdomain') . '.' . SITE . '.' . TLD .  '/' ?>" />


<?php if(MODE == 'local'): ?>

	<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js/ejs_templates/' . $this->config->item('current_lang_code') . '?cache=' . $central->cache_global_js ?>"></script>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js/base?cache=' . $central->cache_global_js ?>"></script>
	<link href="<?= $central->static_assets_domain . 'assets/css/base?cache=' . $central->cache_global_css ?>" rel="stylesheet" type="text/css" />

<?php else: ?>
	
	<script 		 src="<?= $central->static_assets_domain . 'vcweb2/assets/all_ejs_templates_' . $this->config->item('current_lang_code') . '_' . $central->cache_global_js . '.js' ?>" type="text/javascript"></script>
	<script 		 src="<?= $central->static_assets_domain . 'vcweb2/assets/all_base_' . $central->cache_global_js . '.js' ?>" type="text/javascript"></script>
	<link 			href="<?= $central->static_assets_domain . 'vcweb2/assets/all_base_' . $central->cache_global_css . '.css' ?>" rel="stylesheet" type="text/css" />
	
<?php endif; ?>


<?php if(extension_loaded('newrelic')): ?>
	<?= newrelic_get_browser_timing_header(); ?>
<?php endif; ?>