<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"> 
	
	<title><?= (isset($header_custom->title_prefix)) ? $header_custom->title_prefix : '' ?><?= $central->title ?></title>
	
	<?php //Verify that the expiration time of the user's access_token is at least +24 in the future from the current time, if not re-login to refresh access token ?>
	<script type="text/javascript">
	window.vc_server_auth_session=<?= ($central->vc_user && isset($central->vc_user->access_token_expiration_time) && ($central->vc_user->access_token_expiration_time > (time() + 300))) ? 'true' : 'false'  ?>;
	window.vc_user_invitations=<?= 		(isset($invitations)) 				? json_encode($invitations) 				: '[]'	?>;
	window.vc_sticky_notifications=<?= 	(isset($user_sticky_notifications)) ? json_encode($user_sticky_notifications) 	: '[]'	?>;
	</script>
	
	<?=
		//includes all front/global js and css files + meta tags
		$this->load->view('front/_common/view_front_common_header', '', true)
	?>

</head>
<body>
<div id="fb-root"></div>
<?php if(MODE == 'local'): ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js/facebook_sdk?cache=' . $this->config->item('cache_global_js') ?>"></script>
<?php else: ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'vcweb2/assets/all_facebook_sdk_' . $this->config->item('cache_global_js') . '.js' ?>"></script>
<?php endif; ?>



<div id="loading_modal" style="display:none;">
	<div id="loading_modal_inner">
		<p><?= $this->lang->line('ad-loading') ?>...</p>
		<img style="border:0;" src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
	</div>
</div>




<?= $this->load->view('front/_common/view_individual_global_pusher_channels', '', true); ?>

