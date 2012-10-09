<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Piwik Helper
 *
 * Helper for echoing piwik tracking tag based on what
 * is defined in config/piwik.php. Load helper in the controller
 * or autoload and call piwik_tag() before closing body tag.
 *
 * @package       CodeIgniter
 * @subpackage    Helpers
 * @category      Helpers
 * @author        Bryce Johnston bryce@wingdspur.com
 */

function piwik_tag($additional_sites_ids = array())
{
    $CI =& get_instance();
    $CI->load->config('piwik');
    
    $piwik_url = $CI->config->item('piwik_url');
    $piwik_url_ssl = $CI->config->item('piwik_url_ssl');
    $site_id = $CI->config->item('site_id');
    $tag_on = $CI->config->item('tag_on');
       
    if($tag_on)
    {
        $tag = '<script type="text/javascript">
        var pkBaseURL = (("https:" == document.location.protocol) ? "'.$piwik_url_ssl.'" : "'.$piwik_url.'");
        document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
        </script><script type="text/javascript">

        try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$site_id.');
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();' . PHP_EOL;
		
		foreach($additional_sites_ids as $as){
			if($as != -1 && $as != '-1')
				$tag .= 'var piwikTracker' . $as . ' = Piwik.getTracker(pkBaseURL + "piwik.php", ' . $as . ');
				        piwikTracker' . $as . '.trackPageView();
				        piwikTracker' . $as . '.enableLinkTracking();' . PHP_EOL;
		}
		
		$tag .= '} catch( err ) {}
        </script>
        <noscript>
        	<p><img src="'  . ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? $piwik_url_ssl : $piwik_url) . 'piwik.php?idsite=' . $site_id . '" style="border:0" alt="" /></p>';
        	
        	foreach($additional_sites_ids as $as){
				$tag .= '<p><img src="'  . ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? $piwik_url_ssl : $piwik_url) . 'piwik.php?idsite=' . $as . '" style="border:0" alt="" /></p>';
			}
			
        $tag .= '</noscript>';
        
		
        return stripslashes($tag);
    }
}