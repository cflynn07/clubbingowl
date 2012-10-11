<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Responsible for all regionalization/language configuration settings
/**

DOTCLOUD LOCALES -------------------------
		
		aa_DJ
		aa_DJ.utf8
		aa_ER
		aa_ET
		af_ZA
		am_ET
		an_ES
		ar_AE
		ar_BH
		ar_DZ
		ar_EG
		ar_IN
		ar_IQ
		ar_JO
		ar_KW
		ar_LB
		ar_LY
		ar_MA
		ar_OM
		ar_QA
		ar_SA
		ar_SD
		ar_SY
		ar_TN
		ar_YE
		as_IN.utf8
		ast_ES
		az_AZ.utf8
		be_BY
		ber_DZ
		ber_MA
		bg_BG
		bn_BD
		bn_IN
		bo_CN
		bo_IN
		br_FR
		bs_BA
		byn_ER
		C
		ca_AD
		ca_ES
		ca_FR
		ca_IT
		crh_UA
		csb_PL
		cs_CZ
		cy_GB
		da_DK
		de_AT
		de_BE
		de_CH
		de_DE
		de_LI.utf8
		de_LU
		dv_MV
		dz_BT
		el_CY
		el_GR
		en_AG
		en_AU
		en_AU.utf8
		en_BW
		en_BW.utf8
		en_CA
		en_CA.utf8
		en_DK
		en_DK.utf8
		en_GB
		en_GB.utf8
		en_HK
		en_HK.utf8
		en_IE
		en_IE.utf8
		en_IN
		en_NG
		en_NZ
		en_NZ.utf8
		en_PH
		en_PH.utf8
		en_SG
		en_SG.utf8
		en_US
		en_US.utf8
		en_ZA
		en_ZA.utf8
		en_ZW
		en_ZW.utf8
		eo_US.utf8
		es_AR
		es_BO
		es_CL
		es_CO
		es_CR
		es_DO
		es_EC
		es_ES
		es_GT
		es_HN
		es_MX
		es_NI
		es_PA
		es_PE
		es_PR
		es_PY
		es_SV
		es_US
		es_UY
		es_VE
		et_EE
		eu_ES
		eu_FR
		fa_IR
		fi_FI
		fil_PH
		fo_FO
		fr_BE
		fr_CA
		fr_CH
		fr_FR
		fr_FR.utf8
		fr_LU
		fur_IT
		fy_DE
		fy_NL
		ga_IE
		gd_GB
		gez_ER
		gez_ET
		gl_ES
		gu_IN
		gv_GB
		ha_NG
		he_IL
		hi_IN
		hne_IN
		hr_HR
		hsb_DE
		ht_HT
		hu_HU
		hy_AM
		id_ID
		ig_NG
		ik_CA
		is_IS
		it_CH
		it_IT
		iu_CA
		iw_IL
		ja_JP.utf8
		ka_GE
		kk_KZ
		kl_GL
		km_KH
		kn_IN
		ko_KR.utf8
		ks_IN
		ku_TR
		kw_GB
		ky_KG
		la_AU.utf8
		lg_UG
		li_BE
		li_NL
		lo_LA
		lt_LT
		lv_LV
		mai_IN
		mg_MG
		mi_NZ
		mk_MK
		ml_IN
		mn_MN
		mr_IN
		ms_MY
		mt_MT
		my_MM
		nan_TW@latin
		nb_NO
		nds_DE
		nds_NL
		ne_NP
		nl_AW
		nl_BE
		nl_NL
		nn_NO
		nr_ZA
		nso_ZA
		oc_FR
		om_ET
		om_KE
		or_IN
		pa_IN
		pap_AN
		pa_PK
		pl_PL
		POSIX
		ps_AF
		pt_BR
		pt_PT
		ro_RO
		ru_RU
		ru_UA
		rw_RW
		sa_IN
		sc_IT
		sd_IN
		se_NO
		shs_CA
		sid_ET
		si_LK
		sk_SK
		sl_SI
		so_DJ
		so_ET
		so_KE
		so_SO
		sq_AL
		sr_ME
		sr_RS
		ss_ZA
		st_ZA
		sv_FI
		sv_SE
		ta_IN
		te_IN
		tg_TJ
		th_TH
		ti_ER
		ti_ET
		tig_ER
		tk_TM
		tlh_GB.utf8
		tl_PH
		tn_ZA
		tr_CY
		tr_TR
		ts_ZA
		tt_RU@iqtelif.UTF-8
		tt_RU.utf8
		ug_CN
		uk_UA
		ur_PK
		uz_UZ
		ve_ZA
		vi_VN
		wa_BE
		wal_ET
		wo_SN
		xh_ZA
		yi_US
		yo_NG
		zh_CN
		zh_HK
		zh_SG
		zh_TW
		zu_ZA 

MY LAPTOP LOCALES -------------------------

		af_ZA
		af_ZA.ISO8859-1
		af_ZA.ISO8859-15
		af_ZA.UTF-8
		am_ET
		am_ET.UTF-8
		be_BY
		be_BY.CP1131
		be_BY.CP1251
		be_BY.ISO8859-5
		be_BY.UTF-8
		bg_BG
		bg_BG.CP1251
		bg_BG.UTF-8
		ca_ES
		ca_ES.ISO8859-1
		ca_ES.ISO8859-15
		ca_ES.UTF-8
		cs_CZ
		cs_CZ.ISO8859-2
		cs_CZ.UTF-8
		da_DK
		da_DK.ISO8859-1
		da_DK.ISO8859-15
		da_DK.UTF-8
		de_AT
		de_AT.ISO8859-1
		de_AT.ISO8859-15
		de_AT.UTF-8
		de_CH
		de_CH.ISO8859-1
		de_CH.ISO8859-15
		de_CH.UTF-8
		de_DE
		de_DE.ISO8859-1
		de_DE.ISO8859-15
		de_DE.UTF-8
		el_GR
		el_GR.ISO8859-7
		el_GR.UTF-8
		en_AU
		en_AU.ISO8859-1
		en_AU.ISO8859-15
		en_AU.US-ASCII
		en_AU.UTF-8
		en_CA
		en_CA.ISO8859-1
		en_CA.ISO8859-15
		en_CA.US-ASCII
		en_CA.UTF-8
		en_GB
		en_GB.ISO8859-1
		en_GB.ISO8859-15
		en_GB.US-ASCII
		en_GB.UTF-8
		en_IE
		en_IE.UTF-8
		en_NZ
		en_NZ.ISO8859-1
		en_NZ.ISO8859-15
		en_NZ.US-ASCII
		en_NZ.UTF-8
		en_US
		en_US.ISO8859-1
		en_US.ISO8859-15
		en_US.US-ASCII
		en_US.UTF-8
		es_ES
		es_ES.ISO8859-1
		es_ES.ISO8859-15
		es_ES.UTF-8
		et_EE
		et_EE.ISO8859-15
		et_EE.UTF-8
		eu_ES
		eu_ES.ISO8859-1
		eu_ES.ISO8859-15
		eu_ES.UTF-8
		fi_FI
		fi_FI.ISO8859-1
		fi_FI.ISO8859-15
		fi_FI.UTF-8
		fr_BE
		fr_BE.ISO8859-1
		fr_BE.ISO8859-15
		fr_BE.UTF-8
		fr_CA
		fr_CA.ISO8859-1
		fr_CA.ISO8859-15
		fr_CA.UTF-8
		fr_CH
		fr_CH.ISO8859-1
		fr_CH.ISO8859-15
		fr_CH.UTF-8
		fr_FR
		fr_FR.ISO8859-1
		fr_FR.ISO8859-15
		fr_FR.UTF-8
		he_IL
		he_IL.UTF-8
		hi_IN.ISCII-DEV
		hr_HR
		hr_HR.ISO8859-2
		hr_HR.UTF-8
		hu_HU
		hu_HU.ISO8859-2
		hu_HU.UTF-8
		hy_AM
		hy_AM.ARMSCII-8
		hy_AM.UTF-8
		is_IS
		is_IS.ISO8859-1
		is_IS.ISO8859-15
		is_IS.UTF-8
		it_CH
		it_CH.ISO8859-1
		it_CH.ISO8859-15
		it_CH.UTF-8
		it_IT
		it_IT.ISO8859-1
		it_IT.ISO8859-15
		it_IT.UTF-8
		ja_JP
		ja_JP.eucJP
		ja_JP.SJIS
		ja_JP.UTF-8
		kk_KZ
		kk_KZ.PT154
		kk_KZ.UTF-8
		ko_KR
		ko_KR.CP949
		ko_KR.eucKR
		ko_KR.UTF-8
		lt_LT
		lt_LT.ISO8859-13
		lt_LT.ISO8859-4
		lt_LT.UTF-8
		nl_BE
		nl_BE.ISO8859-1
		nl_BE.ISO8859-15
		nl_BE.UTF-8
		nl_NL
		nl_NL.ISO8859-1
		nl_NL.ISO8859-15
		nl_NL.UTF-8
		no_NO
		no_NO.ISO8859-1
		no_NO.ISO8859-15
		no_NO.UTF-8
		pl_PL
		pl_PL.ISO8859-2
		pl_PL.UTF-8
		pt_BR
		pt_BR.ISO8859-1
		pt_BR.UTF-8
		pt_PT
		pt_PT.ISO8859-1
		pt_PT.ISO8859-15
		pt_PT.UTF-8
		ro_RO
		ro_RO.ISO8859-2
		ro_RO.UTF-8
		ru_RU
		ru_RU.CP1251
		ru_RU.CP866
		ru_RU.ISO8859-5
		ru_RU.KOI8-R
		ru_RU.UTF-8
		sk_SK
		sk_SK.ISO8859-2
		sk_SK.UTF-8
		sl_SI
		sl_SI.ISO8859-2
		sl_SI.UTF-8
		sr_YU
		sr_YU.ISO8859-2
		sr_YU.ISO8859-5
		sr_YU.UTF-8
		sv_SE
		sv_SE.ISO8859-1
		sv_SE.ISO8859-15
		sv_SE.UTF-8
		tr_TR
		tr_TR.ISO8859-9
		tr_TR.UTF-8
		uk_UA
		uk_UA.ISO8859-5
		uk_UA.KOI8-U
		uk_UA.UTF-8
		zh_CN
		zh_CN.eucCN
		zh_CN.GB18030
		zh_CN.GB2312
		zh_CN.GBK
		zh_CN.UTF-8
		zh_HK
		zh_HK.Big5HKSCS
		zh_HK.UTF-8
		zh_TW
		zh_TW.Big5
		zh_TW.UTF-8
		C
		POSIX
 */

