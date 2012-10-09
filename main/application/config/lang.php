<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Responsible for all regionalization/language configuration settings

if(isset($_SERVER['HTTP_HOST']))
	$host = explode('.', $_SERVER['HTTP_HOST']);
else
	$host[0] = 'www';

switch($host[0]){
	case 'www':
		$lang = 'english';
		$lang_locale = 'en_EN';
		break;
	case 'es':
		$lang = 'spanish';
		$lang_locale = 'es_ES';
		break;
	case 'de':
		$lang = 'german';
		$lang_locale = 'de_DE';
		break;
	case 'ja':
		$lang = 'japanese';
		$lang_locale = 'ja_JP';
		break;
		
		
/*	case 'ar':
		$lang = 'arabic';
		break;
	case 'cs':
		$lang = 'czech';
		break;
	case 'de':
		$lang = 'german';
		break;
	case 'el':
		$lang = 'greek';
		break;
	case 'es':
		$lang = 'spanish';
		break;
	case 'fr':
		$lang = 'french';
		break;
	case 'hi':
		$lang = 'hindi';
		break;
	case 'it':
		$lang = 'italian';
		break;
	case 'iw':
		$lang = 'hebrew';
		break;
	case 'ja':
		$lang = 'japanese';
		break;
	case 'ko':
		$lang = 'korean';
		break;
	case 'nl':
		$lang = 'dutch';
		break;
	case 'no':
		$lang = 'norwegian';
		break;
	case 'pl':
		$lang = 'polish';
		break;
	case 'pt':
		$lang = 'portuguese';
		break;
	case 'ru':
		$lang = 'russian';
		break;
	case 'sv':
		$lang = 'swedish';
		break;
	case 'zh':
		$lang = 'chinese';
		break; */
		
		
		
	default: 
	//error
		$lang = 'english';
		break;
}

$config['supported_lang_codes'] = array(
	'en',
	'es',
	'de',
	'ja'
);

$config['supported_langs'] = array(
	'en' 	=> 'english',
	'es' 	=> 'spanish',
	'de' 	=> 'german',
	'ja'	=> 'japanese'
);

$config['current_lang'] = $lang;

$config['active_subdomain'] = $host[0];


$config['current_lang_code'] = $host[0];
if($host[0] == 'www')
	$config['current_lang_code'] = 'en';

$config['current_lang_locale'] = $lang_locale . ((DEPLOYMENT_ENV == 'cloudcontrol') ? '.utf8' : '');

/* End of file lang.php */
/* Location: ./application/config/lang.php */