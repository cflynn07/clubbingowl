// https://gist.github.com/854622 -- modified
(function(window,undefined){
	
	// Prepare our Variables
	var
		History = window.History,
		$ = window.jQuery,
		document = window.document;

	// Check to see if History.js is enabled for our Browser
	if ( !History.enabled ) {
		return false;
	}

	// Wait for Document
	$(function(){
		
		// Prepare Variables
		var
			$body = $(document.body),
			rootUrl = History.getRootUrl(),
			scrollOptions = {
				duration: 800,
				easing:'swing'
			};
		
		// Internal Helper
		$.expr[':'].internal = function(obj, index, meta, stack){
			// Prepare
			var
				$this = $(obj),
				url = $this.attr('href') || '',
				isInternalLink;

			// Check link
			isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;

			// Ignore or Keep
			return isInternalLink;
		};


		var ajax_page_fetch_helper = function(type, url){
			
			// Set Loading
//			$body.addClass('loading');
			
			jQuery('div#loading_modal').css({
				display: 'block',
				top: (Math.ceil(jQuery(window).height() / 2) - 100)
			});
			
			var current_url = window.location.href;
			var relativeUrl = url.replace(rootUrl,'');
			
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			// Ajax Request the Traditional Page
			$.ajax({
				url: url,
				type: 'post',
				dataType: 'html',
				data: {
					ci_csrf_token: cct,
					ajaxify: true
				},
				success: function(data, textStatus, jqXHR){
					
					console.log(window.module.Globals.prototype.unbind_callback);
					
					if(typeof window.module.Globals.prototype.unbind_callback == 'function'){
						window.module.Globals.prototype.unbind_callback();
						delete window.module.Globals.prototype.unbind_callback;
					}
						
						
						
					jQuery('div[role=main]').html(data);
					var title = jQuery('div[role=main] > div#ajaxify_page_title').html();
					History.pushState({
						type: type.type
					},title,url);


					//manually trigger route if same url
					if(current_url == url)
						Backbone.history.loadUrl();
					
					
					
					// Inform Google Analytics of the change
					if ( typeof window.pageTracker !== 'undefined' ) {
						window.pageTracker._trackPageview(relativeUrl);
					}

					// Inform ReInvigorate of a state change
					if ( typeof window.reinvigorate !== 'undefined' && typeof window.reinvigorate.ajax_track !== 'undefined' ) {
						reinvigorate.ajax_track(url);
						// ^ we use the full url here as that is what reinvigorate supports
					}
					
					jQuery('div#loading_modal').css({
						display: 'none'
					});
					
					if(type.type == 1){
						if(jQuery(window).scrollTop() > 0)			
							jQuery('html, body').animate({
							    scrollTop: 0
							}, 500);
					}else if(type.type == 2){
						//do nothing
						
					}else if(type.type == 3){
						jQuery('html, body').scrollTop(0);
						setTimeout(function(){
							jQuery('html, body').animate({
							    scrollTop: (jQuery('.ajaxify_t2:first').offset().top - jQuery('.ajaxify_t2:first').parent().height() - 10)
							}, 750);
						}, 150);
						
					}
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					document.location.href = url;
					return false;
				}
			}); // end ajax
		};


		// Ajaxify Helper
		$.fn.ajaxify = function(){
			// Prepare
			var $this = $(this);
			
			// Ajaxify
			$this.find('a:internal:not(.no-ajaxy):not(div.fb_dialog *)').live('click', function(event){
				// Prepare
				var
					$this = $(this),
					url = $this.attr('href');				
				
				// Continue as normal for cmd clicks etc
				if ( event.which == 2 || event.metaKey ) { return true; }
				
				var type = 1;
				if(jQuery(this).hasClass('ajaxify_t2')){
					type = 2;
				}else if(jQuery(this).hasClass('ajaxify_t3')){
					type = 3;
				}
				
				var type_o = {
					type: type
				};
				
				ajax_page_fetch_helper(type_o, url);
				
				event.preventDefault();
				return false;
			});
			
			
			// Chain
			return $this;
		};
		
		// Ajaxify our Internal Links
		$body.ajaxify();
		
		// Hook into State Changes
		$(window).bind('statechange',function(){
						
			console.log('statechange');
						
			// Prepare Variables
			var
				State = History.getState(),
				url = State.url;
					
			ajax_page_fetch_helper(State.data, url);
			
		}); // end onStateChange
		
		
			

		
		
	}); // end onDomLoad

})(window); // end closure