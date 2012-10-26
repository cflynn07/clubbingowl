<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* Name:  Twilio
	*
	* Author: Ben Edmunds
	*		  ben.edmunds@gmail.com
	*         @benedmunds
	*
	* Location:
	*
	* Created:  03.29.2011
	*
	* Description:  Twilio configuration settings.
	*
	*
	*/

	/**
	 * Mode ("sandbox" or "prod")
	 **/
	$config['mode']   = 'prod';

	/**
	 * Account SID
	 **/
	$config['account_sid']   = 'AC62befd2e1ad5c86d59a6b140d935096e';

	/**
	 * Auth Token
	 **/
	$config['auth_token']    = '8c2406f63e9ec2ae7a0ed278576f4a44';

	/**
	 * API Version
	 **/
	$config['api_version']   = '2010-04-01';

	/**
	 * Twilio Phone Number
	 **/
	$config['number']        = '617-431-2099';


/* End of file twilio.php */