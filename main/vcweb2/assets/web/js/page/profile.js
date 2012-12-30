if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.profile = function(){
				
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
		
		
		
		jQuery('section#settings input[type=checkbox]').iphoneStyle().bind('change', function(e){
			
			var checked = jQuery(this).is(':checked');
			jQuery.background_ajax({
				data: {
					vc_method: 		'update_settings',
					opt_out_email: 	checked
				},
				success: function(data){
					
				}
			});
			
			
		});
		
		
		
		
		
		
		
		
		page_reload_function = function(){
			window.location.reload(true);
		};
		window.EventHandlerObject.addListener("vc_login", page_reload_function);
		window.EventHandlerObject.addListener("vc_logout", page_reload_function);
	
		custom_events_unbind.push([
			'vc_logout',
			window.EventHandlerObject,
			page_reload_function
		]);
		custom_events_unbind.push([
			'vc_login',
			window.EventHandlerObject,
			page_reload_function
		]);
	
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			
			for(var i in unload_items){
				unload_items[i][1].unbind(unload_items[i][0], unload_items[i][2]);
			}
			
			for(var i in timeout_cancels){
				clearTimeout(timeout_cancels[i]);
			}
			
			for(var i in custom_events_unbind){
				custom_events_unbind[i][1].removeListener(custom_events_unbind[i][0], custom_events_unbind[i][2]);
			}
			
			for(var i in facebook_callbacks){
				facebook_callbacks[i] = function(){};
			}
			
		}
		
	
	
	
	}
	
});