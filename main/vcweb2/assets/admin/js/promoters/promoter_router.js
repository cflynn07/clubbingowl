jQuery(function(){
		
	var callback_helper = function(callback){
		if(typeof callback === 'function')
			callback();		
	}
	
	
	var routes = {
		
		'/support': 													'support',
		
		'/manage_image': 												'manage_image',
		'/manage_guest_lists_new': 										'manage_guest_lists_new',
		'/manage_guest_lists_edit': 									'manage_guest_lists_edit',
		'/manage_guest_lists': 											'manage_guest_lists',
		'/my_profile_img': 												'my_profile_img',
		'/my_profile': 													'my_profile',
	
		'/reports_clients': 											'reports_clients',
		'/reports_sales': 												'reports_sales',
		'/reports_guest_lists': 										'reports_guest_lists',
		
		'/clients': 													'clients',
		
		'/tables':														'tables',
		
		'/guest_lists':													'guest_lists',
		
		'': 															'dashboard'
			
	}
	
	var routes_prefix = 'admin/promoters';
	var routes_suffix = '*splat';
	var routes_prod = {};
	for(var i in routes){
		routes_prod[routes_prefix + i + routes_suffix] = routes[i];
	}
	
		
	var Router = Backbone.Router.extend({

		//---------------------------------------------------------------------------
		//	Defined Routes
		//---------------------------------------------------------------------------
		
		routes: routes_prod,
		
		//---------------------------------------------------------------------------
		
					
		//---------------------------------------------------------------------------
		//	Route Callbacks
		//---------------------------------------------------------------------------
		
		
		
		support: function(){
			
			window.promoter_admin_menu_set_active('support');
			console.log('--------- promoter support  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		manage_image: function(){
			
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_manage_image);
			console.log('--------- promoter manage_image  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		manage_guest_lists_new: function(){
			
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_manage_guest_lists_new);
			console.log('--------- promoter manage_guest_lists_new  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		manage_guest_lists_edit: function(){
			
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_manage_guest_lists_edit);
			console.log('--------- promoter manage_guest_lists_edit  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		manage_guest_lists: function(){
			
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_manage_guest_lists);
			console.log('--------- promoter manage_guest_lists  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		my_profile_img: function(){
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_my_profile_img);
			console.log('--------- promoter my_profile_img  ---------');
		},
		
		// --------------------------------------------------------------------------
		
		my_profile: function(){
			
			window.promoter_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_promoter_my_profile);
			console.log('--------- promoter my_profile  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		reports_clients: function(){
			
			window.promoter_admin_menu_set_active('reports');
			console.log('--------- promoter reports_clients  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		reports_sales: function(){
			
			window.promoter_admin_menu_set_active('reports');
			console.log('--------- promoter reports_sales  ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		reports_guest_lists: function(){
			
			window.promoter_admin_menu_set_active('reports');
			console.log('--------- promoter reports_guest_lists ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		clients: function(){
			
			window.promoter_admin_menu_set_active('clients');
			callback_helper(window.vc_page_scripts.admin_promoter_clients);
			console.log('--------- promoter clients ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		tables: function(){
			
			window.promoter_admin_menu_set_active('tables');
			callback_helper(window.vc_page_scripts.admin_promoter_tables);
			console.log('--------- promoter tables ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		guest_lists: function(){
			
			window.promoter_admin_menu_set_active('guest_lists');
			callback_helper(window.vc_page_scripts.admin_promoter_guest_list);
			console.log('--------- promoter guest_lists ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		dashboard: function(segment){
			
			window.promoter_admin_menu_set_active('dashboard');
			console.log('--------- promoter dashboard ---------');
			callback_helper(window.vc_page_scripts.admin_promoter_dashboard);
			
		}
		//---------------------------------------------------------------------------

	});
	
	//Create instance of router
	window.routerInstance = new Router();
	
	//Begin route monitoring
	Backbone.history.start({
		pushState: true,
		hashChange: false
	});
	
});