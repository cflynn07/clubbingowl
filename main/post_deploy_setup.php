<?php

if(php_sapi_name() != 'cli')
	die();

$custom_env_file = '/home/dotcloud/current/custom.json';
$fh = fopen($custom_env_file, 'w+');

$json = new stdClass;
$json->deployment_unique_id = md5(time() . rand(0, 1000));

fwrite($fh, json_encode($json));
fclose($fh);
