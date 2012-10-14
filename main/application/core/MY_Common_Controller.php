<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Common_Controller extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		//TODO: switch over to 1 object?
		$central = new stdClass;
		
		
		
		
		
		
		
		//preventing pingdom from creating thousands of sessions...
		$non_session_granting_user_agents = array(
			'Pingdom.com_bot_version_1.4_(http://www.pingdom.com/)',	//pingdom bot
			'NewRelicPinger'
		);
		
		if(isset($_SERVER['HTTP_USER_AGENT']))
			foreach($non_session_granting_user_agents as $ua){
				if(strpos($_SERVER['HTTP_USER_AGENT'], $ua) === 0)
					die('success');
			}
			
		
		
		
		
		
		
		$is_not_static_request = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] != 'www.' . ASSETS_SITE . '.' . TLD));
		
		$is_not_static_request = true;
		
		if($is_not_static_request)
			$this->load->library('session');
		
		if($is_not_static_request)
			$vc_user = $this->session->userdata('vc_user');
		else 
			$vc_user = false;
		
			
		$central->vc_user = ($vc_user) ? json_decode($vc_user) : false;
		
		//is this http or https request?
		if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
			$central->scheme = 'https';
		else 
			$central->scheme = 'http';
		
		
		
		if($vc_user){
			$vc_user = json_decode($vc_user);
			
			if(isset($vc_user->last_activity)){
				
				if($vc_user->last_activity < (time() - (60 * 5))){
					//notify friends online...
					
					$this->load->helper('run_gearman_job');
					$arguments = array('access_token' => $vc_user->access_token,
											'user_oauth_uid' => $vc_user->oauth_uid);
					run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);
					
				}
				
			}else{
				
				//user just logged in, notify friends
				$this->load->helper('run_gearman_job');
				$arguments = array('access_token' => $vc_user->access_token,
										'user_oauth_uid' => $vc_user->oauth_uid);
				run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);
				
			}
			
			$vc_user->last_activity = time();
			$this->session->set_userdata('vc_user', json_encode($vc_user));
			
			//retrieve this users notifications
			$this->load->model('model_users', 'users', true);
			$user_sticky_notifications = $this->users->retrieve_user_sticky_notifications($vc_user->oauth_uid);
			$this->load->vars('user_sticky_notifications', $user_sticky_notifications);
			
		}
		unset($vc_user);
		
		if(!isset($user_sticky_notifications)){
			
			$user_sticky_notifications = array();
			$this->load->vars('user_sticky_notifications', $user_sticky_notifications);
			
		}
		unset($user_sticky_notifications);
		
		
		
		
		// this is how we set global variables available to all views with codeigniter
		$central->karma_link_base = base_url();
		$central->front_link_base = base_url();

		$central->promoter_admin_link_base = base_url() . 'admin/promoters/';
		$central->manager_admin_link_base = base_url() . 'admin/managers/';
		$central->super_admin_link_base = base_url() . 'admin/super_admins/';
		$central->facebook_link_base  = base_url() . 'facebook/';
		
						
		//$static_assets_domain = "$central->scheme://www." . ASSETS_SITE . "." . TLD;
		$static_assets_domain = "https://www." . ASSETS_SITE . "." . TLD;
		$central->static_assets_domain = $static_assets_domain . '/';
		
	//	$central->front_assets = $this->config->item('front_assets');
		$central->front_assets_nocdn = "$static_assets_domain/vcweb2/assets/web/";
		$central->front_assets = $central->front_assets_nocdn;
		
	//	$central->admin_assets = $this->config->item('admin_assets');
		$central->admin_assets_nocdn = "$static_assets_domain/vcweb2/assets/admin/";
		$central->admin_assets = $central->admin_assets_nocdn;
		
	//	$central->global_assets = $this->config->item('global_assets');
		$central->global_assets_nocdn = "$static_assets_domain/vcweb2/assets/global/";
		$central->global_assets = $central->global_assets_nocdn;
		
	//	$central->facebook_assets = $this->config->item('facebook_assets');
		$central->facebook_assets_nocdn = "$static_assets_domain/vcweb2/assets/facebook/";
		$central->facebook_assets = $central->facebook_assets_nocdn;

		//http://d1pv30wi5cq71r.cloudfront.net/vc-images/
		$central->s3_uploaded_images_base_url = $this->config->item('s3_uploaded_images_base_url');
		
		# ----------- caching ------------- #
		$central->cache_admin_css = $this->config->item('cache_admin_css');
		$central->cache_admin_js = $this->config->item('cache_admin_js');
		$central->cache_admin_images = $this->config->item('cache_admin_images');
		
		$central->cache_karma_css = $this->config->item('cache_karma_css');
		$central->cache_karma_js = $this->config->item('cache_karma_js');
		$central->cache_karma_images = $this->config->item('cache_karma_images');
		
		$central->cache_front_css = $this->config->item('cache_front_css');
		$central->cache_front_js = $this->config->item('cache_front_js');
		$central->cache_front_images = $this->config->item('cache_front_images');
		
		$central->cache_global_css = $this->config->item('cache_global_css');
		$central->cache_global_js = $this->config->item('cache_global_js');
		$central->cache_global_images = $this->config->item('cache_global_images');
		
		$central->cache_facebook_css = $this->config->item('cache_facebook_css');
		$central->cache_facebook_js = $this->config->item('cache_facebook_js');
		$central->cache_facebook_images = $this->config->item('cache_facebook_images');
		# ----------- end caching ----------- #
		
		$central->title = $this->config->item('title_base');
		$central->facebook_app_id = $this->config->item('facebook_app_id');
		$central->facebook_default_scope = $this->config->item('facebook_default_scope');
		

		
		
		
		/*
		
		//load language files
		$language = $this->config->item('current_lang');
		$this->lang->load('menu', 					$language);
		$this->lang->load('menu_user', 				$language);
		$this->lang->load('invitations_dialog', 	$language);
		$this->lang->load('invite', 				$language);
		$this->lang->load('footer', 				$language);
		$this->lang->load('app_data', 				$language);
		
		
		*/
		
		if(isset($_SERVER['REQUEST_URI']))
			$central->request_uri = $_SERVER['REQUEST_URI'];
		else
			$central->request_uri = '/';
		
		
		
		
		
		
		$this->load->vars('central', $central);
				
		if(!$this->input->is_ajax_request()){
			
			if(ENVIRONMENT == 'development'){
			//	$this->output->enable_profiler();
			}
		
		}
		
	}
	
}

/* End of file MY_Common_Controller.php */
/* Location: /application/core/MY_Common_Controller.php */