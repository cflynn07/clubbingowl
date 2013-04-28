(function(exports) {
	var Globals = function(){};

	Globals.prototype.front_link_base 					= window.location.protocol + '//' + window.location.host + '/';
	Globals.prototype.front_assets 						= '<?= $central->front_assets ?>';
	Globals.prototype.admin_assets 						= '<?= $central->admin_assets ?>';
	Globals.prototype.global_assets						= '<?= $central->global_assets ?>';
	Globals.prototype.s3_uploaded_images_base_url		= '<?= $central->s3_uploaded_images_base_url ?>';
	Globals.prototype.pusher_api_key					= '<?= $this->config->item('pusher_api_key') ?>';
	
	var vc_user = jQuery.cookies.get('vc_user');
	if(vc_user && typeof vc_user.vc_oauth_uid !== 'undefined')
		Globals.prototype.user_oauth_uid					= vc_user.vc_oauth_uid;
	else 
		Globals.prototype.user_oauth_uid 					= false;
	
	
	
	Globals.prototype.fb_app_id = '<?= $this->config->item('facebook_app_id') ?>';
	
//	Globals.prototype.loading_indicator = '<img src="' + Globals.prototype.admin_assets + 'images/ajax.gif" alt="loading..." />';
	
	exports.module = exports.module || {};
	exports.module.Globals = Globals;
	
})(window);

jQuery(function(){
	if(!jQuery.cookies.test()){
	//	alert("Cookies must be enabled for ClubbingOwl to work!");
	//	return;
	}
});