<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Queries Facebook graph API, retrieves data related to app requests that
 * are sent when a user invites a friend to join a guest list
 * 
 */
class Net_Gearman_Job_retrieve_facebook_app_requests extends Net_Gearman_Job_Common{
	
    public function run($args){
    	
    	//get all the stuff we're going to need...
    	$request_ids = $args['request_ids'];
		$request_ids = explode(',', $request_ids);		
		
		//now the first index is the latest request
		$request_ids = array_reverse($request_ids);
		
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		//call graph API and get user basic info
		$CI->load->library('library_facebook', '', 'facebook');
		
		
		
		
		//multiCURL can make this go A LOT faster...
		
		
		
		
		
		
		//retrieve the last 10 requests
		for($i = 0; ($i < 5 && $i < count($request_ids)); $i++){
			$result[] = $CI->facebook->fb_api_query($request_ids[$i]);
		}
		
//		foreach($result as $res){
//			var_dump($res);
//		}
		
		
		//format requests for each request type
		foreach($result as &$res){
			
			if(isset($res['data'])){
				
				$data = json_decode($res['data']);
								
				if(!isset($data->type)){
					
					//no type property of data object, error
					$default_obj = new stdClass;
					$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
					$res['data'] = $default_obj;
					continue;
					
				}else{
					
					switch($data->type){
						case 0:
							//User adds friend to GL entourage
							
							if(!isset($data->gl_type)){
								//improper format...
								$default_obj = new stdClass;
								$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
								$res['data'] = $default_obj;
								continue;
							}
							
							if($data->gl_type == 0){
								//promoter
								
								if(!isset($data->pgla_id) || !isset($data->promoter_id)){
									//improper format...
									$default_obj = new stdClass;
									$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
									$res['data'] = $default_obj;
									continue;
								}
								
								$CI->load->model('model_guest_lists', 'guest_lists', true);
								$data->retrieve_pgla = $CI->guest_lists->retrieve_pgla($data->promoter_id, $data->pgla_id);
								
								if(!$data->retrieve_pgla){
									//unknown pgla
									$default_obj = new stdClass;
									$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
									$res['data'] = $default_obj;
									continue;
								}
								
								$res['data'] = $data;
						
							}elseif($data->gl_type == 1){
								//team
								
								if(!isset($data->tgla_id) || !isset($data->tv_id)){
									//improper format...
									$default_obj = new stdClass;
									$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
									$res['data'] = $default_obj;
									continue;
								}
								
								$CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
								$data->retrieve_tgla = $CI->team_guest_lists->retrieve_tgla($data->tv_id, $data->tgla_id);
								
								if(!$data->retrieve_tgla){
									//unknown tgla
									$default_obj = new stdClass;
									$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
									$res['data'] = $default_obj;
									continue;
								}
								
								$res['data'] = $data;
								
							}else{
								//improper format...
								$default_obj = new stdClass;
								$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
								$res['data'] = $default_obj;
								continue;
							}
							
							break;
						case 1:
							//User invites another user to check out VibeCompass
							break;
						case 2:
							//Promoter adds a friend to their guest list
							break;
						case 3:
							//Manager adds a friend to house guest list
							break;
						case 4:
							//Manager adds a friend as a promoter
							break;
						case 5:
							//Manager adds a friend as a host
							break;
						default:
							//unknown data type, set type manually
							$default_obj = new stdClass;
							$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
							$res['data'] = $default_obj;						
							break;
					}
					
				}
				
			}else{
				
				//invitation has no 'data' key, set default
				
				$default_obj = new stdClass;
				$default_obj->type = 1;		//<-- invitation to check out vibecompass as default request
				$res['data'] = $default_obj;
				continue;
			}
			
		}
		
		
		
		
		
//		var_dump($result);
		
		
		
		$data = json_encode(array('success' => true,
									'message' => $result));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		//Possibly remove for production, kind of cool to look at tho.
		$response = "Retrieved request IDS: ";
		for($i = 0; ($i < 5 && $i < count($request_ids)); $i++){
			$response .= $request_ids[$i] . ", ";
		}
		
		
		
		
		$response = rtrim($response, ', ');
		echo $response . PHP_EOL;
    }

}