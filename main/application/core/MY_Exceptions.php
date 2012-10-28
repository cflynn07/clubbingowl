<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions{
	
	/**
	 * General Error Page
	 *
	 * This function takes an error message as input
	 * (either as a string or an array) and displays
	 * it using the specified template.
	 *
	 * @access	private
	 * @param	string	the heading
	 * @param	string	the message
	 * @param	string	the template name
	 * @return	string
	 */
	function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		
	//	die('<script type="text/javascript">window.vc_page_error=true;</script>');
		
		$CI = @get_instance();
		if($CI !== false){	
			
			$output = '';
			$script = '<script>window.vc_page_error=true;</script>';
			if($CI->input->post('ajaxify')){
				//ajaxify request, don't reload header/footer
				
				$output .= $script;
				$output .= $CI->load->view('front/_common/view_front_invite', 	'', true);
				$output .= $CI->load->view('front/error/view_front_error', 		array('message' => $message), true);
				
			}else{
				
				set_status_header($status_code);
				
				//load header/footer
				$output .= $CI->load->view('front/view_front_header', 			'', true);
				$output .= $script;
				$output .= $CI->load->view('front/_common/view_front_invite', 	'', true);
				$output .= $CI->load->view('front/error/view_front_error', 		array('message' => $message), true);
				$output .= $CI->load->view('front/view_front_footer', 			'', true);
				
			}
			
			@ob_clean();		
			return $output;
		}
		//-----------------------------
		
		
		
		
		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
	
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include(APPPATH.'errors/'.$template.EXT);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
		
	}
	
}
