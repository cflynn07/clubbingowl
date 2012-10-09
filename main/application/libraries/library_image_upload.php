<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * Provides properties and functions for the uploading and cropping of user submitted photos
 * */
class Library_image_upload{
	#########################################################################################################
	# CONSTANTS																								#
	# You can alter the options below																		#
	#########################################################################################################
	
	
	public $CI;						//reference to the CI superobject
	public $image_upload_error;
	
	//keeps track of image data such as name, width/height, crop coordinates
	public $image_data;
	public $TMP_CC_WORKING_DIR;
	
	private $cache_settings;
	
	/*
	 * constructor
	 * 
	 * @return	Image_upload (stdObject)
	 */
	function __construct(){
		
		$this->cache_settings = array(
	        "Cache-Control" => "max-age=315360000",
	        "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
	    );
		
		$this->image_data = array();
		$this->CI =& get_instance(); //very important to assign by reference and not by value to avoid
										//creating gigantic duplicate object of CI superobject
		if(!isset($_SERVER['TMP_DIR'])){
			//assume local dev env
			$this->TMP_CC_WORKING_DIR = '/Applications/XAMPP/xamppfiles/temp/';
			
		}else{
			$this->TMP_CC_WORKING_DIR = $_SERVER['TMP_DIR'] . '/'; //Implemented for CC compatability
		}
		
	}
	
	##########################################################################################################
	# IMAGE FUNCTIONS																						 #
	# do not need to alter these functions																	 #
	##########################################################################################################
	
	/**
	 * Takes a temp image out of the auto-expires directory and places is in the live images folder (original & all sizes)
	 * 
	 * @param 	string (type)
	 * @param 	string (name)
	 * @return 	string (new_image_name)
	 */
	function make_image_live($image_type, $original_name, $live_image = false){
		
		if($live_image){
			//If we're making an image live that will replace an old live image, delete the old live image
			$this->_delete_old_images($original_name, $image_type, $live_image);
		}
		
		//Original image
		//retrieve original image from s3
		$this->CI->load->library('s3');
		$response_obj = $this->CI->s3->getObject('vcweb2', 'vc-images/' . $image_type . '/originals/temp/' . $original_name . '.jpg');
		
		//Did the request have an error?
		if($response_obj->error){
			die(json_encode(array('success' => false,
									'message' => 'Unable to retrieve original image from s3')));
		}
		
		$new_image_name = md5($this->CI->session->userdata('session_id') . time());
					
		//Convert raw image into gd resource
		$image = imagecreatefromstring($response_obj->body);
		
		//place original back on s3 with new file name
		//create temporary file on filesystem
		imagejpeg($image, $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
		
		//place temporary file on s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/' . $image_type . '/originals/' . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete temporary file
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
		
		//--------------------------------------------------------------
		//'_p'
		
		$response_obj = $this->CI->s3->getObject('vcweb2', 'vc-images/' . $image_type . '/originals/temp/' . $original_name . '_p.jpg');
		
		//Did the request have an error?
		if($response_obj->error){
			die(json_encode(array('success' => false,
									'message' => 'Unable to retrieve _p image from s3')));
		}
						
		//Convert raw image into gd resource
		$image = imagecreatefromstring($response_obj->body);
		
		//place original back on s3 with new file name
		//create temporary file on filesystem
		imagejpeg($image, $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//place temporary file on s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/' . $image_type . '/' . $new_image_name . '_p.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete temporary file
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//--------------------------------------------------------------
		//'_t'
		
		$response_obj = $this->CI->s3->getObject('vcweb2', 'vc-images/' . $image_type . '/originals/temp/' . $original_name . '_t.jpg');
		
		//Did the request have an error?
		if($response_obj->error){
			die(json_encode(array('success' => false,
									'message' => 'Unable to retrieve _t image from s3')));
		}
						
		//Convert raw image into gd resource
		$image = imagecreatefromstring($response_obj->body);
		
		//place original back on s3 with new file name
		//create temporary file on filesystem
		imagejpeg($image, $this->TMP_CC_WORKING_DIR . $new_image_name . '_t.jpg');
		
		//place temporary file on s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '_t.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/' . $image_type . '/' . $new_image_name . '_t.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete temporary file
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '_t.jpg');
		
		return $new_image_name;
	}
	
	/**
	 * Routing function for diferent types of image uploads
	 * 
	 * @param 	array (config options)
	 * @return 	null
	 */
	function image_upload($options = array()){
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
			'type' 			=> 'promoter_profile_picture',
			'upload_type' 	=> false,		//<-- defined in view controller function (ex: _manage_guest_lists_new || _manage_guest_lists_edit)
			'image_data' 	=> false,
			'live_image' 	=> false
		);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_contest_entries: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		//verify image was uploaded
		if(!$this->CI->input->post('ocupload')){
			$this->image_upload_error = 'ocupload not set';
			return false;
		}
		
		//upload type == 'guest_lists' || 'events'
		
		switch($upload_type){
			case 'events':
			case 'guest_lists':
				return $this->_event_guest_list_upload($upload_type, 
														$image_data, 
														$live_image);
				break;
			case 'venues/banners':
				return $this->_venues_banners_upload($upload_type, 
														$image_data, 
														$live_image);
				break;
			case 'profile-pics':
				return $this->_promoter_image_upload();
				break;
			default: 
				$this->image_upload_error = 'Unknown error';
				return false;
				break;
		}
		
	}

