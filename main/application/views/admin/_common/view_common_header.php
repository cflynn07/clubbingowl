<?php if(ENVIRONMENT == 'production' && false): ?>
<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
<script type="text/javascript">console={};console.log=function(){};</script>
<?php endif; ?>

<?php # ------------------------ Begin META tags ------------------------ # ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=1000; user-scalable=1;" /> 


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









<script type="text/javascript">
jQuery(function(){
	
	if(true || jQuery.isMobile()){
		
		jQuery('body').css('background', 'none');
		jQuery('#primary_left').hide();
		jQuery('#primary_right').css({
			margin: 	0,
			width: 		'980px',
			position: 	'absolute',
			top: 		0,
			left: 		0
		});  
		jQuery('#primary_right > .inner').css({
			padding: '10px',
			margin: 0,
			width: '980px',
			overflow: 'hidden',
			'min-width': 0
		});
		jQuery('#primary_right > .inner > div').css({
			width: 			'980px',
			'max-width': 	'980px'
		});
		
		jQuery('#primary_right > .inner #admin_managers_tables_wrapper > div#tabs').css({
			width: '980px'
		});
		
		jQuery('div.ui-widget ul.ui-tabs-nav li').each(function(){
		
			jQuery(this).css({
				'padding': '5px 0 5px 0',
			}).find('a').addClass('button-action btn-link').css({
				'font-size': '18px'
			});
		
		});
		
		
		jQuery('*[data-mobile_font]').each(function(){
			jQuery(this).css({
				'font-size': jQuery(this).attr('data-mobile_font')
			});
		});
		
		
		var EVT = window.ejs_view_templates_admin_hosts || window.ejs_view_templates_admin_promoters || window.ejs_view_templates_admin_managers;
				
		var mobile_menu = jQuery('#primary_right > #mobile_menu');
		if(!mobile_menu.length){


			var links = [];
			var prefix = '';
			jQuery('#primary_left div#menu > ul > li').each(function(){
				
				var first_link = jQuery(this).find('> a:first');				
				
				if(jQuery(this).find('> ul').length){
					
					var prefix = jQuery(this).find('a:first > span').html();
					jQuery(this).find('> ul li a').each(function(){
						
						links.push({
							title: 	prefix + ' - ' + jQuery(this).html(),
							href: 	jQuery(this).attr('href')
						});
						
					});
					
				}else{
					
					links.push({
						title: 	first_link.find('> span').html(),
						href: 	first_link.attr('href')
					});
					
				}
								
			});
			
			var html = new EJS({
				text: EVT['admin_mobile_menu']
			}).render({
				links: links
			});
	
			jQuery('#primary_right').prepend(html);
			jQuery('#primary_right #mobile_menu select#mobile_menu_nav').bind('change', function(){
				
				var value = jQuery(this).val();
				jQuery('#primary_left div#menu a[href="' + value + '"]').trigger('click');
				
			});
			
			jQuery.fbUserLookup([window.admin_users_oauth_uid], '', function(rows){
				if(rows.length){
					jQuery('#primary_right #mobile_menu span[data-user_name]').html(rows[0].name);
				}
			});
			
			
			jQuery('#primary_right > .inner').css({
				top: '40px'
			});
			
		}
		
		
		
		
	}
	  
});
</script>
