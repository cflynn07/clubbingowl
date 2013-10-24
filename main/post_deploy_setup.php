<?php

if(php_sapi_name() != 'cli')
	die();


// Read the file and convert the underlying JSON dictionary into a PHP array
$env = json_decode(file_get_contents("/home/dotcloud/environment.json"), true);


$custom_env_file = '/home/dotcloud/current/custom.json';
$fh = fopen($custom_env_file, 'w+');

$json = new stdClass;
//$json->deployment_unique_id = md5(time() . rand(0, 1000));


$cust_dir = exec('cd ~/code && pwd -P');
$json->deployment_unique_id = md5($cust_dir);



fwrite($fh, json_encode($json));
fclose($fh);
