<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Colors {
		private $foreground_colors = array();
		private $background_colors = array();
 
		public function __construct() {
			// Set up shell colors
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';
 
		$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
		}
 
		// Returns colored string
	public function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";
 
			// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
			}
 
			// Add string and end coloring
		$colored_string .=  $string . "\033[0m";
 
			return $colored_string;
		}
 
		// Returns all foreground color names
		public function getForegroundColors() {
			return array_keys($this->foreground_colors);
		}
 
		// Returns all background color names
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}
}









class Worker extends CI_Controller {
	
    public function __construct() {
    	parent::__construct();
		//force only access from command line
        if(php_sapi_name() !== 'cli') {
            show_404();
        }
		
		error_reporting(E_ALL);
	//	error_reporting(E_ALL);
		ini_set('display_errors', '1');
		
    }
	
	// Test some basic printing with Colors class
	//	echo $colors->getColoredString("Testing Colors class, this is purple string on yellow background.", 	"purple", 	"yellow") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is blue string on light gray background.", 	"blue", 	"light_gray") 	. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is red string on black background.", 		"red", 		"black") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on green background.", 		"cyan", 	"green") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on default background.", 		"cyan") 					. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is default string on cyan background.", 		null, 		"cyan") 		. "\n";
	
	
	
	
	
	
	
	
	
	public function mandrill_test(){
		
		$this->load->library('library_bulk_email', '', 'library_bulk_email');
		
		$test_users = array(
			'Casey Flynn' => 'casey_flynn@cobarsystems.com',
			'Casey Flynn1' => 'casey_flynn@clubbingowl.com',
			'Casey Flynn2' => 'casey_flynn@cobarsystems.com',
			'Casey Flynn3' => 'casey_flynn@clubbingowl.com'
		);
		
				
		foreach($test_users as $key => $email){
			
			$this->library_bulk_email->add_queue(array(
				'html'		=> '<p>This is a test</p>',
				'text'		=> 'This is a test',
				'subject'	=> 'Email test from ClubbingOwl',
				'to_email'	=> $email, 
				'to_name'	=> $key,
			));
						
		}
		
		$this->library_bulk_email->flush_queue();
		
		echo 'complete' . PHP_EOL;			
		
	}
	
	
	
	
	
	
	
	
	
	public function run_billing(){
		
		
		$previous_month			= date('n', strtotime('-1 month'));
		$previous_month_year	= date('Y', strtotime('-1 month'));
		
		
	
		$this->db->select('*')
			->from('teams');
		$query = $this->db->get();
		$all_teams = $query->result();
		
	
		// Create new Colors class
		$colors = new Colors();
	 	$handle = fopen ("php://stdin","r");
	 	
		
	 	echo $colors->getColoredString("Hello, welcome to the ClubbingOwl billing experience. My name is Karl, I will help you bill your clients today.", 										'green', null) . PHP_EOL;
	 	echo $colors->getColoredString("We will start by billing all of the clients for the previous month of " . date('F', strtotime('-1 month')) . ' that have not already been invoiced, one at a time.', 	'purple', null) . PHP_EOL . PHP_EOL;
	 	
		
		echo $colors->getColoredString("You have " . count($all_teams) . " clients.", 	'brown', null) . PHP_EOL;
		echo $colors->getColoredString("First up:", 									'brown', null) . PHP_EOL;

		foreach($all_teams as $team){
			
			
			//see if this team was invoiced already for the previous month
			$this->db->select('*')
				->from('teams_invoices')
				->where(array(
					'invoice_month'		=> $previous_month,
					'invoice_year'		=> $previous_month_year,
					'team_fan_page_id'	=> $team->fan_page_id
				));
			$query = $this->db->get();
			$result = $query->row();
			
			
			echo $colors->getColoredString($team->name, 														'white', null) 	. PHP_EOL;
			echo $colors->getColoredString("\t" . "DATE BEGIN BILLING:" . "\t" . $team->date_begin_billing, 	'lightred', null) . PHP_EOL;
			
			
			
			if($result){
				echo $colors->getColoredString("\t" . "This team has already been invoiced for " . date('F', strtotime('-1 month')) . " ... skipping", 	'light_cyan', null) . PHP_EOL;
			}else{						
				$line 	= strtolower(fgets($handle));
				
				
				
				
				
				$month_days = cal_days_in_month(CAL_GREGORIAN, $previous_month, $previous_month_year);
				
				
				
				
			}
			
			
			
		}
	
		echo $colors->getColoredString("And that completes our billing cycle. Have a nice day.", 'green', null) . PHP_EOL;
				
	}
	
	
	/**
	 * Email a user's vibecompass friends when they join VibeCompass or join a guest-list
	 * 
	 */
	public function worker_email_vc_user_friends($oauth_uid = "504405294"){
		
		
		$this->db->where(array('oauth_uid' => $oauth_uid));
		$query = $this->db->get('users');
		$result = $query->row();
		
		
		
		
		/*	
		$this->load->helper('run_gearman_job');
		$arguments = array(
			'user_oauth_uid'	=> $result->oauth_uid,
			'access_token'		=> $result->access_token
		);
		
		run_gearman_job('gearman_email_friends_new_user', $arguments, false);
		
		*/
		
		
		
		
		
		//user just logged in, notify friends
		$this->load->helper('run_gearman_job');
		$arguments = array('access_token' => $result->access_token,
								'user_oauth_uid' => $result->oauth_uid);
		run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);
		
