<?php if(ENVIRONMENT == 'production' && false): ?>
<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
<script type="text/javascript">console={};console.log=function(){};</script>
<?php endif; ?>

<?php # ------------------------ Begin META tags ------------------------ # ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="format-detection" content="telephone=no">
<?php # ------------------------ End META tags ------------------------ # ?>

<?php
	//global javascript variables for team chat system
	$vc_tc_obj = new stdClass;
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
?>
<script type="text/javascript">window.vc_tc_obj=<?= json_encode($vc_tc_obj) ?>;</script>

<link href="<?= $central->static_assets_domain . 'assets/css?g=admin_base&cache=' . $central->cache_global_css ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js?g=admin_base&subg=' . $subg . '&cache=' . $central->cache_global_js ?>"></script>

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