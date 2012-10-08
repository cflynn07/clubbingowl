<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Production Gearman Servers
 * (public DNS)
 * ec2-46-51-146-184.eu-west-1.compute.amazonaws.com
 * ec2-46-137-57-19.eu-west-1.compute.amazonaws.com
 * 
 * Development Gearman Servers
 * (Internal DNS)
 * ip-10-228-27-117.eu-west-1.compute.internal
 * ip-10-224-110-185.eu-west-1.compute.internal
 * 
 */

 
$local_gearman 	= 	array(
 						'servers' => array(
// 											'ec2-79-125-91-67.eu-west-1.compute.amazonaws.com',
//											'ec2-46-51-152-150.eu-west-1.compute.amazonaws.com'
											'ec2-46-137-54-60.eu-west-1.compute.amazonaws.com'
											)
						);
						
$cc_gearman 	= 	array(
 						'servers' => array(
// 											'ec2-46-137-142-15.eu-west-1.compute.amazonaws.com',	  	//		'ip-10-48-203-76.eu-west-1.compute.internal',
//											'ec2-46-51-138-75.eu-west-1.compute.amazonaws.com'    		//'ip-10-227-59-108.eu-west-1.compute.internal'
											'ec2-54-247-157-205.eu-west-1.compute.amazonaws.com'
											)
 						);


if(DEPLOYMENT_ENV == 'cloudcontrol'){
	//cloudcontrol
	$gearman = $cc_gearman;
	
}else{
	//local
	$gearman = $local_gearman;
	
}

$config['gearman'] = $gearman;

/* End of file autoload.php */
/* Location: ./application/config/autoload.php */