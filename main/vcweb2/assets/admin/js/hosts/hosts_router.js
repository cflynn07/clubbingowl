jQuery(function(){
		
	var callback_helper = function(callback){
		if(typeof callback === 'function')
			callback();
		
	//	window.module.Globals.prototype.global_views.admin_wrapper.ajaxify_change();
		
	}
	
	var routes = {
		
		'/': 															'dashboard'
			
	}
	
	var routes_prefix 	= 'admin/hosts';
	var routes_suffix 	= '*splat';
	var routes_prod 	= {};
	for(var i in routes){
		routes_prod[routes_prefix + i + routes_suffix] = routes[i];
	}
	
	var Router = Backbone.Router.extend({

		//---------------------------------------------------------------------------
		//	Defined Routes
		//---------------------------------------------------------------------------
		
		routes: 	routes_prod,
		
		//---------------------------------------------------------------------------
		
					
		//---------------------------------------------------------------------------
		//	Route Callbacks
		//---------------------------------------------------------------------------
	
		dashboard: 	function(segment){
						
			var segments = segment.split('/');			
			
			jQuery('li[data-date]').removeClass('current');
			jQuery('li[data-date="' + segments[0] + '"]').addClass('current');
			
			console.log('--------- host dashboard ---------');
			callback_helper(window.vc_page_scripts.admin_host_dashboard);
			
		}
		//---------------------------------------------------------------------------

	});
	
	//Create instance of router
	window.routerInstance = new Router();
	
	//Begin route monitoring
	Backbone.history.start({
		root: '/',
		pushState: true,
		hashChange: false
	});
	
	//speed shit up
//	jQuery.NoClickDelay(jQuery('#primary_right > .inner:first').get(0));
	
});