		return;
				
	}
	
	
	
	
	public function test(){
	
		echo 'brazin test' . PHP_EOL;
	
		
		
		return;
	
		$this->load->library('Cloudcontrol', '', 'cloudcontrol');
	    $email = 'cflynn@ccs.neu.edu';
	    $password = 'w3smsnicq34';
	    $this->cloudcontrol->auth($email, $password);
	    $this->cloudcontrol->worker_create('vibecompass', 'default', 'index.php', 'worker all');
		
		echo 'done!' . PHP_EOL;		
				
	}
	
	
	public function cron_all(){
		
		$filename = ((MODE == 'local') ? '~/Documents/workspace/clubbingowl/' : '/home/dotcloud/current/') . 'cronjobs.txt';
		
		//set up the crontab
		$fh = fopen($filename, 'w+');
		
		$txt = '';
		
		fwrite($fh, $txt);
		fclose($fh);
		
		exec('crontab ' . $filename);
		$this->all('crontab');
		
	}
	
	
	/**
	 * Worker that performs all tasks, mostly used for development/simplicity
	 * 
	 */
	public function all($arg1 = ''){
		
		try{
 			
			echo 'all worker starting' . PHP_EOL;
		
			$this->load->library('pearloader');
			$gearman_worker = $this->pearloader->load('Net', 'Gearman', 'Worker');	
						
			$gearman_worker->addAbility('facebook_user_authenticate');
						
			$gearman_worker->addAbility('guest_list_text_message');
			$gearman_worker->addAbility('guest_list_share_facebook');
			$gearman_worker->addAbility('retrieve_facebook_app_requests');
			
		//	if(MODE == 'local'){
				$gearman_worker->addAbility('admin_promoter_piwik_stats');
				$gearman_worker->addAbility('admin_manager_piwik_stats');
		//	}
			
			$gearman_worker->addAbility('news_feed_retrieve');
			$gearman_worker->addAbility('friend_feed_retrieve');
			$gearman_worker->addAbility('friend_retrieve');
			$gearman_worker->addAbility('gearman_individual_promoter_friend_activity');		
			
				
			$gearman_worker->addAbility('gearman_admin_manager_promoter_piwik_stats');
			
						
			$gearman_worker->addAbility('gearman_promoter_manual_add');			
			$gearman_worker->addAbility('gearman_manager_manual_add');			
			$gearman_worker->addAbility('gearman_vc_user_notify_friends_online');		
			$gearman_worker->addAbility('gearman_email_friends_new_user');				
			$gearman_worker->addAbility('gearman_retrieve_friend_venues_activity');	
			$gearman_worker->addAbility('gearman_individual_venue_friend_activity');	
			$gearman_worker->addAbility('gearman_send_emails');	
			$gearman_worker->addAbility('gearman_send_sms_notification');	
			
			
			//finds friends that are associated with a group of promoters
			$gearman_worker->addAbility('gearman_promoter_friend_activity');
			
			
			//promoter & team guest list status	
			$gearman_worker->addAbility('gearman_new_promoter_gl_status');	
			$gearman_worker->addAbility('gearman_new_manager_gl_status');	
			
			
			$gearman_worker->addAbility('gearman_confirmation_email_team');	
			$gearman_worker->addAbility('gearman_confirmation_email_promoter');	
			
			
			//finds reviews of a user's friends for a given promoter
			$gearman_worker->addAbility('gearman_individual_promoter_friend_reviews');	
					
					
			
		//	if($arg1 == 'special_emails'){
				$gearman_worker->addAbility('gearman_email_friends_new_user');	
				$gearman_worker->addAbility('gearman_send_sms_mass_text_team_announcements');	
		//		$gearman_worker->addAbility('gearman_email_friends_gl_join');
		//	}
			
			if($arg1 == 'crontab'){
				$gearman_worker->addAbility('gearman_update_crontab');
			}
			
			
		    $gearman_worker->beginWork();
			
		}catch(Net_Gearman_Exception $e){
		   	
		//	var_dump($e); //die();
			echo 'Exception...';
			
		}
	}










	public function auto_promote($cj_id){
		
		return;
	//	$this->db->select('*')
	//		->from('')
		
		
		$team_guest_list	= $args['team_guest_list'];
		$user_oauth_uid 	= $args['user_oauth_uid'];
		$venue_name 		= $args['venue_name'];
		$date 				= $args['date'];
		$guest_list_name 	= $args['guest_list_name'];
		
		
		$guest_list_name 	= strtoupper($guest_list_name);
		
		
		$image 				= (isset($args['image'])) ? $args['image'] : false;
		
		$c_url_identifier 	= (isset($args['c_url_identifier'])) ? $args['c_url_identifier'] : '';
		
		
		$facebook_application_id = $CI->config->item('facebook_app_id');
		
		$CI->load->model('model_users', 'users', true);
		$vc_user = $CI->users->retrieve_user($user_oauth_uid);

		$CI->load->library('library_facebook', '', 'facebook');
		
		$app_description = 'ClubbingOwl is the fastest way to plan your evening! Find out where your friends party and join them. With ClubbingOwl getting on a guest-list or reserving a table is only one click away.';
		
		
		
		
		if(MODE == 'local')
				$base_url = 'https://www.clubbingowl.com/';
			else
				$base_url = 'https://www.clubbingowl.com/';
		
		
		
		if($team_guest_list){
			//team guest list
			
			$team_venue_id		= $args['team_venue_id'];
			
			$params = array(
//				'message' 		=> "$vc_user->users_full_name is on the ClubbingOwl guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!",
//				'message' 		=> "I'm on the guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!\n\n Click here to join \"$guest_list_name.\"",
				'message'		=> "I just joined the house guest list $guest_list_name at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : 'this ' . date('l', strtotime($date))) . ". \n\n Click here to join $guest_list_name with ClubbingOwl.", 
		
//				'link' 			=> "www.facebook.com/pages/@/$team_venue_id?sk=app_$facebook_application_id",
				
				'link'			=> $base_url . 'venues/' . $c_url_identifier . '/' . str_replace(' ', '_', $venue_name) . '/guest_lists/' . str_replace(' ', '_', $guest_list_name) . '/',
				
				
				'picture' 		=> ($image) ? $CI->config->item('s3_uploaded_images_base_url') . 'guest_lists/' . $image . '_t.jpg' : $CI->config->item('global_assets') . 'images/ClubbingOwlBackgroundWeb_small2.png',
				'name' 			=> $guest_list_name,
				'caption' 		=> "Click here to join $guest_list_name with ClubbingOwl",
				'description' 	=> $app_description
			);
			
		}else{
			//promoter guest list
			
			$promoter_public_identifier	= $args['promoter_public_identifier'];
			$promoter_full_name 		= $args['promoter_full_name'];
			$user_third_party_id  		= $args['user_third_party_id'];
			
			$guest_list_url_name = str_replace(' ', '_', $guest_list_name);
			
			//we need the promoter_public_identifier and the pgla_name to form the hyperlink to post on facebook
			
			
				
			$params = array(
				
				'message'		=> "I just joined $promoter_full_name's guest list at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : 'this ' . date('l', strtotime($date))) . ". \n\n Click here to join $guest_list_name with ClubbingOwl.", 
				
		//		'message' 		=> "I'm on $promoter_full_name's guest list \"$guest_list_name\" at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!\n\n Click here to join \"$guest_list_name.\"",
				'link' 			=> $base_url . "promoters/$promoter_public_identifier/guest_lists/$guest_list_url_name/?ref=$user_third_party_id",
				'picture' 		=> ($image) ? $CI->config->item('s3_uploaded_images_base_url') . 'guest_lists/' . $image . '_t.jpg' : $CI->config->item('global_assets') . 'images/vibecompass_logo.png',
				'name' 			=> $guest_list_name,
				'caption' 		=> "Click here to join $guest_list_name with ClubbingOwl",
				'description' 	=> $app_description
			);
			
		}

	//	$result = $CI->facebook->fb_api_query('/me/feed/', $vc_user->users_access_token, 'POST', $params);
		//we don't need a user access token to post to a user's facebook wall if they've granted our application 'publish-stream' access
		$result = $CI->facebook->fb_api_query($user_oauth_uid . '/feed/', false, 'POST', $params);
		
		var_dump($result);
		
		echo "Posted to $vc_user->users_full_name's wall" . PHP_EOL;
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function rank_invite($cj_id){
		
	}
	
	
	
	public function all_cron_test(){
		$index_php = ((MODE == 'local') ? '/Users/casey/Documents/workspaces/clubbingowl/main/index.php' : '/home/dotcloud/current/index.php');

		$filename  = ((MODE == 'local') ? '/Users/casey/Documents/workspaces/clubbingowl/' : '/home/dotcloud/current/') . 'cronjobs.txt';

		//get all the stuff we're going to need...
		$CI =& get_instance();
		echo 'Updating crontab...';

		$sql = "SELECT *
				FROM cron_jobs cj
				WHERE cj.cj_deactivated = 0 AND (cj.cj_once = 0 OR (cj.cj_once = '1' AND cj.cj_once_ran != 1))";
		$query  = $this->db->query($sql);
		$result = $query->result();

		$cron_string = 'TZ=America/New_York' . PHP_EOL;
		foreach($result as $res){
			
			
			$dtz = new DateTimeZone("America/New_York");
			$sec = timezone_offset_get($dtz, new DateTime());
			if($sec !== false)
				$gmt_offset = $sec / 60 / 60;
			else 
				$gmt_offset = 0;
			
			
			
			//MIN 		HOUR		Day-Of-Month		MONTH 		Day-Of-Week
			$cron_string .= $res->cj_min . ' ' . (($res->cj_hour + $gmt_offset) % 23). ' ' . $res->cj_day_of_month . ' ' . $res->cj_month . ' ' . $res->cj_day_of_week . ' ';
			$cron_string .= 'php ' . $index_php . ' worker ' . $res->cj_type;
			
			if($res->cj_type == 'auto_promote'){
				
				$cron_string .= ' ' . $res->cj_id;
				
			}else if($res->cj_type == 'rank_invite'){
				
				
				
			}
			
			$cron_string .= PHP_EOL;
		
		}
		
		//set up the crontab
		$fh = fopen($filename, 'w+');
		fwrite($fh, $cron_string);
		fclose($fh);

		exec('crontab -r');
		exec('crontab ' . $filename);
		
		echo 'done' . PHP_EOL;
		
	}


}

/* End of file worker.php */
/* Location: ./application/controllers/worker.php */