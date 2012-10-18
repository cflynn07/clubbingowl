<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'core/MY_Common_Controller.php');

class MY_Controller extends MY_Common_Controller{
	
	public function __construct(){
		parent::__construct();
		
		//intense anti-caching headers to ensure that browsers load page freshly every time to start gearman jobs
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("M355AGE: Well now, aren't you clever :)");
		//allows cookies to be set in iframe w/ IE
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		
		
		if($this->input->is_ajax_request())
			header('X-Accel-Buffering: no');
				
				
				
		//load language files
		$language = $this->config->item('current_lang');
		$this->lang->load('menu', 					$language);
		$this->lang->load('menu_user', 				$language);
		$this->lang->load('invitations_dialog', 	$language);
		$this->lang->load('invite', 				$language);
		$this->lang->load('footer', 				$language);
		$this->lang->load('app_data', 				$language);
		
		
		
	//	Kint::dump(json_decode($this->input->cookie('vc_user')));
	//	Kint::dump($this->input->cookie('fbsr_236258849725316'));
	//	Kint::dump(facebook_signed_request_decode($this->input->cookie('fbsr_236258849725316')));
		
		
		
	

			
		/*
		
		$this->load->model('model_app_data', 'app_data', true);
		$app_live = $this->app_data->retrieve_app_live();
		
		if(false && $app_live->as_app_live != '1'){
			$splash = $this->load->view('karma/splash/view_splash', '', true);
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			die($splash);
		}
		*/
		
		
		//NOTE: Session initialized here to prevent glitch with new sessions being created for 404 pages
	//	$this->load->library('session');
		
		//--------------------------------- CHECK USER SESSION ---------------------------------------
		if($vc_user = $this->session->userdata('vc_user')){
			
			$vc_user = json_decode($vc_user);
			
			if(!isset($vc_user->super_admin)){
			
				$this->load->helper('facebook_signed_request_decode');
				$facebook_app_id = $this->config->item('facebook_app_id');
				if($fbsr_cookie = $this->input->cookie('fbsr_' . $facebook_app_id)){
					$fbsr_cookie = facebook_signed_request_decode($fbsr_cookie);
					
					if(isset($fbsr_cookie['user_id'])){
						if($fbsr_cookie['user_id'] != $vc_user->oauth_uid){
							//someone different is logged in on the front end
							
							$this->session->unset_userdata('vc_user');
							
						}
					}else{
						//???
					}
					
				}else{
					//user has logged out on client-side, session invalid
					
					//For now, do nothing
				}
			
			}
			
			unset($vc_user);
			
		}
		//--------------------------------- END CHECK USER SESSION ---------------------------------------
		
		
		
		
		//--------------------------------- CHECK USER INVITATIONS ---------------------------------------

		$user_invitations = array();
		//check to see if user has any invitations
		$this->load->model('model_users', 'users', true);
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user){
			
			$vc_user = json_decode($vc_user);
			if($invitations = $this->users->retrieve_user_invitations($vc_user->oauth_uid)){
		
				$user_invitations = $invitations;
									
			}	
			
		}
		$this->load->vars('invitations', $user_invitations);
		//--------------------------------- END CHECK USER INVITATIONS ---------------------------------------

		
		
