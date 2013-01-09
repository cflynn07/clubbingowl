jQuery(function(){
		
	var callback_helper = function(callback){
//		window.vc_page_scripts.suite_re_init();
	
		if(window.module.Globals.prototype.unbind_callback && typeof window.module.Globals.prototype.unbind_callback === 'function')
			window.module.Globals.prototype.unbind_callback();
			
		if(typeof callback === 'function')
			callback();		
			
		window.module.Globals.prototype.global_views.admin_wrapper.ajaxify_change();
		
	}
	
	
	var routes = {
		
		'/support': 													'support',
		
		'/manage_image': 												'manage_image',
		
		'/settings_venues_edit_floorplan': 								'settings_venues_edit_floorplan',
		'/settings_venues_edit': 										'settings_venues_edit',
		'/settings_venues_new':											'settings_venues_new',
		'/settings_venues': 											'settings_venues',
		'/settings_hosts':												'settings_hosts',
		'/settings_promoters': 											'settings_promoters',
		'/settings_payment': 											'settings_payment',
		'/settings_guest_lists_new': 									'settings_guest_lists_new',
		'/settings_guest_lists_edit': 									'settings_guest_lists_edit',
		'/settings_guest_lists': 										'settings_guest_lists',
		'/settings_checkin_categories': 								'settings_checkin_categories',

		
		
		'/reports_clients':												'reports_clients',
		'/reports_sales':												'reports_sales',
		'/reports_guest_lists':											'reports_guest_lists',
		
		
		
		'/promoters_statistics':										'promoters_statistics',
		'/promoters_clients':											'promoters_clients',
		'/promoters_guest_lists':										'promoters_guest_lists',
		
		
		'/marketing_new': 												'marketing_new',
		'/marketing':													'marketing',	
		
		'/clients/:oauth_uid': 											'clients_individual',
		'/clients': 													'clients',
		
		'/tables':														'tables',
		
		'/guest_lists':													'guest_lists',
		
		'': 															'dashboard'
			
	}
	
	var routes_prefix = 'admin/managers';
	var routes_suffix = '*splat';
	var routes_prod = {};
	for(var i in routes){
		routes_prod[routes_prefix + i + routes_suffix] = routes[i];
	}
	
	
	console.log(routes_prod);
	
	var Router = Backbone.Router.extend({

		//---------------------------------------------------------------------------
		//	Defined Routes
		//---------------------------------------------------------------------------
		
		routes: routes_prod,
		
		//---------------------------------------------------------------------------
		
					
		//---------------------------------------------------------------------------
		//	Route Callbacks
		//---------------------------------------------------------------------------
			
		// --------------------------------------------------------------------------
				
		support: function(segment){
			
			window.manager_admin_menu_set_active('support');
			callback_helper(window.vc_page_scripts.admin_manager_support);
			console.log('--------- manager support ---------');
			
		},
		
		// --------------------------------------------------------------------------

		manage_image: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_manage_image);
			console.log('--------- manager manage_image ---------');
			
		},

		// --------------------------------------------------------------------------
				
		settings_venues_edit_floorplan: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_venues_edit_floorplan);
			console.log('--------- manager settings_venues_edit_floorplan ---------');
			
		},
		
		// --------------------------------------------------------------------------
				
		settings_venues_edit: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_venues_edit);
			console.log('--------- manager settings_venues_edit ---------');
			
		},
		
		// --------------------------------------------------------------------------
				
		settings_venues_new: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_venues_new);
			console.log('--------- manager settings_venues_new ---------');
			
		},
		
		// --------------------------------------------------------------------------
				
		settings_venues: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_venues);
			console.log('--------- manager settings_venues ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		settings_hosts: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_hosts);
			console.log('--------- manager settings_hosts ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		settings_promoters: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_promoters);
			console.log('--------- manager settings_promoters ---------');
			
		},
		
		// --------------------------------------------------------------------------
		settings_payment: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_payment);
			console.log('--------- manager settings_payment ---------');
			
		},
		// --------------------------------------------------------------------------

		settings_guest_lists: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_guest_lists);
			console.log('--------- manager settings_guest_lists ---------');
			
		},

		// --------------------------------------------------------------------------

		settings_guest_lists_edit: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_guest_lists_edit);
			console.log('--------- manager settings_guest_lists_edit ---------');
			
		},

		// --------------------------------------------------------------------------
		
		settings_guest_lists_new: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_guest_lists_new);
			console.log('--------- manager settings_guest_lists_new ---------');
			
		},
		
		// --------------------------------------------------------------------------

		settings_checkin_categories: function(segment){
			
			window.manager_admin_menu_set_active('settings');
			callback_helper(window.vc_page_scripts.admin_manager_settings_checkin_categories);
			console.log('--------- manager settings_checkin_categories ---------');
			
			
		},

		// --------------------------------------------------------------------------
		
		reports_clients: function(segment){
			
			window.manager_admin_menu_set_active('reports');
			console.log('--------- manager reports_clients ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		reports_sales: function(segment){
			
			window.manager_admin_menu_set_active('reports');
			console.log('--------- manager reports_sales ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		reports_guest_lists: function(segment){
			
			window.manager_admin_menu_set_active('reports');
			callback_helper(window.vc_page_scripts.admin_manager_reports_guest_lists);
			console.log('--------- manager reports_guest_lists ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		promoters_statistics: function(segment){
			
			window.manager_admin_menu_set_active('promoters');
			callback_helper(window.vc_page_scripts.admin_manager_promoters_statistics);
			console.log('--------- manager promoters_statistics ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		promoters_clients: function(segment){
			
			window.manager_admin_menu_set_active('promoters');
			callback_helper(window.vc_page_scripts.admin_manager_promoters_clients);
			console.log('--------- manager promoters_clients ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		promoters_guest_lists: function(segment){
			
			window.manager_admin_menu_set_active('promoters');
			callback_helper(window.vc_page_scripts.admin_manager_promoters_guest_lists);
			console.log('--------- manager promoters_guest_lists ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		marketing_new: function(segment){
			
			window.manager_admin_menu_set_active('marketing');
			callback_helper(window.vc_page_scripts.admin_manager_marketing_new);
			console.log('--------- manager marketing new ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		marketing: function(segment){
			
			window.manager_admin_menu_set_active('marketing');
			callback_helper(window.vc_page_scripts.admin_manager_marketing);
			console.log('--------- manager marketing ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		clients_individual: function(segment){
			
			window.manager_admin_menu_set_active('clients');
			callback_helper(window.vc_page_scripts.admin_manager_clients_individual);
			console.log('--------- manager clients individual ---------');
			
		},

		// --------------------------------------------------------------------------
		
		clients: function(segment){
			
			window.manager_admin_menu_set_active('clients');
			callback_helper(window.vc_page_scripts.admin_manager_clients);
			console.log('--------- manager clients ---------');
			
		},
		
		// --------------------------------------------------------------------------
		
		tables: function(segment){
			
			window.manager_admin_menu_set_active('tables');
			callback_helper(window.vc_page_scripts.admin_manager_tables);
			console.log('--------- manager tables ---------');
			
		},
				
		// --------------------------------------------------------------------------
		
		guest_lists: function(segment){
			
			window.manager_admin_menu_set_active('guest_lists');
			callback_helper(window.vc_page_scripts.admin_manager_guest_lists);
			console.log('--------- manager guest_lists ---------');
			
		},

		// --------------------------------------------------------------------------
		
		dashboard: function(segment){
			
			window.manager_admin_menu_set_active('dashboard');
			callback_helper(window.vc_page_scripts.admin_manager_dashboard);
			console.log('--------- manager dashboard ---------');
			
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