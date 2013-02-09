<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sends a request to the facbeook API to post a message on a user's
 *  facebook wall when they've been accepted on a guest list
 * 
 */
class Net_Gearman_Job_gearman_update_crontab extends Net_Gearman_Job_Common{

	public function run($args){

		$index_php = ((MODE == 'local') ? '/Users/casey/Documents/workspaces/clubbingowl/main/index.php' : '/home/dotcloud/current/index.php');

		$filename  = ((MODE == 'local') ? '/Users/casey/Documents/workspaces/clubbingowl/' : '/home/dotcloud/current/') . 'cronjobs.txt';

		//get all the stuff we're going to need...
		$CI =& get_instance();
		echo 'Updating crontab...';

		$sql = "SELECT *
				FROM cron_jobs cj
				WHERE cj.cj_deactivated = 0 AND (cj.cj_once = 0 OR (cj.cj_once = '1' AND cj.cj_once_ran != 1))";
		$query  = $CI->db->query($sql);
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