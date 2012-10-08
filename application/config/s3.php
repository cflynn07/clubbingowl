<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config["accessKey"] = "AKIAJF43DXAJ4J6K3MIA";
$config["secretKey"] = "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";
$config["useSSL"] = FALSE;





//UPLOADED IMAGES S3 BUCKET CDN
//d1pv30wi5cq71r.cloudfront.net


//------------------------------


//ASSETS FROM CC SERVERS CDN
//d2u307swt14szw.cloudfront.net




/*
 * during testing/development serve contents directly from the s3 bucket.
 * 
 * While live, serve contents directly from cloudfront CDN
 * */

$config['s3_bucket_name'] = 'vcweb2';

if(isset($_SERVER['HTTP_HOST'])){
    $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
    $base_url .= '://'. $_SERVER['HTTP_HOST'];
    $base_url .= isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80' ? ( ':'.$_SERVER['SERVER_PORT'] ) : '';
  	$base_url .= '/';
	
//    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
}else{
    $base_url = 'http://vibecompass.dev/';
}

if(ENVIRONMENT == 'development'){
			
	if(DEPLOYMENT_ENV == 'cloudcontrol'){
		//cloudcontrol
		
		//Switch between using assets on S3 and using assets on server during development.
		//Note, when deployed assets dir will be set to 'Deny from all' via .htaccess and only S3 bucket
		//or cloudfront will be used.
		
		//$config['s3_base_url'] = 'http://' . $config['s3_bucket_name'] . '.s3.amazonaws.com/';
		$config['s3_base_url'] = $base_url . $config['s3_bucket_name'] . '/'; 
		
	}else{
		//local
		
		//$config['s3_base_url'] = 'http://' . $config['s3_bucket_name'] . '.s3.amazonaws.com/';
		$config['s3_base_url'] = $base_url . $config['s3_bucket_name'] . '/';  
		
	}
	
}else{
	
	
	//Serve files directly from CloudControl Apache
//	$config['s3_base_url'] = $base_url . $config['s3_bucket_name'] . '/';
	
	//Use CDN -- production (better)
	if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
		$config['s3_base_url'] = 'https://d2u307swt14szw.cloudfront.net/' . $config['s3_bucket_name'] . '/';
	else
		$config['s3_base_url'] = 'http://d2u307swt14szw.cloudfront.net/' . $config['s3_bucket_name'] . '/';
	
}

$config['s3_assets_base_url'] = $config['s3_base_url'] . 'assets/';

$config['karma_assets'] = $config['s3_assets_base_url'] . 'web_old/';
$config['front_assets'] = $config['s3_assets_base_url'] . 'web/';

$config['admin_assets'] = $config['s3_assets_base_url'] . 'admin/';
$config['global_assets'] = $config['s3_assets_base_url'] . 'global/';
$config['facebook_assets'] = $config['s3_assets_base_url'] . 'facebook/';



//S3 -- NO CDN
//$config['s3_uploaded_images_base_url'] = 'http://' . $config['s3_bucket_name'] . '.s3.amazonaws.com/vc-images/'; 

//S3 -- CDN
if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
	$config['s3_uploaded_images_base_url'] = 'https://d1pv30wi5cq71r.cloudfront.net/vc-images/'; 
else 
	$config['s3_uploaded_images_base_url'] = 'http://d1pv30wi5cq71r.cloudfront.net/vc-images/'; 


//$config['s3_base_url'] . 'vc-images/';
$config['s3_promoter_picture_base_url'] = $config['s3_uploaded_images_base_url'] . 'profile-pics/';

unset($base_url);

/* End of file s3.php */
/* Location: ./application/config/s3.php */