$locales = array(
	'cloud'	=> array(
		'english'	=> 'en_US.utf8',
		'spanish'	=> 'es_ES',
		'german'	=> 'de_DE',
		'japanese'	=> 'ja_JP.utf8'
	),
	'local'	=> array(
		'english'	=> 'en_US.UTF-8',
		'spanish'	=> 'es_ES.UTF-8',
		'german'	=> 'de_DE.UTF-8',
		'japanese'	=> 'ja_JP.UTF-8'
	)
);



if(isset($_SERVER['HTTP_HOST']))
	$host = explode('.', $_SERVER['HTTP_HOST']);
else
	$host[0] = 'www';

switch($host[0]){
	case 'www':
		$lang = 'english';
		break;
	case 'es':
		$lang = 'spanish';
		break;
	case 'de':
		$lang = 'german';
		break;
	case 'ja':
		$lang = 'japanese';
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


$config['current_lang'] 		= $lang;
$config['active_subdomain'] 	= $host[0];
$config['current_lang_code']	= (($host[0] == 'www') ? 'en' : $host[0]);
$config['current_lang_locale']	= $locales[((MODE == 'production' || MODE == 'staging') ? 'cloud' : 'local')][$lang];

/* End of file lang.php */
/* Location: ./application/config/lang.php */