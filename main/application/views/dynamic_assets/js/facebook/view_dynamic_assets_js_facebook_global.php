(function(exports) {
	var Globals = function(){};

	Globals.prototype.front_link_base 					= window.location.protocol + '//' + window.location.host + '/';
	Globals.prototype.front_assets 						= '<?= $central->front_assets ?>';
	Globals.prototype.admin_assets 						= '<?= $central->admin_assets ?>';
	Globals.prototype.global_assets						= '<?= $central->global_assets ?>';
	Globals.prototype.s3_uploaded_images_base_url		= '<?= $central->s3_uploaded_images_base_url ?>';
	Globals.prototype.facebook_link_base 				= '<?= $central->facebook_link_base ?>';
	Globals.prototype.facebook_assets	 				= '<?= $central->facebook_assets ?>';
	
	exports.module = exports.module || {};
	exports.module.Globals = Globals;
	
})(window);

jQuery(function(){
	if(!jQuery.cookies.test()){
		alert("Cookies must be enabled for VibeCompass to work!");
		return;
	}
});