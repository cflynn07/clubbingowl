<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cobar Systems LLC credentials
 * 
 */
$config["accessKey"] = "AKIAJJAKWQUEVAMRXU5A";
$config["secretKey"] = "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";
$config["useSSL"] = FALSE;
$config['s3_bucket_name'] = 'clubbingowl';

$protocol = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http');
$base_url = $protocol . '://www.staticowl.' . TLD . '/';

$config['s3_base_url'] 			= $base_url . $config['s3_bucket_name'] . '/'; 
$config['s3_assets_base_url'] 	= $config['s3_base_url'] 		. 'assets/';
$config['karma_assets'] 		= $config['s3_assets_base_url'] . 'web_old/';
$config['front_assets'] 		= $config['s3_assets_base_url'] . 'web/';
$config['admin_assets'] 		= $config['s3_assets_base_url'] . 'admin/';
$config['global_assets'] 		= $config['s3_assets_base_url'] . 'global/';
$config['facebook_assets'] 		= $config['s3_assets_base_url'] . 'facebook/';

$config['s3_uploaded_images_base_url'] = $protocol . '://d1pv30wi5cq71r.cloudfront.net/vc-images/'; 
$config['s3_promoter_picture_base_url'] = $config['s3_uploaded_images_base_url'] . 'profile-pics/';

unset($base_url);
unset($protocol);

/* End of file s3.php */
/* Location: ./application/config/s3.php */