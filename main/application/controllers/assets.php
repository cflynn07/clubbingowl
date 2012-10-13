<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for events showcase page
 * 
 */
class Assets extends MY_Common_Controller {
	
	private $global_assets;
	private $admin_assets;
	private $front_assets;
	private $facebook_assets;
	private $dynamic_assets;
	
	/**
	 * Controller constructor. Perform any universal operations here.
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
		
		
		/**
		 * This controller should not be accessed from production or staging environments unless by CLI
		 * to generate asset files
		 * 
		 */
		if(MODE == 'production' || MODE == 'staging')
			if(php_sapi_name() != 'cli')
				show_404();
				
				
				
		$this->load->driver('minify');
		$this->load->library('library_memcached', '', 'memcached');
		
		$this->global_assets 	= FCPATH . 'vcweb2/assets/global/';
		$this->admin_assets 	= FCPATH . 'vcweb2/assets/admin/';
		$this->front_assets 	= FCPATH . 'vcweb2/assets/web/';
		$this->facebook_assets 	= FCPATH . 'vcweb2/assets/facebook/';
		$this->dynamic_assets 	= 'dynamic_assets/';
		
		header('Pragma: public');
		header('Cache-Control: max-age=313892594, public');
		header('Date: Sun, 27 May 2012 18:40:00 GMT');
		header('Expires: Thu, 15 Apr '.(date('Y')+10).' 20:00:00 GMT');
		header('Last-Modified: Thu, 10 May 2012 19:00:00 GMT');
		header_remove('set-cookie');
			
	}
	
	/**
	 * Combine and minify css files
	 */
	function css($group = ''){
		
		header('Content-Type: text/css');
		
		
	//	$group = $this->input->get('g');
		# ----------------------- ASSET GROUPS --------------------- #

		//globally included css
		$global_css_include = array();
		//admin included css
		$admin_css_include = array();
		//cscs included css
		$front_css_include = array();
		//
		$facebook_css_include = array();
		//dynamic css
		$dynamic_css = array();
	
		switch($group){
			case 'base':
				
				$front_css_include = array(
					'jquery_ui/redmond/jquery-ui-1.8.20.custom',
					'base',
					'invite',
					'friends',
					'history',
					'home',
					'landing',
					'locations',
					'profile',
					'promoter',
					'venue',
					'venues',
					'team',
					'gl_floorplan_layout'
				);
				
				$global_css_include = array(
					'jquery_notify/jquery.notify',
					'kswedberg-jquery-cluetip-477822d/jquery.cluetip'
				);
				
				break;
			case 'admin_base':
				
				
				$global_css_include = array(
					'pageslider/jquery.pageslide',
					'jquery_notify/jquery.notify',
					'imgareaselect-default'
				);
				
				$admin_css_include = array(
					'jQuery_UI_uniq',
					'iPhoneCheckboxes',
					'visualize',
					'style',
					'jquery.cleditor',
					
					//pages
					'page/admin_team_chat',
					'page/admin_promoter_dashboard'					
				);
			
				break;
			case 'facebook_app_base':
				
				$global_css_include = array(
			//		'jquery-ui-1.8.14.custom',
					'jquery_notify/jquery.notify'
				);
				$front_css_include = array(
					'jquery_ui/redmond/jquery-ui-1.8.20.custom',
					'base',
					'facebook',
					'locations',
					'venues',
					'promoter',
					'gl_floorplan_layout'
				);
				
				break;
			default:
				break;
		}


		$output_file_name = 'all_' . $group . '_';


		# ----------------------- /ASSET GROUPS --------------------- #
	
	
	
		//combine with base and remove duplicates
				
		foreach($global_css_include as &$css){
			$css = $this->global_assets . 'css/' . $css . '.css';
		}unset($css);		
		foreach($admin_css_include as &$css){
			$css = $this->admin_assets . 'css/' . $css . '.css';
		}unset($css);		
		foreach($front_css_include as &$css){
			$css = $this->front_assets . 'css/' . $css . '.css';
		}unset($css);
		foreach($facebook_css_include as &$css){
			$css = $this->facebook_assets . 'css/' . $css . '.css';
		}unset($css);
		
		$merge_array = array_merge($front_css_include, $admin_css_include, $facebook_css_include, $global_css_include);
		$output = $this->minify->combine_files($merge_array, 'css', true);
		
		
		
			
		if(MODE == 'local' && php_sapi_name() != 'cli'){
			$this->output->set_output($output);
		}else{
			$filename = FCPATH . 'vcweb2/assets/' . $output_file_name . $this->config->item('cache_global_css') . '.css';
			if(!file_exists($filename)){
				$fh = fopen($filename, 'w+');
				fwrite($fh, $output);
				fclose($fh);
			}
		}
		
		
		
		
		
	}
	
	/**
	 * Combine and minify js files
	 */
	function js($group = '', $subg = ''){
		
		header('Content-Type: text/javascript');
		
		//$group = $this->input->get('g');
		# ----------------------- ASSET GROUPS --------------------- #
		
		
		$group_assets = array();
		
	
		switch($group){
			case 'base':
				
				$group_assets = array(
					
					array('jquery/jquery1.7.2.min',										'global_js'),
					array('history',													'global_js'),
					array('jquery.history',												'global_js'),
					array('jquery/jquery-ui-1.8.18.min',								'global_js'),
					array('pusher/pusher-1.11',											'global_js'),
					array('jquery.cookies.2.2.0.min',									'global_js'),					
					array('jquery_cookies_domain_settings', 							'global_js'),
					array('json2',														'global_js'),
					array('jquery_notify/jquery.notify',								'global_js'),
					array('kswedberg-jquery-cluetip-477822d/jquery.cluetip.all.min',	'global_js'),
					array('charts/highcharts',											'global_js'),
					array('charts/themes/gray',											'global_js'),
					array('jquery.maskedinput-1.3.min',									'global_js'),
					array('ejs/ejs_0.9_alpha_1_production.min',							'global_js'),
					array('underscore/underscore.min',									'global_js'),
					array('backbone/backbone',											'global_js'),
					
					array(
						array('front/view_dynamic_assets_js_front_global', ''), 		'dynamic'
					),
					array(
						array('front/view_dynamic_assets_js_front_pusher_init', ''), 	'dynamic'
					),
					
					
					array('lib/modernizr',												'front_js'),
					array('app/app',													'front_js'),
					array('page/promoter',												'front_js'),
					array('app/event_handler',											'front_js'),
					array('app/google_analytics',										'front_js'),
					
					array('app/vc_auth',												'front_js'),
					
					array('app/vc_global_event_callbacks',								'front_js'),
					array('app/facebook_invite',										'front_js'),
					array('app/vc_invitations',											'front_js'),
					array('app/sticky_notifications',									'front_js'),
					array('app/ajaxify_front',											'front_js'),
					array('app/auto_suggest',											'front_js'),
					array('page/home_news_feed',										'front_js'),
					array('page/team_page',												'front_js'),
					array('page/friends_feed',											'front_js'),
					array('page/individual_friend',										'front_js'),
					array('page/promoter_profile',										'front_js'),
					array('page/promoter_pusher_presence_channels',						'front_js'),
					array('page/promoter_guest_list_individual',						'front_js'),
					array('page/app_requests',											'front_js'),
					array('page/venues_home',											'front_js'),
					array('page/venue_guest_list_individual',							'front_js'),
					array('page/venue_profile',											'front_js'),
					array('page/profile',												'front_js'),
					array('page/facebook_plugin',										'front_js'),
					array('app/router',													'front_js')
					
				);
				
				// --------------------------- base group assets 1 -------------------------------		
				
				break;
			case 'facebook_sdk':				
			
				$group_assets = array(
					array(
						array('front/view_dynamic_assets_js_front_facebook_sdk', ''), 				'dynamic'
					),
				);
				
				break;
			case 'facebook_sdk_facebook':
				
				$group_assets = array(
					array(
						array('facebook/view_dynamic_assets_js_facebook_facebook_sdk', ''), 		'dynamic'
					),
				);
		
				break;
			case 'facebook_sdk_admin':
				
				$group_assets = array(
					array(
						array('admin/view_dynamic_assets_js_admin_facebook_sdk', ''), 				'dynamic'
					),
				);

				break;
			case 'facebook_app_base':
				
				$group_assets = array(
				
					array('jquery/jquery1.7.2.min',											'global_js'),
					array('jquery/jquery-ui-1.8.18.min',									'global_js'),
					array('pusher/pusher-1.11',												'global_js'),
					array('jquery.cookies.2.2.0.min',										'global_js'),
					array('json2',															'global_js'),
					array('jquery_notify/jquery.notify',									'global_js'),
					array('jquery.maskedinput-1.3.min',										'global_js'),
					array('ejs/ejs_0.9_alpha_1_production.min',								'global_js'),
					array('facebook_app/facebook_app_events',								'global_js'),
					array('facebook_app/facebook_vcauth',									'global_js'),
					array('underscore/underscore.min',										'global_js'),
					array('backbone/backbone',												'global_js'),
							
					array('google_analytics', 												'admin_js'),
					
					array(
						array('facebook/view_dynamic_assets_js_facebook_global', ''),		'dynamic'
					),
					
					array('page/facebook_plugin',											'front_js'),
					array('app/router',														'front_js')
					
				);
							
				break;
			case 'admin_base':

				$group_assets = array(

					array('jquery/jquery1.7.2.min',											'global_js'),
					array('jquery/jquery-ui-1.8.18.min',									'global_js'),
					array('pusher/pusher-1.11',												'global_js'),
					array('history',														'global_js'),
					array('jquery.history',													'global_js'),
					array('jquery.cookies.2.2.0.min',										'global_js'),
					array('custom',															'admin_js'),
					array('ejs/ejs_0.9_alpha_1_production.min',								'global_js'),
					array('admin_event_handler',											'admin_js'),
					
					
					array('json2',															'global_js'),
					
					array('json_parse',														'global_js'),
					array('json_parse_state',												'global_js'),
					array('pageslider/jquery.pageslide.min',								'global_js'),
					array('jquery_notify/jquery.notify',									'global_js'),
					array('soundmanager/soundmanager2-nodebug-jsmin',						'global_js'),
					array('jquery.ui.tooltip',												'global_js'),
					array('jquery.price_format.1.7.min',									'global_js'),
					array('charts/highcharts',												'global_js'),
					array('jquery.jcarousel.min',											'global_js'),
					array('jquery.ocupload-1.1.2',											'global_js'),
					array('jquery.imgareaselect.min',										'global_js'),
					array('data_tables/js/jquery.dataTables.min',							'global_js'),
					array('jquery.dumbformstate-1.01',										'global_js'),
					array('underscore/underscore.min',										'global_js'),
					array('backbone/backbone',												'global_js'),
					
		//			array('jquery.maskedinput-1.3.min',										'global_js'),
		//			array('ejs/ejs_0.9_alpha_1_production.min',								'global_js'),
		
					array(
						array('front/view_dynamic_assets_js_front_global', ''), 			'dynamic'
					),
						
					array('jquery.ui.touch-punch.min',										'admin_js'),
					array('cufon-yui',														'admin_js'),
					array('ColaborateLight_400.font',										'admin_js'),
					array('easyTooltip',													'admin_js'),
					array('jquery.tablesorter.min',											'admin_js'),
					array('visualize.jQuery',												'admin_js'),
					array('iphone-style-checkboxes',										'admin_js'),
					array('jquery.cleditor.min',											'admin_js'),
					array('admin_custom_events',											'admin_js'),
					array('google_analytics',												'admin_js'),
					
					
					
			//		array('pusher/global_promoter_pusher_notifications',					'admin_js'),
			//		array('pusher/global_manager_pusher_notifications',						'admin_js'),
			//		array('pusher/global_host_pusher_notifications',						'admin_js'),
					
					
					
					array('jquery.maskedinput-1.3.min',										'admin_js'),
					array('admin_team_chat', 												'admin_js'),
					array('suite_re_init', 													'admin_js')
					
					
				);
				
				
				//switch($this->input->get('subg')){
				switch($subg){
					case 'promoters':
					
					//	$group .= '-promoters';
						
						$group_assets[] = array('promoters/promoters_ajaxify_front', 						'admin_js');
						
						$group_assets[] = array('promoters/page/admin_promoter_setup_dashboard', 			'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_dashboard', 					'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_guest_list', 				'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_tables', 					'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_clients', 					'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_my_profile', 				'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_my_profile_img', 			'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_manage_guest_lists', 		'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_manage_guest_lists_new', 	'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_manage_guest_lists_edit',	'admin_js');
						$group_assets[] = array('promoters/page/admin_promoter_manage_image',				'admin_js');
						
						$group_assets[] = array('promoters/promoter_router', 								'admin_js');
					
						break;
					case 'managers':
					
					//	$group .= '-managers';
					
						$group_assets[] = array('managers/managers_ajaxify_front', 						'admin_js');
					
						$group_assets[] = array('managers/page/admin_manager_dashboard',				'admin_js');
						$group_assets[] = array('managers/page/admin_manager_guest_lists',				'admin_js');
						$group_assets[] = array('managers/page/admin_manager_tables',					'admin_js');
						$group_assets[] = array('managers/page/admin_manager_clients',					'admin_js');
						$group_assets[] = array('managers/page/admin_manager_promoters_guest_lists',	'admin_js');
						$group_assets[] = array('managers/page/admin_manager_promoters_statistics',		'admin_js');
						$group_assets[] = array('managers/page/admin_manager_promoters_clients',		'admin_js');
						$group_assets[] = array('managers/page/admin_manager_reports_guest_lists',		'admin_js');
						$group_assets[] = array('managers/page/admin_manager_settings_promoters',		'admin_js');
						$group_assets[] = array('managers/page/admin_manager_settings_hosts',			'admin_js');					
						$group_assets[] = array('managers/page/admin_manager_settings_venues',			'admin_js');					
						$group_assets[] = array('managers/page/admin_manager_settings_venues_edit',					'admin_js');					
						$group_assets[] = array('managers/page/admin_manager_settings_venues_new',					'admin_js');					
						$group_assets[] = array('managers/page/admin_manager_settings_venues_edit_floorplan',		'admin_js');		
						
						$group_assets[] = array('managers/page/admin_manager_marketing_new',						'admin_js');		
						$group_assets[] = array('managers/page/admin_manager_marketing',							'admin_js');		
								
						$group_assets[] = array('managers/page/admin_manager_support',								'admin_js');				
						$group_assets[] = array('managers/page/admin_manager_manage_image',							'admin_js');				
							
					
						$group_assets[] = array('managers/managers_router', 										'admin_js');
					
						break;
				}
				
				break;	
			case 'ejs_templates':
				
				/**
				 * Loads a separate JS file containing a single object of EJS templates for each
				 * supported language.
				 * 
				 */
				
				$lang = $subg; //$this->input->get('lang');
				
				if(!in_array($lang, array_keys($this->config->item('supported_lang_codes'))))
					$lang = 'en'; //default
				
				
				$languages = $this->config->item('supported_langs');
				$language = $languages[$lang];
			//	$group .= '-' . $lang;
				
				//load all language files needed by the EJS templates
				
				//load language files
				$this->lang->load('menu', 					$language);
				$this->lang->load('menu_user', 				$language);
				$this->lang->load('invitations_dialog', 	$language);
				$this->lang->load('invite', 				$language);
				$this->lang->load('footer', 				$language);
				$this->lang->load('app_data', 				$language);
				$this->lang->load('home_auth', 				$language);
				$this->lang->load('friends', 				$language);
				$this->lang->load('venues', 				$language);
				$this->lang->load('promoters', 				$language);
								
				$group_assets = array(
					array(
						array(
							'global/view_dynamic_assets_js_ejs_templates',
							array(
								'ejs_templates' => array(
									
									// ---------------- FRONT --------------------
									
									//global
									'user_menu',
									'notifications_request_response',
									
									//primary
									'primary_join_promoter_gl',
									'primary_join_team_gl',
									'primary_join_vibecompass',
									'primary_pop_guestlist',
									'primary_pop_guestlist_empty',
									
									//friends
									'friends_nonvc_friend', 
									'friends_vc_friend',
									'friends_recent_activity_join_pgl',
									'friends_recent_activity_join_tgl',
									'friends_recent_activity_join_vc',
									'friends_vibecompass_friends',
									'friends_promoters',
									
									//venues
									'venues_friends_visited',
									'venues_profile_friends_li_message',
									'venues_profile_friends_li',
									'venues_profile_news_feed_item',
									
									//promoters
									'promoters_profile_friends_venues',
									'promoters_profile_news_feed_item'
																		
								),
								'lang' => $language
							)
						), 
						'dynamic'
					)
				);
				
							
				break;
			default:
				die('unknown g');
				break;
		}
		
		$output_file_name = 'all_' . $group . '_' . $subg . '_';
		
		$compress = (ENVIRONMENT == 'production') ? true : false;
		if(php_sapi_name() == 'cli')
			$compress = true;
			
			
		# ----------------------- /ASSET GROUPS --------------------- #
		
		
		$output = "";
		foreach($group_assets as $asset){
			
			
			switch($asset[1]){
				
				case 'front_js':
					
					$js = $this->front_assets . 'js/' . $asset[0] . '.js';	
									
					$output .= "\n" . "// cscs asset: " . $asset[0] . "\n\n";
					$output .= $this->minify->js->min($js, $compress);
					
					break;
				case 'global_js':
					
					$js = $this->global_assets . 'js/' . $asset[0] . '.js';	
									
					$output .= "\n" . "// global asset: " . $asset[0] . "\n\n";
					$output .= $this->minify->js->min($js, $compress);
					
					break;
				case 'admin_js':
					
					$js = $this->admin_assets . 'js/' . $asset[0] . '.js';	
									
					$output .= "\n" . "// admin asset: " . $asset[0] . "\n\n";
					$output .= $this->minify->js->min($js, $compress);
					
					break;
				case 'dynamic':
					
									
					$output .= "\n" . "// dynamic asset: " . $asset[0][0] . "\n\n";
					$output .= $this->minify->js->min_ci_view($this->dynamic_assets . 'js/' . $asset[0][0], $asset[0][1], $compress);
					
					break;
								
			}
			
		}
		
		
			
		
		if(MODE == 'local' && php_sapi_name() != 'cli'){
			$this->output->set_output($output);
		}else{
			$filename = FCPATH . 'vcweb2/assets/' . $output_file_name . $this->config->item('cache_global_js') . '.js';
			if(!file_exists($filename)){
				$fh = fopen($filename, 'w+');
				fwrite($fh, $output);
				fclose($fh);
			}
		}
		
		
	}
		
}

/* End of file assets.php */
/* Location: ./application/controllers/assets.php */