<script type="text/javascript">
var re_name_tag='<?= ($central->vc_user) ? ($central->vc_user->last_name . ', ' . $central->vc_user->first_name) : 'unauth' ?>';
document.write(unescape("%3Cscript src='<?= $central->global_assets_nocdn . 'js/reinvigorate/' ?>re_.js?<?= $central->cache_global_js ?>' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{
  var key = "k9096-0e37qc95ai";
  reinvigorate.url_override='';
  reinvigorate.ajax_track=function(url){
    try{
      delete reinvigorate.wkeys[key];
      reinvigorate.url_override=url;
      reinvigorate.track(key);
    } catch(err){}
  };
  reinvigorate.url_filter=function(url){
    if(reinvigorate.url_override !== ''){
      url=reinvigorate.url_override;
      reinvigorate.url_override='';
    }
    return url.replace(/^https?:\/\/(www\.)?/,"http://");
  };
  reinvigorate.track(key);
} catch(err) {}
</script>

<div id="invitations_dialog" style="display:none;">
	<h2><?= $this->lang->line('id-h') ?></h2>
	<p><?= $this->lang->line('id-p') ?></p>
	<table class="ui-widget">
		<thead class="ui-widget-header">
			<tr>
				<th><?= $this->lang->line('id-th_manager') ?></th>
				<th><?= $this->lang->line('id-th_team_name') ?></th>
				<th><?= $this->lang->line('id-th_inv_exp') ?></th>
				<th><?= $this->lang->line('id-th_inv_type') ?></th>
				<th style="min-width:150px;"><?= $this->lang->line('id-th_act') ?></th>
			</tr>
		</thead>
		<tbody class="ui-widget-content">
			<tr>
				<td></td>
			</tr>
		</tbody>
	</table>
	<p><?= $this->lang->line('id-note') ?></p>
</div>

<div id="user_notifications"></div>
<script type="text/javascript">window.vc_display_sticky_notifications();</script>

<div id="user">
	<div class="center">
		<ul>
		</ul>
	</div>
</div>

<div id="spacer">
</div>

<header id="header">
	<div class="center">
			
		<a href="<?= $central->front_link_base ?>"><h1 class="logo">Clubbing Owl</h1></a>
		
		<nav id="navigation">
			
			<ul class="menu">
				<li><a class="nav_link" href="<?= $central->front_link_base ?>friends/" title="<?= $this->lang->line('m-friends') ?>"><?= $this->lang->line('m-friends') ?></a></li>
				<li><a class="nav_link" href="<?= $central->front_link_base ?>promoters/cities/" title="<?= $this->lang->line('m-promoters') ?>"><?= $this->lang->line('m-promoters') ?></a>
					<ul class="drop">
						
						<?php foreach($active_promoter_cities as $city): ?>
							<li><a href="<?= $central->front_link_base ?>promoters/cities/<?= $city->c_url_identifier ?>/"><?= $city->c_name . ', ' . $city->c_state ?></a></li>
						<?php endforeach; ?>
						
						<li class="footer"><a href="<?= $central->front_link_base ?>promoters/cities/"><?= $this->lang->line('m-all_cities') ?></a></li>
					</ul>
				</li>
				<li><a class="nav_link" href="<?= $central->front_link_base ?>venues/" title="<?= $this->lang->line('m-venues') ?>"><?= $this->lang->line('m-venues') ?></a>
					<ul class="drop">
						
						<?php foreach($active_cities as $city): ?>
							<li><a href="<?= $central->front_link_base ?>venues/<?= $city->c_url_identifier ?>/"><?= $city->c_name . ', ' . $city->c_state ?></a></li>
						<?php endforeach; ?>
						
						<li class="footer"><a href="<?= $central->front_link_base ?>venues/"><?= $this->lang->line('m-all_cities') ?></a></li>
					</ul>
				</li>
				
				
				
				
				 <li id="vc_search" class="search"><a href="javascript: void(0);" class="icon"></a>
				    <div id="search-drop">
				    	
				      <form id="search">
				        <label for="search-input"><?= $this->lang->line('m-search') ?></label>
				        <input type="search" id="search-input" placeholder="<?= $this->lang->line('m-search') ?>" />
				      </form>
				      
				      <style type="text/css">
				      	ul.ui-autocomplete{
				      		display: none !important;
				      	}
				      </style>
				      
				      <div id="no_search_results_msg" style="display:none;"><?= $this->lang->line('ad-no_search_results') ?></div>
				      <div id="search-drop-results" class="drop" style="display:none;">
				      		<ul>
				      			<li class="search_header search_promoters"><?= $this->lang->line('m-promoters') ?></li>
				      			<li class="search_header search_venues"><?= $this->lang->line('m-venues') ?></li>
				      			<li class="search_header search_friends"><?= $this->lang->line('m-friends') ?></li>
				      			<li class="search_header search_lists"><?= $this->lang->line('m-lists') ?></li>
				      		</ul>
				      </div>
				      
				    </div>
				 </li>
				
				
				
				<li class="login">
					<span><?= $this->lang->line('m-search') ?></span>
				    <div id="login-drop">
				        <div class="drop right">
				          <p><?= $this->lang->line('m-login_message') ?></p>
						  <p><a class="no-ajaxy vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-small.png" alt="Facebook Connect" /></a></p>
				        </div>
				    </div>
				</li>
				
				<li class="language">
					<span><?= strtoupper($this->config->item('current_lang_code')) ?></span>
					
					
					<div id="language-drop">
						<ul class="drop right">
							<li><a class="no-ajaxy" href="<?= $central->scheme . '://' . 'www.' . SITE . '.' . TLD ?>">English</a></li>
							<li><a class="no-ajaxy" href="<?= $central->scheme . '://' . 'de.' . SITE . '.' . TLD ?>">Deutsch</a></li>
							<?php if(false): ?>
							<li><a href="<?= $central->scheme . '://' . 'de.vibecompass.' . TLD . $central->request_uri  ?>">Deutsch (beta)</a></li>
							<li><a href="<?= $central->scheme . '://' . 'ja.vibecompass.' . TLD . $central->request_uri  ?>">日本語 (beta)</a></li>
							<?php endif; ?>
						</ul>
					</div>
					
					
				</li>
			</ul>
	
	<div id="mobile-drop"></div>
	
		</nav>

	</div>
</header>
<?php //This javascript is located here to make manipulation of menu ul instantanious. (No DOM-search delay) ?>
<script type="text/javascript">
	window.module.VCAuth.prototype.update_menu();
</script>

<div id="fb_style_inject">
	<style type="text/css">
		#fb-root{
			position: absolute; 
			top: 0px;
		}
	</style>
</div>

<div role="main" class="center">
