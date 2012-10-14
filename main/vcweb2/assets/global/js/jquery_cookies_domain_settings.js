(function(jQuery){
	
	var tempDate = new Date();
	var opts = {
		domain: ((window.location.host.indexOf('clubbingowl.dev') != -1) ? '.clubbingowl.dev' : '.clubbingowl.com'),
		path: '/',
		expiresAt: new Date(tempDate.getTime() + 63113851900),
		secure: false
	};
	jQuery.cookies.setOptions(opts);
	
})(jQuery);
