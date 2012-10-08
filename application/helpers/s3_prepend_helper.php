<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('s3_prepend'))
{
	/*
	 * helper function used to prepend the amazon s3 url in front of static assets file names
	 * 
	 * @param	array [header_javascripts]
	 * @param	array [header_css]
	 * @return	null 
	 * */
	function s3_prepend(&$header_javascripts, &$header_css, $asset_group)
	{
        // the helper function doesn't have access to $this, so we need to get a reference to the 
        // CodeIgniter instance.  We'll store that reference as $CI and use it instead of $this
        $CI =& get_instance();
        
        /* ----------------------------- amazon s3 url ------------------------------ */
		//prep page-specific js files by appending url to front
		if($header_javascripts)
		foreach($header_javascripts as &$val){
			$val = $CI->config->item($asset_group) . 'js/' . $val;
			
			//Also append cache-control segment to url
			if($asset_group == 'global_assets'){
				$val .= '?' . $CI->config->item('cache_global_js');
			}elseif($asset_group == 'karma_assets'){
				$val .= '?' . $CI->config->item('cache_karma_js');
			}elseif($asset_group == 'admin_assets'){
				$val .= '?' . $CI->config->item('cache_admin_js');
			}
		}
			
		
		//prep page-specific css files by appending url to front
		if($header_css)
		foreach($header_css as &$val){
			$val = $CI->config->item($asset_group) . 'css/' . $val;
			
			//Also append cache-control segment to url
			if($asset_group == 'global_assets'){
				$val .= '?' . $CI->config->item('cache_global_css');
			}elseif($asset_group == 'karma_assets'){
				$val .= '?' . $CI->config->item('cache_karma_css');
			}elseif($asset_group == 'admin_assets'){
				$val .= '?' . $CI->config->item('cache_admin_css');
			}
		}
		/* ----------------------------- amazon s3 url ------------------------------- */
	}
}

/* End of file s3_prepend_helper.php */
/* Location: ./application/helpers/s3_prepend_helper.php */