	/**
	 * Handles uploading an image to S3 for an event or a guest list
	 * See _promoter_image_upload() for more documentation
	 * 
	 * @return 	null
	 */
	private function _event_guest_list_upload($upload_type, $image_data, $live_image){
				
		if(!$upload_type){
			$this->image_upload_error = 'Unknown error';
			return false;
		}
			
		/* ---------------------- verify size & type are allowed ----------------------*/
		$config['upload_path'] = $this->TMP_CC_WORKING_DIR;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size']	= '10000';
		$config['max_width'] = '8000';
		$config['max_height'] = '8000';
		$min_width = 188;
		$min_height = 266;

		$this->CI->load->library('upload', $config);

		//image did not meet restrictions, exit function and save error message to object property
		if (!$this->CI->upload->do_upload('file')){
			//save error to public property of this class for use after function exits
			$this->image_upload_error = strip_tags($this->CI->upload->display_errors());				
			return false;
		}
			
		//collect upload data
		$image_upload_data = $this->CI->upload->data();
		
		list($image_width, $image_height) = getimagesize($image_upload_data['full_path']);
		if($image_width < $min_width || $image_height < $min_height){
			unlink($image_upload_data['full_path']);
			$this->image_upload_error = 'Image does not meet minimum height/width requirements.';
			return false;
		}
		
		/* ---------------------- end verify size & type are allowed ----------------------*/
		
	//	if($image_data){
	//		$this->_delete_old_images($image_data->image, $upload_type, $live_image);
	//	}
		
		/* ---------------------- convert to jpg ----------------------*/
		$this->_convert_to_jpeg($image_upload_data['full_path']);
		/* ---------------------- end convert to jpg ----------------------*/	

				
		/* ---------------------- rename and save base version to S3 ----------------------*/
		
		/**
		 * s3 naming schema for user profile pictures:
		 * 	  -- original, uncropped version. Probably will never be shown to users
		 * 		all original image versions go in 'originals' folder
		 * 
		 * _p -- profile version. cropped to a 3:4 aspect ratio and resized to W x H
		 * _t -- thumbnail version. smaller version of _p
		 */
		
		$this->CI->load->library('s3');
							
		//creates a new image name for this promoter's uploaded profile picture
		$new_image_name = md5($this->CI->session->userdata('session_id') . time());
		
		//create 'image_data' array of values for use in web browser
		$this->image_data = array_merge($this->image_data, array('image' => $new_image_name, 
																	'upload_type' => $upload_type));															
		
		//save original version of profile picture to s3
		$sourceFile = $image_upload_data['full_path'];
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . (($live_image) ? '/originals/' : '/originals/temp/') . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);		
		/* ---------------------- end rename and save base version to S3 ----------------------*/
		
		/* ---------------------- initial crop to aspect ratio for display & thumbnail ----------------------*/
		
		//crop center of image at 188 x 266 pixels
		$image_cropped = $this->_initial_image_crop($image_upload_data['full_path'], $upload_type);
					
		//upload cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $image_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . (($live_image) ? '/' : '/originals/temp/') . $new_image_name . '_p.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//ALSO create initial cropped thumbnail version of image
		$thumb_image_cropped = $this->_generate_thumbnail($image_cropped, $upload_type);
		
		//upload thumb-cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $thumb_image_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . (($live_image) ? '/' : '/originals/temp/') . $new_image_name . '_t.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete all temporary files
		unlink($image_upload_data['full_path']);
		unlink($this->TMP_CC_WORKING_DIR . $image_cropped);	
		unlink($this->TMP_CC_WORKING_DIR . $thumb_image_cropped);		
		
		/* ---------------------- end initial crop to aspect ratio for display & thumbnail ----------------------*/
		
		//we just uploaded an image, this version is not live.
		$manage_image = $this->CI->session->flashdata('manage_image');
		
		if($live_image){
			
			if($manage_image !== false){
				$manage_image = json_decode($manage_image);
			}
			
			//update database with new image name and crop data
			$data = array(
				'image' => $this->image_data['image'],
				'x0' 	=> $this->image_data['x0'],
				'y0' 	=> $this->image_data['y0'],
				'x1' 	=> $this->image_data['x1'],
				'y1' 	=> $this->image_data['y1']
			);
	
			$this->CI->users_promoters->update_guest_list(
											((isset($manage_image->up_id)) ? $manage_image->up_id : false),
											((isset($manage_image->pgla_id)) ? $manage_image->pgla_id : false),
											$data
											);
			$manage_image->image_data = $data;
	
		}

