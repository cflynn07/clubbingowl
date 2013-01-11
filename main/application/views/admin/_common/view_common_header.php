<?php if(ENVIRONMENT == 'production' && false): ?>
<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
<script type="text/javascript">console={};console.log=function(){};</script>
<?php endif; ?>

<?php # ------------------------ Begin META tags ------------------------ # ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">


<?php # ------------------------ End META tags ------------------------ # ?>

<?php
	//global javascript variables for team chat system
	$vc_tc_obj = new stdClass;
	
	/**
	 * Will be false in super-admin area
	 */
	if(isset($team_fan_page_id) && isset($team_chat_members)){
		
		$vc_tc_obj->team_chat_members 	= $team_chat_members;
		$vc_tc_obj->team_fan_page_id	= $team_fan_page_id;
		
		$users = array();
		foreach($team_chat_members->managers as $man){
			$users[] = $man->oauth_uid;
		}
		foreach($team_chat_members->promoters as $pro){
			$users[] = $pro->oauth_uid;
		}
		foreach($team_chat_members->hosts as $host){
			$users[] = $host->oauth_uid;
		}
		$vc_tc_obj->users			  	= $users;
	}
?>
<script type="text/javascript">window.vc_tc_obj=<?= json_encode($vc_tc_obj) ?>;</script>

<?php if(MODE == 'local'): ?>

	<link   href="<?= $central->static_assets_domain . 'assets/css/admin_base?cache=' . $central->cache_global_css ?>" rel="stylesheet" type="text/css" />
	<script  src="<?= $central->static_assets_domain . 'assets/js/ejs_templates_admin_' . $subg . '/' . 'en' . '?cache=' . $central->cache_global_js ?>" type="text/javascript"></script>
	<script  src="<?= $central->static_assets_domain . 'assets/js/admin_base/' . $subg . '?cache=' . $central->cache_global_js ?>" type="text/javascript"></script>
	
<?php else: ?>

	<link 	href="<?= $central->static_assets_domain . 'vcweb2/assets/all_admin_base_' . $central->cache_global_css . '.css'; ?>" rel="stylesheet" type="text/css" />
	<script  src="<?= $central->static_assets_domain . 'vcweb2/assets/all_ejs_templates_admin_' . $subg . '_' . 'en' . '_' . $central->cache_global_js . '.js' ?>" type="text/javascript"></script>
	<script  src="<?= $central->static_assets_domain . 'vcweb2/assets/all_admin_base_' . $subg . '_' . $central->cache_global_js . '.js'; ?>" type="text/javascript"></script>	
	
<?php endif; ?>


<!--[if IE 8]>
	<script type='text/javascript' src='<?=$central->admin_assets?>js/excanvas.js'></script>
	<link rel="stylesheet" href="<?=$central->admin_assets?>css/IEfix.css" type="text/css" media="screen" />
<![endif]--> 
 
<!--[if IE 7]>
	<script type='text/javascript' src='<?=$central->admin_assets?>js/excanvas.js'></script>
	<link rel="stylesheet" href="<?=$central->admin_assets?>css/IEfix.css" type="text/css" media="screen" />
<![endif]--> 

<link rel="shortcut icon" href="<?=$central->global_assets?>images/fav_v_2.jpg">



<?php if(extension_loaded('newrelic')): ?>
	<?= newrelic_get_browser_timing_header(); ?>
<?php endif; ?>