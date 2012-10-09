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

 
$gearman 	= 	array(
 						'servers' => array(
 							'ec2-23-21-32-142.compute-1.amazonaws.com'
						)
					);
						

$config['gearman'] = $gearman;

/* End of file autoload.php */
/* Location: ./application/config/autoload.php */