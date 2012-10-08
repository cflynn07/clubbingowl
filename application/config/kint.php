<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Kint
| -------------------------------------------------------------------------
| Kint is a wrapper of var_dump that provides a far greater detailed display
| of debugging output.
|
*/

$config['kint_settings'] = array(
	/**
	 * @var callback
	 *
	 * @param string $file filename where the function was called
	 * @param int|NULL $line the line number in the file (not applicable when used in resource dumps)
	 */
	'pathDisplayCallback' => "kint::_debugPath",


	/** @var int max length of string before it is truncated and displayed separately in full */
	'maxStrLength' => 60,


	/** @var int max array/object levels to go deep, if zero no limits are applied */
	'maxLevels' => 8,

	/** @var bool if set to false, kint will become silent */
	'enabled' => TRUE,


	/** @var string the css file to format the output of kint */
	'skin' => 'kint.css',
);

//deactivate kint if in production
if(ENVIRONMENT == 'production'){
	$config['kint_settings']['enabled'] = FALSE;
}

/* End of file kint.php */
/* Location: ./application/config/kint.php */