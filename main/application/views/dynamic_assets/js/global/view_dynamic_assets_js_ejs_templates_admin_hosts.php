<?php

	/**
	 * Load all the EJS templates, then create a JSON object and attach to the Globals object
	 * 
	 * NOTE: Templates are created for each supported language in config, all lang files are loaded in 'assets' controller
	 * 
	 */
	$ejs_view_templates = new stdClass;
	foreach($ejs_templates as $ejst){
		$ejs_view_templates->$ejst = $this->load->view('dynamic_assets/ejs/admin_hosts/' . $ejst, array('lang_code' => $lang), true);
	}
	$ejs_view_templates = json_encode($ejs_view_templates);
?>
(function(exports){
	exports.ejs_view_templates_admin_hosts = <?= $ejs_view_templates ?>;	
})(window);