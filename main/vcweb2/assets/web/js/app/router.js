jQuery(function(){
	
	
	/**
	 * Loads global-callbacks, then item-specific callback
	 * 
	 * pass 1 argument with type string and value 'presence' to prevent disconnecting an established presence channel
	 */
	var callback_helper = function(callback){
		
		if(window.vc_page_error === true){
			delete window.vc_page_error;
			return;
		}
		
		
		var kill_presence_channel = true;
		
		if(typeof callback === 'function')
			callback();
		
		for(var i in arguments)				
			if(arguments[i] === 'presence')
				kill_presence_channel = false;
		
		if(kill_presence_channel)
			if(typeof window.kill_presence_channel === 'function'){
				window.kill_presence_channel();
				window.kill_presence_channel = false;
			}
			
			
			
			
		window.setTimeout(function(){
			jQuery(window).trigger('resize');
			
			
			var window_height 	= jQuery(window).height();
			var content_height 	= jQuery('#footer').height() + jQuery('#footer').offset().top;
			
			var difference 		= window_height - content_height;
			difference -= 25;
			console.log(difference);
			
			if(difference > 20){
				
				jQuery('div[role=main]').css('padding-top', 	Math.floor(difference / 2) + 'px');
				jQuery('div[role=main]').css('padding-bottom', 	Math.floor(difference / 2) + 'px');
				
			}
			
		}, 30);
		
		
		window.setTimeout(function(){
			jQuery(window).trigger('resize');
		}, 500);
		
		
		
	}
	
	var Router = Backbone.Router.extend({

		//---------------------------------------------------------------------------
		//	Defined Routes
		//---------------------------------------------------------------------------
		routes: {
			
			
			//promoters callbacks
			'promoters/cities/:city*splat': 										'promoters_city',
			'promoters/cities*splat': 												'promoters',
			
			'promoters/:public_identifier/events/:event*splat': 					'promoters_specific_event',
			'promoters/:public_identifier/events*splat': 							'promoters_all_events',
			'promoters/:public_identifier/guest_lists/:guest_list*splat': 			'promoters_specific_guest_list',
			'promoters/:public_identifier/guest_lists*splat': 						'promoters_all_guest_lists',
			'promoters/:public_identifier*splat': 									'promoters_profile',

			
			

			//venues-global page callbacks
			'venues/:city/:venue_name/events/:event*splat': 						'venues_specific_event',
			'venues/:city/:venue_name/events*splat': 								'venues_all_events',
			'venues/:city/:venue_name/guest_lists/:guest_list*splat': 				'venues_specific_guest_list',
			'venues/:city/:venue_name/guest_lists*splat': 							'venues_all_guest_lists',
			'venues/:city/:venue_name*splat': 										'venues_profile',
			'venues/:city*splat':													'venues_city',
			'venues*splat': 														'venues',
			
			
			
			//friends callbacks
			'friends/:third_party_id*splat': 										'friends_individual',
			'friends*splat': 														'friends',
			
			//profile callback
			'profile*splat': 														'profile',
			
			//corp callbacks
			'corp*splat': 															'team',
			'corp/tos*splat': 														'tos',
			
			//requests callback
			'requests*splat': 														'requests',
			
			//plugin callback
			'plugin*splat':															'plugin',
			'facebook*splat':														'plugin',
			
			//home page callback
			'*splat': 																'home'
			
		},
		//---------------------------------------------------------------------------
		
					
		//---------------------------------------------------------------------------
		//	Route Callbacks
		//---------------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		
		// --------------------------------------------------------------------------------------------------
		
		
		promoters_specific_event: function(city, public_identifier, event){
			
			console.log('--------- promoters specific event ---------');
			console.log(city);
			console.log(public_identifier);
			console.log(event);
			
			window.vc_page_scripts.promoter_all();
			
			callback_helper(window.vc_page_scripts.promoter_pusher_presence_channels, 'presence');
			
		},
		
		promoters_all_events: function(city, public_identifier){
			
			console.log('--------- promoters all events ---------');
			console.log(city);
			console.log(public_identifier);
			
			window.vc_page_scripts.promoter_all();
			
			callback_helper(window.vc_page_scripts.promoter_pusher_presence_channels, 'presence');
			
		},
				
		promoters_specific_guest_list: function(city, public_identifier, guest_list){
			
			console.log('--------- promoters specific guest list ---------');
			console.log(city);
			console.log(public_identifier);
			console.log(guest_list);
			
			window.vc_page_scripts.promoter_all();
			
			callback_helper(window.vc_page_scripts.promoter_guest_list_individual, window.vc_page_scripts.promoter_pusher_presence_channels, 'presence');
			
		},
				
		promoters_all_guest_lists: function(city, public_identifier){
			
			console.log('--------- promoters all guest lists ---------');
			console.log(city);
			console.log(public_identifier);
			
			window.vc_page_scripts.promoter_all();
			
			callback_helper(window.vc_page_scripts.promoter_pusher_presence_channels, 'presence');
			
		},		
	
		promoters_profile: function(city, public_identifier){
			
			console.log('--------- promoters profile ---------');
			console.log(city);
			console.log(public_identifier);
			
			window.vc_page_scripts.promoter_all();
			
			callback_helper(window.vc_page_scripts.promoter_profile, window.vc_page_scripts.promoter_pusher_presence_channels, 'presence');
			
		},
		
		promoters_city: function(city){
			
			console.log('--------- promoters city ---------');
			console.log(city);
			callback_helper(window.vc_page_scripts.promoters_cities);
			
		},
		
		promoters: function(){
			
			console.log('--------- promoters ---------');
			callback_helper(window.vc_page_scripts.promoters_cities);
			
		},
		
		
		// --------------------------------------------------------------------------------------------------
		
		
		venues_specific_event: function(city, venue_name, event){
			
			console.log('--------- venues specific event ---------');
			console.log(city);
			console.log(venue_name);
			console.log(event);
			callback_helper();
			
		},
		
		venues_all_events: function(city, venue_name){
			
			console.log('--------- venues all events ---------');
			console.log(city);
			console.log(venue_name);
			callback_helper();
			
		},
				
		venues_specific_guest_list: function(city, venue_name, guest_list){
			
			console.log('--------- venues specific guest list ---------');
			console.log(city);
			console.log(venue_name);
			console.log(guest_list);
			
			callback_helper(window.vc_page_scripts.venue_guest_list_individual);
			
		},
				
		venues_all_guest_lists: function(city, venue_name){
			
			console.log('--------- venues all guest lists ---------');
			console.log(city);
			console.log(venue_name);
			callback_helper();
			
		},		
				
		venues_profile: function(city, venue_name){
			
			console.log('--------- venues profile ---------');
			console.log(city);
			console.log(venue_name);
			callback_helper(window.vc_page_scripts.venue_profile);
			
		},
		
		venues_city: function(city){
			
			console.log('--------- venues city ---------');
			console.log(city);
			callback_helper(window.vc_page_scripts.venues_home);
			
		},
		
		venues: function(){
			
			console.log('--------- venues ---------');
			callback_helper(window.vc_page_scripts.venues_home);
			
		},
		
		
		// --------------------------------------------------------------------------------------------------
		
		
		friends_individual: function(third_party_id){
			
			console.log('--------- friends_individual ---------');
			console.log(third_party_id);
			callback_helper(window.vc_page_scripts.individual_friend);
			
		},
		
		friends: function(){
			
			console.log('--------- friends ---------');
			callback_helper(window.vc_page_scripts.friends_feed);
			
		},
		
		// --------------------------------------------------------------------------------------------------
		
		profile: function(){
			
			console.log('--------- profile ---------');
			callback_helper(window.vc_page_scripts.profile);
			
		},
		
		// --------------------------------------------------------------------------------------------------
		
		team: function(){
			console.log('--------- team ---------');
			callback_helper(window.vc_page_scripts.team_page);
		},
		
		tos: function(){
			console.log('--------- tos ---------');
			callback_helper();
		},
		
		// --------------------------------------------------------------------------------------------------
		
		requests: function(){
			
			console.log('--------- requests ---------');
			callback_helper(window.vc_page_scripts.app_requests);
			
		},
		
		// --------------------------------------------------------------------------------------------------

		plugin: function(){
			
			console.log('--------- facebook plugin ---------');
			callback_helper(window.vc_page_scripts.facebook_plugin_init);
			
		},

		// --------------------------------------------------------------------------------------------------
		
		home: function(segment){
					
			console.log('--------- home ---------');
			callback_helper(window.vc_page_scripts.home_news_feed);
			
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
	
	
	var get_friends = function(){
		console.log('get_friends');
		
		fbEnsureInit(function(){
		
			vc_user = jQuery.cookies.get('vc_user');
			
			if(vc_user)			
				setTimeout(function(){
					var token 	= FB.getAccessToken();						
					var fql 	= "SELECT uid, name, pic, pic_square, is_app_user, third_party_id FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) ORDER BY is_app_user DESC";
					
					FB.api({
					    method: 'fql.query',
						access_token : token,
					    query: fql
					}, function(data) {
						
					    window.all_vc_friends = data;
					    
					});
				}, 1000);
			
		});
	}
	
	get_friends();
	
	var vc_login_callback = function(){
				
		get_friends();	
			
	};
	window.EventHandlerObject.addListener("vc_login", vc_login_callback);
	
	
	
});