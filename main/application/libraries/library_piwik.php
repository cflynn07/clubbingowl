<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Piwik Class
 *
 * Library for retrieving stats from Piwik Open Source Analytics API
 * with geoip capabilities using the free MaxMind GeoLiteCity database
 *
 * @package       CodeIgniter
 * @subpackage    Libraries
 * @category      Libraries
 * @author        Bryce Johnston < bryce@wingdspur.com >
 * @license       MIT
 */

class Library_piwik
{
    private $_ci;
    private $geoip_on = FALSE;
    private $piwik_url = '';
    public 	$site_id = '';
    private $token = '';
    private $gi;

    function __construct()
    {
        $this->_ci =& get_instance();
        $this->_ci->load->config('piwik');
        
        $this->piwik_url = $this->_ci->config->item('piwik_url');
        $this->site_id = $this->_ci->config->item('site_id');
        $this->token = $this->_ci->config->item('token');
        $this->geoip_on = $this->_ci->config->item('geoip_on');
        
		$this->geoip_on = false;
		
        if($this->geoip_on)
        {
            $this->_ci->load->helper('geoip');
            $this->_ci->load->helper('geoipcity');
            $this->_ci->load->helper('geoipregionvars');
        }
    }
	
	/**
	 * API Call get decoded
	 * Generic API call
	 * 
	 * @param 	API query string
	 * @param	array (result)
	 */
	public function api_call($api_query){
		$url = $this->piwik_url.'/index.php?module=API' . $api_query . '&format=JSON&token_auth='.$this->token;		
		return $this->_get_decoded($url);
	}
	
	/**
	 * Added by Casey on 8.24.2011
	 * 
	 * Piwik integration into VibeCompass requires 
	 * multiple site_id's throughout the app. This
	 * method provides ability to switch between site id's
	 * 
	 * @param
	 * @param 	site_id (if blank, default site id assigned)
	 * @return	null
	 * */
	public function set_site_id($site_id = false){
		if($site_id)
			$this->site_id = $site_id;
		else
			$this->site_id = $this->_ci->config->item('site_id');
	}
    
    /**
     * actions
     * Get actions (hits) for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function actions($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getActions&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }
    
    /**
     * keywords
     * Get search keywords for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function keywords($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Referers.getKeywords&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }
    
    /**
     * last_visits
     * Get information about last 10 visits (ip, time, country, pages, etc.)
     *
     * @access  public
     * @param   int     $cnt      Limit the number of visits returned by $cnt
     * @return  array
     */
    public function last_visits($cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisitsDetails&idSite='.$this->site_id.'&period=day&date=today&filter_limit='.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }
    
    /**
     * last_visits_parsed
     * Get information about last 10 visits (ip, time, country, pages, etc.) in a formatted array with GeoIP information if enabled
     *
     * @access  public
     * @param   int     $cnt      Limit the number of visits returned by $cnt
     * @return  array
     */
    public function last_visits_parsed($cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisitsDetails&idSite='.$this->site_id.'&period=day&date=today&filter_limit='.$cnt.'&format=JSON&token_auth='.$this->token;
        $visits = $this->_get_decoded($url);
        
        $data = array();
        ($this->geoip_on ? $this->_geoip_open() : 0);
        foreach($visits as $v)
        {
            // Get the last array element which has information of the last page the visitor accessed
            $cnt = count($v['actionDetails']) - 1; 
            $page_link = $v['actionDetails'][$cnt]['url'];
            $page_title = $v['actionDetails'][$cnt]['pageTitle'];
            
            // Get just the image names (API returns path to icons in piwik install)
            $flag = explode('/', $v['countryFlag']);
            $flag_icon = end($flag);
            
            $os = explode('/', $v['operatingSystemIcon']);
            $os_icon = end($os);
            
            $browser = explode('/', $v['browserIcon']);
            $browser_icon = end($browser);
            
            // Get GeoIP information if enabled
            $city = "";
            $region = "";
            $country = "";
            if($this->geoip_on)
            {
                $geoip = $this->get_geoip($v['visitIp'], TRUE);
                if(!empty($geoip))
                {
                    $city = $geoip['city'];
                    $region = $geoip['region'];
                    $country = $geoip['country'];
                }
                
            }
            
            $data[] = array(
              'time' => date("M j, g:i a", $v['lastActionTimestamp']),
              'title' => $page_title,
              'link' => $page_link,
              'ip_address' => $v['visitIp'],
              'provider' => $v['provider'],
              'country' => $v['country'],
              'country_icon' => $flag_icon,
              'os' => $v['operatingSystem'],
              'os_icon' => $os_icon,
              'browser' => $v['browserName'],
              'browser_icon' => $browser_icon,
              'geo_city' => $city,
              'geo_region' => $region,
              'geo_country' => $country
            );
        }
        ($this->geoip_on ? $this->_geoip_close() : 0);
        return $data;
    }
    
    /**
     * page_titles
     * Get page visit information for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function page_titles($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Actions.getPageTitles&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    /**
     * unique_visitors
     * Get unique visitors for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function unique_visitors($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getUniqueVisitors&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    /**
     * visits
     * Get all visits for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'week', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function visits($period = 'day', $cnt = 10)
   	{	
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getVisits&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    /**
     * websites
     * Get refering websites (traffic sources) for the specific time period
     *
     * @access  public
     * @param   string  $period   Time interval ('day', 'month', or 'year')
     * @param   int     $cnt      Gets the number of $period from the current period to what $cnt is set to (i.e. last 10 days by default) 
     * @return  array
     */
    public function websites($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Referers.getWebsites&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    /**
     * _get_decoded
     * Gets and returns json_decoded array from the URL passed
     *
     * @access  private
     * @param   string  $url   URL to Piwik API method returning JSON
     * @return  array
     */
    private function _get_decoded($url)
    {
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
  
    // ---- GeoIP functions ---------------------------------------------------------------------- //
    
    /**
     * get_geoip
     * Uses GeoLiteCity.dat and geoip helpers to get GeoIP information for the IP address passed
     *
     * @access  public
     * @param   string    $ip_address     IP Address
     * @param   boolean   $conn           TRUE or FALSE - whether a connection to GeoLiteCity is already open or not
     * @return  array
     */
    public function get_geoip($ip_address, $conn = FALSE)
    {
        if($this->geoip_on)
        {
            ($conn == FALSE ? $this->_geoip_open() : 0);
            $geoip = array();
            $record = geoip_record_by_addr($this->gi, $ip_address);
            if(!empty($record))
            {
                $geoip['city'] = $record->city;
                $geoip['region'] = $record->region;
                $geoip['country'] = $record->country_code3;
            }
            ($conn == FALSE ? $this->_geoip_close() : 0);
            return $geoip;
        }
        else
        {
            show_error('You must enable GeoIP in the piwik config file to use get_geoip.');
        }
    }
    
    /**
     * _geoip_open
     * Opens connection to GeoLiteCity.dat
     *
     * @access  private
     * @return  void
     */
    private function _geoip_open()
    {
        $this->gi = geoip_open(APPPATH.'helpers/geoip/GeoLiteCity.dat', GEOIP_STANDARD);
    }
    
    /**
     * _geoip_close
     * Closes connection to GeoLiteCity.dat
     *
     * @access  private
     * @return  void
     */
    private function _geoip_close()
    {
        geoip_close($this->gi);
    }
    
}
// END Piwik Class

/* End of file library_piwik.php */
/* Location: ./application/libraries/library_piwik.php */
