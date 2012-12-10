// https://gist.github.com/854622 -- modified
(function(window,undefined){
	
	window.promoter_admin_menu_set_active = function(li_item){
		var menu = jQuery('div#primary_left div#menu ul');
		menu.find('li.current').removeClass('current');
		menu.find('li.li_' + li_item).addClass('current');
	}
				
	// Prepare our Variables
	var
		History 	= window.History,
		$ 			= window.jQuery,
		document 	= window.document;

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
		
		
		
		
		
		var ajax_page_fetch_helper = function(url){
			
			// Set Loading
			
			jQuery('div#loading_modal').css({
				display: 'block',
				top: (Math.ceil(jQuery(window).height() / 2) - 100)
			});
			jQuery('div#primary_right').css({
				opacity: 0.4
			});
			
			var current_url = window.location.href.split('#')[0];
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
						
					jQuery('#easyTooltip').remove();	
					jQuery('div#primary_right').css({
						opacity: 1
					});
					jQuery('div#primary_right > div.inner').html(data);
					
					var title = 'ClubbingOwl';									
					History.pushState({},title,url);


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
					
					
					jQuery(window).scrollTop(0);
					
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					document.location.href = url;
					return false;
				}
			}); // end ajax
		};


		// Ajaxify Helper
		var initiate_ajaxify = function(){
			
			jQuery('a.ajaxify').live('click', function(event){
				
				// Prepare
				var
					$this = $(this),
					url = $this.attr('href');				
				
				// Continue as normal for cmd clicks etc
				if ( event.which == 2 || event.metaKey ) { return true; }
				
				ajax_page_fetch_helper(url);
				
				event.preventDefault();
				return false;
				
			});
			
			// Prepare
			var $this = $(this);
			
			// Chain
			return $this;
		};
		
		// Ajaxify our Internal Links
		initiate_ajaxify();
			
		
		
		// Hook into State Changes
		$(window).bind('statechange',function(){
												
			// Prepare Variables
			var
				State = History.getState(),
				url = State.url;
					
			ajax_page_fetch_helper(url);
			
		}); // end onStateChange
		
		
		
				
		
	}); // end onDomLoad

})(window); // end closure