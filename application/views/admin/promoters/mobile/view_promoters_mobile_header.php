<!DOCTYPE html>
<html>
    <head>
    	<?php if(ENVIRONMENT == 'production'): ?>
		<?php //quick, easy, dirty way of disabling all javascript console debugging if this is production code ?>
			<script type="text/javascript">
			console = {};
			console.log = function(){};
			</script>
		<?php endif; ?>
		
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= $title ?></title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
        <style>
            /* App custom styles */
        </style>
        
<?php if(false): ?>        
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
	        
	<?php if($central->scheme == 'http'): //load https version if site loaded via https ?>
		<script src="http://js.pusher.com/1.11/pusher.min.js"></script>
	<?php elseif($central->scheme == 'https'): ?>
		<script src="https://d3dy5gmtp8yhk7.cloudfront.net/1.11/pusher.min.js"></script>	
	<?php endif; ?>
<?php endif; ?>

	<script type="text/javascript" src="<?= $central->global_assets_nocdn . 'js/jquery/jquery1.7.2.min.js?' . $central->cache_global_js ?>"></script>
	<script type="text/javascript" src="<?= $central->global_assets_nocdn . 'js/jquery/jquery-ui-1.8.18.min.js?' . $central->cache_global_js ?>"></script>
	<script type="text/javascript" src="<?= $central->global_assets_nocdn . 'js/pusher/pusher-1.11.js?' . $central->cache_global_js ?>"></script>


        <?php //additional global javascripts ?>
        <script type="text/javascript" src="<?= $central->global_assets ?>js/jquery.cookies.2.2.0.min.js?<?= $central->cache_global_js ?>"></script>
		<script type="text/javascript" src="<?= $central->global_assets ?>js/json2.js?<?= $central->cache_global_js ?>"></script>
		<script type="text/javascript" src="<?= $central->global_assets ?>js/json_parse.js?<?= $central->cache_global_js ?>"></script>
		<script type="text/javascript" src="<?= $central->global_assets ?>js/json_parse_state.js?<?= $central->cache_global_js ?>"></script>
        
        <script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-28170126-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
        
        <link rel="shortcut icon" href="<?=$central->global_assets?>images/fav_v_1.jpg">
        
        <?php if(extension_loaded('newrelic')): ?>
			<?= newrelic_get_browser_timing_header(); ?>
		<?php endif; ?>
		
    </head>
    <body>
    	<div id="fb-root"></div>	
		<script type="text/javascript">
			
			window.fbAsyncInit = function() {
			    FB.init({appId: '<?=$central->facebook_app_id?>',
			    		status: true,
			    		cookie: true,
			             xfbml: true});
			
				//indicates facebook has completed loading
			  	fbApiInit = true;
			};
			
			//used to load code within body after facebook init complete
			function fbEnsureInit(callback) {
			    if(!window.fbApiInit) {
			        setTimeout(function() {fbEnsureInit(callback);}, 50);
			    } else {
			        if(callback) {
			            callback();
			        }
			    }
			}
			  
			(function() {
			    var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol +
			  			'//connect.facebook.net/en_US/all.js';
						document.getElementById('fb-root').appendChild(e);
			 }());
		
		</script>