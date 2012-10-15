/**
 * Extend JQuery with helpful helpers
 * 
 * 
 * @param {Object} jQuery
 */
(function(jQuery){
	
	/**
	 * Globally attach ci_csrf token to all ajax requests
	 * 
	 */
	(function($){
		var _ajax = $.ajax,
		A = $.ajax = function(options) {
		if (A.data)
	        if(options.data) {
	            if(typeof options.data !== 'string')
	                options.data = $.param(options.data);
	            if(typeof A.data !== 'string')
	                A.data = $.param(A.data);
	            options.data += '&' + A.data;
	        } else
	            options.data = A.data;
		return _ajax(options);
		};
		
		jQuery.ajax.data = {
			ci_csrf_token: jQuery.cookies.get('ci_csrf_token')
		};
		
	})(jQuery);

		
	var jQueryExtensions = {
		
		background_ajax: function(opts){
			if(!opts.url)
				opts.url = window.location.href;
															
			jQuery.ajax({
				url:		opts.url,
				type: 		'post',
				data: 		opts.data,
				cache: 		false,
				dataType: 	'json',
				success: function(data, textStatus, jqXHR){
					
					opts.success(data, textStatus, jqXHR);
											
				}
			});	
		}
		
	};
	jQuery.extend(jQueryExtensions);
	
})(jQuery);