		$this->CI->session->set_flashdata('manage_image', json_encode($manage_image));

		return true;
	}
	
	/**
	 * handles initial image upload from user. 
	 * 	- Verifies image meets restrictions
	 *  - Saves temporarily on server, moves original to S3 and creates initial 'cropped' version on S3
	 * 
	 * @return	null
	 * */
	private function _venues_banners_upload($upload_type, $image_data, $live_image){
				
		if(!$upload_type){
			$this->image_upload_error = 'Unknown error';
			return false;
		}
			
		/* ---------------------- verify size & type are allowed ----------------------*/
		$config['upload_path'] = $this->TMP_CC_WORKING_DIR;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size']	= '10000';
		$config['max_width'] = '8000';
		$config['max_height'] = '8000';
		$min_width = 1000;
		$min_height = 300;

		$this->CI->load->library('upload', $config);

		//image did not meet restrictions, exit function and save error message to object property
		if (!$this->CI->upload->do_upload('file')){
			//save error to public property of this class for use after function exits
			$this->image_upload_error = strip_tags($this->CI->upload->display_errors());				
			return false;
		}
			
		//collect upload data
		$image_upload_data = $this->CI->upload->data();
		
		list($image_width, $image_height) = getimagesize($image_upload_data['full_path']);
		if($image_width < $min_width || $image_height < $min_height){
			unlink($image_upload_data['full_path']);
			$this->image_upload_error = 'Image does not meet minimum height/width requirements.';
			return false;
		}
		
		/* ---------------------- end verify size & type are allowed ----------------------*/
		
		if($image_data){
			$this->_delete_old_images($image_data->image, $upload_type, $live_image);
		}
		
		/* ---------------------- convert to jpg ----------------------*/
		$this->_convert_to_jpeg($image_upload_data['full_path']);
		/* ---------------------- end convert to jpg ----------------------*/	

		
		/* ---------------------- rename and save base version to S3 ----------------------*/
		
		/*
		 * s3 naming schema for user profile pictures:
		 * 	  -- original, uncropped version. Probably will never be shown to users
		 * 		all original image versions go in 'originals' folder
		 * 
		 * _p -- profile version. cropped to a 3:4 aspect ratio and resized to W x H
		 * _t -- thumbnail version. smaller version of _p
		 * */
		
		$this->CI->load->library('s3');
							
		//creates a new image name for this promoter's uploaded profile picture
		$new_image_name = md5($this->CI->session->userdata('session_id') . time());
		
		//create 'image_data' array of values for use in web browser
		$this->image_data = array_merge($this->image_data, array('image' => $new_image_name, 'upload_type' => $upload_type));															
		
		//save original version of profile picture to s3
		$sourceFile = $image_upload_data['full_path'];
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . '/originals/temp/' . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);		
		/* ---------------------- end rename and save base version to S3 ----------------------*/
		
		/* ---------------------- initial crop to aspect ratio for display & thumbnail ----------------------*/
		
		//crop center of image at 188 x 266 pixels
		$image_cropped = $this->_initial_image_crop($image_upload_data['full_path'], $upload_type);
					
		//upload cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $image_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . '/originals/temp/' . $new_image_name . '_p.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//ALSO create initial cropped thumbnail version of image
		$thumb_image_cropped = $this->_generate_thumbnail($image_cropped, $upload_type);
		
		//upload thumb-cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $thumb_image_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/' . $upload_type . '/originals/temp/' . $new_image_name . '_t.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete all temporary files
		unlink($image_upload_data['full_path']);
		unlink($this->TMP_CC_WORKING_DIR . $image_cropped);	
		unlink($this->TMP_CC_WORKING_DIR . $thumb_image_cropped);		
		
		/* ---------------------- end initial crop to aspect ratio for display & thumbnail ----------------------*/
		
		return true;
	}

	/**
	 * handles initial image upload from user. 
	 * 	- Verifies image meets restrictions
	 *  - Saves temporarily on server, moves original to S3 and creates initial 'cropped' version on S3
	 * 
	 * @return	null
	 * */
	private function _promoter_image_upload(){
						
		/* ---------------------- verify size & type are allowed ----------------------*/
		$config['upload_path'] = $this->TMP_CC_WORKING_DIR;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size']	= '10000';
		$config['max_width'] = '8000';
		$config['max_height'] = '8000';
	//	$min_width = 200;
	//	$min_height = 266;
		$min_width = 240;
		$min_height = 319;

		$this->CI->load->library('upload', $config);

		//image did not meet restrictions, exit function and save error message to object property
		if (!$this->CI->upload->do_upload('file')){
			//save error to public property of this class for use after function exits
			$this->image_upload_error = strip_tags($this->CI->upload->display_errors());				
			return false;
		}
		
		//collect upload data
		$image_upload_data = $this->CI->upload->data();
		
		list($image_width, $image_height) = getimagesize($image_upload_data['full_path']);
		if($image_width < $min_width || $image_height < $min_height){
			unlink($image_upload_data['full_path']);
			$this->image_upload_error = 'Image does not meet minimum height/width requirements.';
			return false;
		}
		
		/* ---------------------- end verify size & type are allowed ----------------------*/
		
		
		/* ---------------------- convert to jpg ----------------------*/
		$this->_convert_to_jpeg($image_upload_data['full_path']);
		/* ---------------------- end convert to jpg ----------------------*/	

		
		/* ---------------------- rename and save base version to S3 ----------------------*/
		
		/*
		 * s3 naming schema for user profile pictures:
		 * 	  -- original, uncropped version. Probably will never be shown to users
		 * 		all original image versions go in 'originals' folder
		 * 
		 * _p -- profile version. cropped to a 3:4 aspect ratio and resized to W x H
		 * _t -- thumbnail version. smaller version of _p
		 * */
		
		//get name of promoter's profile picture from database and delete files on s3
		//since we are about to create a new profile picture
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$this->CI->load->config('s3');
		$this->CI->load->library('s3');
		$current_promoter = $this->CI->library_promoters->promoter;
				
		if(isset($current_promoter->up_profile_image) && strlen($current_promoter->up_profile_image) == 40)
			$this->_delete_old_images($current_promoter->up_profile_image);
							
		//creates a new image name for this promoter's uploaded profile picture
		$new_image_name = md5($current_promoter->up_id . time());
		
		//create 'image_data' array of values for use in web browser
		$this->image_data = array_merge($this->image_data, array('profile_img' => $new_image_name));															
		
		//save original version of profile picture to s3
		$sourceFile = $image_upload_data['full_path'];
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/profile-pics/originals/' . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);		
		/* ---------------------- end rename and save base version to S3 ----------------------*/
		
		
		/* ---------------------- initial crop to aspect ratio for profile picture display ----------------------*/
		
		//crop center of image at 3:4 aspect ratio and 240 x 319 pixels
		$profile_pic_cropped = $this->_initial_image_crop($image_upload_data['full_path'], 'profile-pics');
					
		//upload cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $profile_pic_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/profile-pics/' . $new_image_name . '_p.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//ALSO create initial cropped thumbnail version of image
		$thumb_profile_pic_cropped = $this->_generate_thumbnail($profile_pic_cropped, 'profile-pics');
		
		//upload thumb-cropped version to s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $thumb_profile_pic_cropped;
		$bucket = $this->CI->config->item('s3_bucket_name'); //vcweb2 as of 8.30.2011
		$newFileName = 'vc-images/profile-pics/' . $new_image_name . '_t.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete all temporary files
		unlink($image_upload_data['full_path']);
		unlink($this->TMP_CC_WORKING_DIR . $profile_pic_cropped);	
		unlink($this->TMP_CC_WORKING_DIR . $thumb_profile_pic_cropped);		
		
		/* ---------------------- end initial crop to aspect ratio for profile picture display ----------------------*/
		
		/* ----------------- update database with picture name + crop coordinates -------------- */
		
		
		$data = array(
					'profile_image' => $this->image_data['profile_img'],
					'original_width' => $this->image_data['original_width'],
					'original_height' => $this->image_data['original_height'],
					'x0' => $this->image_data['x0'],
					'y0' => $this->image_data['y0'],
					'x1' => $this->image_data['x1'],
					'y1' => $this->image_data['y1']
					);
		$this->CI->users_promoters->update_promoter(array('promoter_id' => $current_promoter->up_id),
													$data);
		
		/* ----------------- end update database with picture name + crop coordinates -------------- */			
		
		return true;												
	}

	/**
	 * crops a user's profile picture given the supplied coordates of the select area
	 * 
	 * @return	bool
	 * */
	function profile_picture_crop(){
		
		$min_width = 240;
		$min_height = 319;		
		
		//Is post properly formatted with required data for image crop?
		$required_crop_keys = array('height', 'width', 'x0', 'x1', 'y0', 'y1');
		//For easier access...
		$crop_array = $this->CI->input->post();
		
		foreach($required_crop_keys as $key)
			if(!array_key_exists($key, $crop_array)){
	//			log_message('error', 'image_upload->profile_picture_crop() called with post array missing key: ' . $key);
				die(json_encode(array('success' => false,
										'message' => 'improper post submission')));
			}
		
		//get the filename of this promoter's profile picture
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$current_promoter = $this->CI->library_promoters->promoter;
		if(!isset($current_promoter->up_profile_image)){
	//		log_message('error', 'image_upload->profile_picture_crop() unable to find profile picture image name for promoter ' . $this->CI->session->userdata('promoter_id'));
			die(json_encode(array('success' => false,
									'message' => 'Unable to find promoter profile picture')));
		}
		
		$image_name = $current_promoter->up_profile_image;
		
		//creates a new image name for this promoter's uploaded profile picture
		$new_image_name = md5($this->CI->session->userdata('session_id') . time());
		
		//create 'image_data' array of values for use in web browser and updating database
		$this->image_data = array_merge($this->image_data, array('profile_img' => $new_image_name,
																	'x0' => $crop_array['x0'],
																	'y0' => $crop_array['y0'],
																	'x1' => $crop_array['x1'],
																	'y1' => $crop_array['y1']));
	/*	MOVED FURTHER DOWN
		//update database with new image name and crop data
		$data = array(
					'profile_image' => $this->image_data['profile_img'],
					'x0' => $this->image_data['x0'],
					'y0' => $this->image_data['y0'],
					'x1' => $this->image_data['x1'],
					'y1' => $this->image_data['y1']
					);
		$this->CI->users_promoters->update_promoter(array('promoter_id' => $current_promoter->up_id),
													$data);
	*/
															
		//retrieve original promoter profile image from s3
		$this->CI->load->library('s3');
		$response_obj = $this->CI->s3->getObject('vcweb2', 'vc-images/profile-pics/originals/' . $image_name . '.jpg');
		
		//Did the request have an error?
		if($response_obj->error){
	//		log_message('error', 'failed to retrieve promoter profile picture from s3 for promoter:' . $this->CI->session->userdata('promoter_id'));
			die(json_encode(array('success' => false,
									'message' => 'Unable to retrieve promoter profile picture from s3')));
		}
		
		//delete old images from s3 associated with this user
		$this->_delete_old_images($image_name);
		
		//Convert raw image into gd resource
		$profile_img = imagecreatefromstring($response_obj->body);
		
		//place original back on s3 with new file name
		//create temporary file on filesystem
		imagejpeg($profile_img, $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
		
		//place temporary file on s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/profile-pics/originals/' . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete temporary file
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
		
		//crop image to requested coordinates
		$base_image = imagecreatetruecolor($min_width, $min_height);
		
		/*
		bool imagecopyresampled ( resource $dst_image , 
		resource $src_image ,
		 int $dst_x , int $dst_y , 
		int $src_x , int $src_y ,
		 int $dst_w , int $dst_h , 
		 int $src_w , int $src_h )
		*/
		
		//create cropped image
		imagecopyresampled($base_image, 
				$profile_img,
				0, 0,
				$crop_array['x0'], $crop_array['y0'],
				$min_width, $min_height,
				(intval($crop_array['x1']) - intval($crop_array['x0'])), 
				(intval($crop_array['y1']) - intval($crop_array['y0'])));
				
		//free memory :)
		imagedestroy($profile_img);
		
		//save temporarily to disk to transport to s3
		imagejpeg($base_image, $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//watermark the image
	//	$this->_apply_watermark($this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//transport image to s3, overwriting old cropped image
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/profile-pics/' . $new_image_name . '_p.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//create thumbnail version of image
		$thumbnail_image = $this->_generate_thumbnail($new_image_name . '_p.jpg', 'profile-pics');
		
		//transport image to s3, overwriting old cropped image
		$sourceFile = $this->TMP_CC_WORKING_DIR . $thumbnail_image;
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/profile-pics/' . $new_image_name . '_t.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//delete temporary files
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		unlink($this->TMP_CC_WORKING_DIR . $thumbnail_image);
		
		//update database with new image name and crop data
	
	  	$data = array(
					'profile_image' => $this->image_data['profile_img'],
					'x0' => $this->image_data['x0'],
					'y0' => $this->image_data['y0'],
					'x1' => $this->image_data['x1'],
					'y1' => $this->image_data['y1']
					);
		$this->CI->users_promoters->update_promoter(array('promoter_id' => $current_promoter->up_id),
													$data);
										
		return true;
	}

	/**
	 * Crop handling function for guest list and event images
	 * 
	 * @param	array
	 * @param 	sting
	 * @param 	bool
	 * 
	 * @return	bool
	 * */
	function image_crop($image_data, $image_type, $live_image = true){
		
	//	var_dump($image_data);
	//	var_dump($image_type);
	//	var_dump($live_image);
	//	die();
		
		/**
		 * Guest List Dimensions:
		 * 188w * 266h
		 * 
		 * Promoter Profile Pic Dimensions:
		 * 240w * 319h
		 * 
		 * Venue Banner Image Dimensions:
		 * 1000w * 300h
		 * 
		 */
		 
		switch($image_type){
			case 'guest_lists':
				$crop_width = 188;
				$crop_height = 266;
				break;
			case 'events':
				$crop_width = 188;
				$crop_height = 266;
				break;
			case 'venues/banners':
				$crop_width = 1000;
				$crop_height = 300;
				break;
		}
		
		//Is post properly formatted with required data for image crop?
		$required_crop_keys = array('height', 'width', 'x0', 'x1', 'y0', 'y1');
		//For easier access...
		$crop_array = $this->CI->input->post();
		
		foreach($required_crop_keys as $key)
			if(!array_key_exists($key, $crop_array)){
		//		log_message('error', 'image_upload->profile_picture_crop() called with post array missing key: ' . $key);
				die(json_encode(array('success' => false,
										'message' => 'improper post submission')));
			}
			
		//TODO: check each of the required_crop_keys for validity
		
		$image_name = $image_data->image;
		
		//creates a new image name for this promoter's uploaded profile picture
		$new_image_name = md5($this->CI->session->userdata('session_id') . time());		
			
		//create 'image_data' array of values for use in web browser and updating database
		$this->image_data = array_merge($this->image_data, array('image' => $new_image_name,
																	'x0' => $crop_array['x0'],
																	'y0' => $crop_array['y0'],
																	'x1' => $crop_array['x1'],
																	'y1' => $crop_array['y1']));
		
//		$string = 'vc-images/' . $image_type . '/originals/' . (($live_image) ? '' : 'temp/') . $image_name . '.jpg';
				
		//retrieve original promoter profile image from s3
		$this->CI->load->library('s3');
		$response_obj = $this->CI->s3->getObject('vcweb2', 'vc-images/' . $image_type . '/originals/' . (($live_image) ? '' : 'temp/') . $image_name . '.jpg');
				
		//Did the request have an error?
		if(!$response_obj || $response_obj->error){
		//	log_message('error', 'failed to retrieve promoter profile picture from s3 for promoter:' . $this->CI->session->userdata('promoter_id'));
			die(json_encode(array('success' => false,
									'message' => 'Unable to retrieve promoter profile picture from s3')));
		}
		
	
		//Convert raw image into gd resource
		$image = imagecreatefromstring($response_obj->body);
		
		//place original back on s3 with new file name
		//create temporary file on filesystem
		imagejpeg($image, $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
		
		//place temporary file on s3
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg';
		$bucket = 'vcweb2';
		$newFileName = 'vc-images/' . $image_type . '/originals/' . ((!$live_image) ? 'temp/' : '') . $new_image_name . '.jpg';
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
			//delete temporary file
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '.jpg');
	
		//crop image to requested coordinates
		$base_image = imagecreatetruecolor($crop_width, $crop_height);
				
		//create cropped image
		imagecopyresampled($base_image, 
				$image,
				0, 0,
				$crop_array['x0'], $crop_array['y0'],
				
				$crop_width, $crop_height,
				
				$crop_array['width'], $crop_array['height']);
				
		//free memory :)
		imagedestroy($image);
		
		//save temporarily to disk to transport to s3
		imagejpeg($base_image, $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//watermark the image
	//	$this->_apply_watermark($this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		
		//-----------------------------------------------------
		
		//transport image to s3, overwriting old cropped image
		$sourceFile = $this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg';
		$bucket = 'vcweb2';
		
		if($live_image){
			$newFileName = 'vc-images/' . $image_type . '/' . $new_image_name . '_p.jpg';
		}else{
			$newFileName = 'vc-images/' . $image_type . '/originals/temp/' . $new_image_name . '_p.jpg';
		}
		
		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//-----------------------------------------------------
		
		//create thumbnail version of image
		$thumbnail_image = $this->_generate_thumbnail($new_image_name . '_p.jpg', $image_type);
		
		//transport image to s3, overwriting old cropped image
		$sourceFile = $this->TMP_CC_WORKING_DIR . $thumbnail_image;
		$bucket = 'vcweb2';
		
		if($live_image){
			$newFileName = 'vc-images/' . $image_type . '/' . $new_image_name . '_t.jpg';
		}else{
			$newFileName = 'vc-images/' . $image_type . '/originals/temp/' . $new_image_name . '_t.jpg';
		}

		$ACL = S3::ACL_PUBLIC_READ;
		$this->CI->s3->putObjectFile($sourceFile, $bucket, $newFileName, $ACL, array(), $this->cache_settings);
		
		//-----------------------------------------------------
		
		//delete temporary files
		unlink($this->TMP_CC_WORKING_DIR . $new_image_name . '_p.jpg');
		unlink($this->TMP_CC_WORKING_DIR . $thumbnail_image);
		
		if($live_image){
			
			//update database with new image name and crop data
			$data = array(
				'image' => $this->image_data['image'],
				'x0' 	=> $this->image_data['x0'],
				'y0' 	=> $this->image_data['y0'],
				'x1' 	=> $this->image_data['x1'],
				'y1' 	=> $this->image_data['y1']
			);

			//doing this just for pgla_id
			$manage_image = $this->CI->session->flashdata('manage_image');
			$manage_image = json_decode($manage_image);
						
			switch($image_type){
				case 'guest_lists':
					$this->CI->users_promoters->update_guest_list(
															((isset($manage_image->up_id)) ? $manage_image->up_id : false),
															((isset($manage_image->pgla_id)) ? $manage_image->pgla_id : false),
															$data
															);
					$manage_image->image_data = $data;
					break;
				case 'events':
			//		$this->CI->users_promoters->update_event(array('promoter_id' => $current_promoter->up_id),
			//											$event_id,
			//											$data);
					break;
			}
	
		}
					
		//delete old images from s3 associated with this user
		$this->_delete_old_images($image_name, $image_type, $live_image);
	
											
		return true;
	}

	/**
	 * keeps track of which images should be removed every time a new set of images are created
	 * 
	 * @return	null
	 * */
	private function _delete_old_images($image_name, $type = 'promoter', $live_image = true){
		
		if(!$live_image){
			//If this is not a live image, don't bother deleting... it will expire in 24 hours
			return;
		}
		
		$this->CI->load->library('s3');
		
		switch($type){
			case 'promoter':
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/profile-pics/originals/' . $image_name . '.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/profile-pics/' . $image_name . '_p.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/profile-pics/' . $image_name . '_t.jpg');
				break;
			case 'guest_lists':
				
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/guest_lists/originals/' . ((!$live_image) ? 'temp/' : '') . $image_name . '.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/guest_lists/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_p.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/guest_lists/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_t.jpg');
				
				break;
			case 'events':
				
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/events/originals/' . ((!$live_image) ? 'temp/' : '') . $image_name . '.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/events/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_p.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/events/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_t.jpg');
				
				break;
			case 'venues':
				
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/venues/originals/' . ((!$live_image) ? 'temp/' : '') . $image_name . '.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/venues/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_p.jpg');
		//		$this->CI->s3->deleteObject('vcweb2', 'vc-images/venues/' . ((!$live_image) ? 'originals/temp/' : '') . $image_name . '_t.jpg');
				
				break;
			default:
				//error?
				break;
		}

	}

	/**
	 * Opens an image file, scales it, and saves it as a jpg
	 * 
	 * @param	string (path to image)
	 * @param	int (scaled width of image)
	 * @param	int (scaled height of image)
	 * @param	double (factor to scale height/width by)
	 * @return	null
	 * */
	function resizeImage($image_path, $width, $height, $scale) {
		$image_data = getimagesize($image_path);
		$imageType = image_type_to_mime_type($image_data[2]);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
		
		switch($imageType) {
			case "image/gif":
				$source = imagecreatefromgif($image_path); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source = imagecreatefromjpeg($image_path); 
				break;
		    case "image/png":
			case "image/x-png":
				$source = imagecreatefrompng($image_path); 
				break;
	  	}
		
		imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
		
		//over write original image as jpeg
		imagejpeg($newImage, $image_path);
		
		//free memory!
		imagedestroy($newImage);
		
		//make sure new image is writable by all
		chmod($image_path, 0777);
	}
	
	/*
	 * Document Me!
	 * 
	 * @param
	 * @param
	 * @param
	 * @param
	 * @param
	 * @param
	 * @param
	 * @return
	 */
	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		
		
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumb_image_name); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}

	/**
	 * When user uploads initial profile picture crop center area with a 3:4
	 * aspect ratio. This will be their default profile picture if they do 
	 * not alter the cropping of their profile picture.
	 * 
	 * @param	string (path to image)
	 * @return	string (path to cropped version of image)
	 * */
	private function _initial_image_crop($image_path, $upload_type){
		
		//crop center of image at 3:4 aspect ratio, 200 x 266 (w x h)
		list($image_width, $image_height) = getimagesize($image_path);
		
		//constants
//		$profile_img_width = 200;
//		$profile_img_height = 266;
		
		
		switch($upload_type){
			case 'profile-pics':
				$crop_width = 240;
				$crop_height = 319;
				break;
			case 'events':
				
			case 'guest_lists':
				$crop_width = 188;
				$crop_height = 266;
				break;
			case 'venues/banners':
				$crop_width = 1000;
				$crop_height = 300;
				break;
		}
	
		
		$profile_img_start_x = floor($image_width / 2) - floor($crop_width / 2);
		$profile_img_start_y = floor($image_height / 2) - floor($crop_height / 2);
		
		//create 'image_data' array of values for use in web browser
		$this->image_data = array_merge($this->image_data, array('original_width' => $image_width,
																	'original_height' => $image_height,
																	'x0' => $profile_img_start_x,
																	'y0' => $profile_img_start_y,
																	'x1' => $profile_img_start_x + $crop_width,
																	'y1' => $profile_img_start_y + $crop_height));
		
		//load necessary images into memory for manipulation
		$temp_image = imagecreatefromjpeg($image_path);
		$cropped_image = imagecreatetruecolor($crop_width, $crop_height);
		
		//create cropped image
		imagecopy($cropped_image, 
				$temp_image, 
				0,
				0,
				$profile_img_start_x,
				$profile_img_start_y,
				$crop_width,
				$crop_height);
		
		//give temporary file a unique name
		$temp_local_name = md5($this->CI->session->userdata('session_id') . time() . 'initial_crop');
		
		//save image temporarily on server filesystem
		imagejpeg($cropped_image, $this->TMP_CC_WORKING_DIR . $temp_local_name);
		
		//watermark the image
	//	$this->_apply_watermark($this->TMP_CC_WORKING_DIR . $temp_local_name);
	
		return $temp_local_name;
	}

	/**
	 * Takes a cropped promoter profile picture and generates a small thumbnail version
	 * for use in search results and promoter profile badges
	 * 
	 * @param	string (filename of cropped profile image)
	 * @return	string (filename of newly created thumb image)
	 */
	private function _generate_thumbnail($image_name, $image_type){
		
		//	var_dump($image_name); 
		//	var_dump($this->TMP_CC_WORKING_DIR);
		switch($image_type){
			case 'events':
			case 'guest_lists':
				$min_original_width = 188;
				$min_original_height = 266;
				$thumb_width = 66;
				$thumb_height = 93;
				break;
			case 'profile-pics':
				$min_original_width = 240;
				$min_original_height = 319;
				$thumb_width = 66;
				$thumb_height = 88;
				break;
			case 'venues/banners':
				$min_original_width = 1000;
				$min_original_height = 300;
				$thumb_width = 286;
				$thumb_height = 86;
				break;
		}
		
		//generate image resource from image name
		$profile_image = imagecreatefromjpeg($this->TMP_CC_WORKING_DIR . $image_name);
		
		//blank thumb-sized image to copy profile image onto
		$thumbnail_image = imagecreatetruecolor($thumb_width, $thumb_height);
		
		imagecopyresampled($thumbnail_image, 
					$profile_image, 
					0, 
					0, 
					0, 
					0,
					$thumb_width,
					$thumb_height,
					$min_original_width, 
					$min_original_height);
		
		//give temporary file a unique name
		$temp_local_name = md5($this->CI->session->userdata('session_id') . time() . 'thumbnail');
		
		//save image temporarily on server filesystem
		imagejpeg($thumbnail_image, $this->TMP_CC_WORKING_DIR . $temp_local_name);
		
		//watermark the image
	//	$this->_apply_watermark($this->TMP_CC_WORKING_DIR . $temp_local_name);
	
		return $temp_local_name;
		
	}

	/*
	 * takes a reference to an image resource and applys the vibecompass watermark to that image
	 * 
	 * called from _initial_image_crop and _profile_picture_crop
	 * 
	 * @param	path to image
	 * @return	null
	 * */
	private function _apply_watermark($image_path){
		
		$this->CI->load->library('image_lib');
		
		chmod($image_path, '777');
		
		$config['source_image'] = $image_path;
		$config['wm_text'] = 'VibeCompass';
		$config['wm_type'] = 'text';
		$config['wm_font_path'] = FCPATH . 'system/fonts/textb.ttf';
		$config['wm_font_size'] = '16';
		$config['wm_font_color'] = 'ffffff';
		$config['wm_vrt_alignment'] = 'bottom';
		$config['wm_hor_alignment'] = 'center';
		$config['wm_padding'] = '20';
		
		
		$this->CI->image_lib->initialize($config);
		$this->CI->image_lib->watermark();
	}

	/**
	 * opens image at specified path, converts to jpeg and 
	 * overwrites original image with new jpg
	 * 
	 * @param	file_path
	 * @return	null
	 * */
	private function _convert_to_jpeg($image_path){
		//get type of specified image
		list($imagewidth, $imageheight, $imageType) = getimagesize($image_path);
		$imageType = image_type_to_mime_type($imageType);
		
		//create gd object/resource based on type
		switch($imageType) {
			case "image/gif":
				$source = imagecreatefromgif($image_path); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source = imagecreatefromjpeg($image_path); 
				break;
		    case "image/png":
			case "image/x-png":
				$source = imagecreatefrompng($image_path); 
				break;
	  	}
		
		//overwrite original
		imagejpeg($source, $image_path);
		
		//free memory! :)
		imagedestroy($source);
	}
}

/* End of file Image_upload.php */
/* Location: ./application/libraries/Image_upload.php */