		/*
		
		//--------------------------------- TEMPORARY SPLASH PAGE REDIRECT -------------------------------------
		if(DEPLOYMENT_ENV == 'cloudcontrol'){		//<------- Mon May 14 2012 08:59:57 GMT-4
				
			//https://www.facebook.com/dialog/pagetab?app_id=%20236915563048749&display=popup&next=http://www.facebook.com
			
			$approved_uids = array(
			
				'504405294',				//Me
				'1634269784',				//Federico
				'691334780',				//Robert
				'100002917463525',			//Elon
				'100002624783324',			//casey testaccount
				'100003261476292',			//vcpromoter vibe
				'100003740963994',			//bossman1
				'500418954',				//anabella
				
				
				//Managers
				'626553085',				//Cameron Grobb 	- ECNL
				'764688187',				//Heather Light 	- Space
				'501786477', 				//Paige Khanna		- Space
				'501519084',				//Mete Aslan 		- Bijou/MKE
				'841305430',				//George Aboujaoude	- Bijou/MKE
				'1324316721',				//Andrew Haddad		- ECNL
				'100001532290273',			// Mete / Bijou Boston
				
				
				
				'1609860156',				//Brian Quinn -- ECNL
				'1088670590',				//Felipe Braga -- ECNL
				'1324316721',				//Andrew Haddad - ECNL
				'700006402',				//Rodrigo Braga - ECNL
				'579767535'					//Gommert Mes -- ECNL
				
				
				//Promoters
				
				
				
			);
			
			//share with splash page
			$this->load->vars('approved_uids', $approved_uids);
			
			$allowed_user_agents = array(
				'Pingdom.com_bot_version_1.4_(http://www.pingdom.com/)'
			//	'Googlebot',
			//	'Yahoo! Slurp',
			//	'msnbot'
			);
			
			$vc_user = $this->session->userdata('vc_user');
			if(!$vc_user){
				
				//is this request allowed b/c of header?
				if(!(isset($_SERVER['HTTP_USER_AGENT']) && in_array($_SERVER['HTTP_USER_AGENT'], $allowed_user_agents))){
					
					//only allow this request through if it's at the root || ajax/auth , and only load splash view.
					if($_SERVER['REQUEST_URI'] != '/' && $_SERVER['REQUEST_URI'] != '/ajax/auth/'){
						
						//redirect to root of domain
						$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
					    $base_url .= '://'. $_SERVER['HTTP_HOST'];
					    $base_url .= isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80' ? ( ':'.$_SERVER['SERVER_PORT'] ) : '';
					 	$base_url .= '/';
						
						header('HTTP/1.1 301 Moved Permanently');
						header('Location: ' . $base_url);
						die();
					
					}else{
						
						//if this isn't a login request to ajax/auth, (only the root domain) display the splash view and die
						if($_SERVER['REQUEST_URI'] != '/ajax/auth/'){
							
							$this->db->select('oauth_uid');
							$query = $this->db->get('users', 104);
							$users = $query->result();
							
							$users_oauth_uids = array();
							foreach($users as $user){
								$users_oauth_uids[] = $user->oauth_uid;
							}
							shuffle($users_oauth_uids);
							
							$data['users'] = $users_oauth_uids;
							error_reporting(0);
							$splash = $this->load->view('karma/splash/view_splash', $data, true);
						//	header("HTTP/1.0 404 Not Found");
							die($splash);
							
						}
					}
					
				}
				
			}else{
				
				$vc_user = json_decode($vc_user);
				
				if(in_array($vc_user->oauth_uid, $approved_uids)){
					//This user is cool, let them in
					
				}else{
					//user is not cool, force to splash page
										
					//only allow this request through if it's at the root || ajax/auth , and only load splash view.
					if($_SERVER['REQUEST_URI'] != '/' && $_SERVER['REQUEST_URI'] != '/ajax/auth/'){
						
						//redirect to root of domain
						$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
					    $base_url .= '://'. $_SERVER['HTTP_HOST'];
					    $base_url .= isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80' ? ( ':'.$_SERVER['SERVER_PORT'] ) : '';
					 	$base_url .= '/';
						
						header('HTTP/1.1 301 Moved Permanently');
						header('Location: ' . $base_url);
						die();
					
					}else{
						
						//if this isn't a login request to ajax/auth, (only the root domain) display the splash view and die
						if($_SERVER['REQUEST_URI'] != '/ajax/auth/'){
							
							$this->db->select('oauth_uid');
							$query = $this->db->get('users', 104);
							$users = $query->result();
							
							$users_oauth_uids = array();
							foreach($users as $user){
								$users_oauth_uids[] = $user->oauth_uid;
							}
							shuffle($users_oauth_uids);
							
							$data['users'] = $users_oauth_uids;
							error_reporting(0);
							$splash = $this->load->view('karma/splash/view_splash', $data, true);
						//	header("HTTP/1.0 404 Not Found");
							die($splash);
							
						}
					}	
				
				}
				
			}

		}
		//--------------------------------- END TEMPORARY SPLASH PAGE REDIRECT -------------------------------------
		*/

	}
	
}

/* End of file MY_Controller.php */
/* Location: /application/core/MY_Controller.php */