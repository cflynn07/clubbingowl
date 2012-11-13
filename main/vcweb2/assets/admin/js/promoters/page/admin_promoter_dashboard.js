if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
		
	window.vc_page_scripts.admin_promoter_dashboard = function(){
				
				
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items 			= [];
		var timeout_cancels 		= [];
		var custom_events_unbind 	= [];
		var facebook_callbacks 		= [];
		var pusher_unbind_event			= [];
		var pusher_disconnect_channel 	= [];
		
		
		
		
		
		
		
		
		//how ghetto....
		if(typeof window.page_obj.first_time_setup !== 'undefined')
			return; //this isn't the normal dashboard...






		var Models = {};
		var Collections = {};
		var Views = {};

		Views.TeamAnnouncements = {
			el: '#team_announcements',
			
			team_managers: [],
			
			initialize: function(){
				
				var team_managers = [];
				for(var i in window.page_obj.team_chat_members.managers){
					team_managers.push(window.page_obj.team_chat_members.managers[i].oauth_uid);
				}
				
				_this = this;
				if(team_managers.length > 0){
					jQuery.fbUserLookup(team_managers, '', function(rows){
						
						_this.team_managers = rows;
						_this.render();
																		
					});
				}else{
					_this.render();
				}
				
			},
			render: function(){
				
				for(var i in this.team_managers){
					this.$el.find('div.pic_square_' + this.team_managers[i].uid).html('<img src="' + this.team_managers[i].pic_square + '">');
				}
				
				jQuery('img#messages_loading_indicator').remove();
				this.$el.show();
				return this;
			},
			events: {
				
			}
		}; Views.TeamAnnouncements = Backbone.View.extend(Views.TeamAnnouncements);
		
		Views.Stats = {
			el: '#promoter_stats_tabs',
			initialize: function(){
					

				var poll_job = {
					data: {
						vc_method: 'stats_retrieve'
					},
					success: function(data){
						jQuery('img#loading_gif').remove();
						
						var categories = [];
						var visits = [];
						var unique_visitors = [];
						
						if(typeof data.message.visits.length == undefined)
							return; //error
						
						var count = 0;
						for(key in data.message.visits){
														
							if(!(count % 2))
								categories.push(key.toString().substring(5, 10).replace('-', '/'));
							else
								categories.push(' ');
							
							count++;
							
							visits.push(data.message.visits[key]);
							unique_visitors.push(data.message.unique_visitors[key]);
						}
						
						jQuery('div#tabs-1').css('display', 'block');
											
						visitors_chart = new Highcharts.Chart({
							chart: {
								renderTo: 'tabs-1',
								defaultSeriesType: 'area',
								width: 1048
							},
							tooltip:{
								enabled: false
							},
							margin: [0, 0, 0, 0],
							xAxis: {
								categories: categories,
								tickmarkPlacement: 'on'
							},
							title: {
								text: ' '
							},
							yAxis: {
								title: {
									text: null
								}
							},
							series: [{
								name: 'Visits',
								data: visits
							},{
								name: 'Unique Visitors',
								data: unique_visitors
							}]
						});
					},
					expire: function(){
						jQuery('div.promoter_stats_tabs img#loading_gif').remove();
						jQuery('div.promoter_stats_tabs div#tabs-1').html('<span style="color:red">We\'re sorry, something went wrong. We\'ll get it fixed as soon as possible, please try again in a few minutes.</span>').css('display', 'block');
					},
					scope: this
				};
							
				jQuery.poll_job(poll_job);				
				
				var trailing_requests_chart = new Highcharts.Chart({
					chart: {
						renderTo: 'tabs-2',
						type: 'column',
						width: 1048
					},
					title: {
						text: ' '
					},
					tooltip:{
						enabled: false
					},
					margin: [0, 0, 0, 0],
					xAxis: {
						categories: window.page_obj.trailing_req_chart_categories
					},
					yAxis: {
						title: {
							text: null
						}
					},
					legend: {
						layout: 'vertical',
						backgroundColor: '#FFFFFF',
						align: 'left',
						verticalAlign: 'top',
						x: 100,
						y: 70,
						floating: true,
						shadow: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
						series: [{
						name: 'Requests',
						data: window.page_obj.trailing_req_chart_values
			
					}]
				});
					
			},
			render: function(){
				
				return this;
			},
			events: {
				
			}
		}; Views.Stats = Backbone.View.extend(Views.Stats);
		
		Views.TopVisitors = {
			el: '#top_visitors_wrapper',
			users: [],
			initialize: function(){
				
				var _this = this;
				//find visitors facebook info...
				fbEnsureInit(function(){
					var users = eval(window.page_obj.statistics.top_visitors);
					
					jQuery.fbUserLookup(users, 'uid, name, first_name, pic_square, pic_big, third_party_id', function(rows){
					
						_this.users = rows;
						_this.render();
							
					});
					
				});				
				
			},
			render: function(){
				var el = this.$el.find('#top_visitors');
				
				el.empty();
				for(var i in this.users){
					
					console.log(this.users);
					
					var html = new EJS({
						text: window.ejs_view_templates_admin_promoters.user_thumb
					}).render(this.users[i]);
					el.append(html);
				}
				
				return this;
				
			},
			events: {
				
			}
		}; Views.TopVisitors = Backbone.View.extend(Views.TopVisitors);
		
		Views.RecentVisitors = {
			el: '#recent_visitors_wrapper',
			users: [],
			initialize: function(){
			
				var _this = this;
				fbEnsureInit(function(){
					var users = eval(window.page_obj.statistics.recent_visitors);

					jQuery.fbUserLookup(users, 'uid, name, first_name, pic_square, pic_big, third_party_id', function(rows){
					
						_this.users = rows;
						_this.render();
							
					});
			
				});
				
			},
			render: function(){
				
				var el = this.$el.find('#recent_visitors');
				el.empty();
				for(var i in this.users){
					var user = this.users[i];
					var html = new EJS({
						text: window.ejs_view_templates_admin_promoters.user_thumb
					}).render(user);
					html = jQuery(html);
					html.data('user', user);
					el.append(html);
				}
				
				return this;
			},
			events: {
				'click div.visitor': 'click_visitor'
			},
			click_visitor: function(e){
				
				var el = jQuery(e.currentTarget);
				var data = el.data('user');
				console.log(data);
				
			}
		}; Views.RecentVisitors = Backbone.View.extend(Views.RecentVisitors);
		
		
		
		
		
		Views.LiveVisitors = {
			el: '#live_visitors_wrapper',
			users: [],
			initialize: function(){
				
				/*---------------- team presence channels ---------------------------*/
						
				Pusher.channel_auth_endpoint = '/ajax/pusher_presence/';
				
				var pusher = new Pusher(window.module.Globals.prototype.pusher_api_key);
				var team_user_presence = pusher.subscribe('presence-promotervisitors-' + window.module.Globals.prototype.user_oauth_uid);
				pusher_disconnect_channel.push(team_user_presence);
				
				var _this = this;
				var subscription_succeeded = function(members){
					
					var live_visitor_timeout_remove_queue = {};
		
		
					//fix box we care about
					_this.$el.find('div#live_visitors').empty();
					
					var users = [];
					
					members.each(function(member){
						users.push(member.id);
					});
					
					if(users.length == 0)
						return;
									
					
					var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE ";
					for(var i = 0; i < users.length; i++){
						if(i == (users.length - 1)){
							fql += "uid = " + users[i];
						}else{
							fql += "uid = " + users[i] + " OR ";
						}
					}
					
					var query = FB.Data.query(fql);
					query.wait(function(rows){
						console.log(rows);
						
						for(var i = 0; i < rows.length; i++){													
							var html = '<div class="live_visitor ' + rows[i].uid + '">';
							html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
							html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
							html += '</div>';
							jQuery('div#live_visitors').append(html);
						}
						
					});
				}
				team_user_presence.bind('pusher:subscription_succeeded', subscription_succeeded);
				
				
				
						
				
				
				
				
				var live_visitor_timeout_remove_queue = {};
				team_user_presence.bind('pusher:member_added', function(member){
				  	
				  	if(live_visitor_timeout_remove_queue[member.id]){
				  		clearTimeout(live_visitor_timeout_remove_queue[member.id]);
				  	}
				  	
				  	//make sure not already in list
				  	if(jQuery('div#live_visitors').find('div.' + member.id).length > 0)
				  		return;
				  	
				  	var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE uid = " + member.id;
				  	console.log(fql);
				  	var query = FB.Data.query(fql);
					query.wait(function(rows){
						console.log(rows);
						
						for(var i = 0; i < rows.length; i++){
							var html = '<div class="live_visitor ' + rows[i].uid + '">';
							html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
							html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
							html += '</div>';
							jQuery('div#live_visitors').append(html);
						}
						
					});
					
				  	console.log('promotervisitors member_added');
					console.log(member)
									  					  	
				});
				
				team_user_presence.bind('pusher:member_removed', function(member){
				  	
				  	live_visitor_timeout_remove_queue[member.id] = setTimeout(function(){
				  		jQuery('div#live_visitors').find('div.' + member.id).remove();
				  	}, 1000 * 5);
				  	
				  	console.log('promotervisitors member_removed');
				  	console.log(member);
				  	
				});
				/*---------------- END team presence channels ---------------------------*/
				
				
				
			},
			render: function(){
				
			},
			events: {
				
			}
		}; Views.LiveVisitors = Backbone.View.extend(Views.LiveVisitors);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var team_announcements 	= new Views.TeamAnnouncements({});
		var stats 				= new Views.Stats({});
		var top_visitors 		= new Views.TopVisitors({});
		var recent_visitors 	= new Views.RecentVisitors({});
		var live_visitors 		= new Views.LiveVisitors({});
		

		
		
		
		
		
		
		/*
		
		var vc_fql_users = [];
				
		fbEnsureInit(function(){
			
			if(! jQuery('div#pending_reservations table tbody tr.loading'))
				return;
			
			var users = eval(window.page_obj.statistics.weekly_guest_lists_users);
			
			if(users.length > 0){
				
				var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
				
				var query = FB.Data.query(fql);
				query.wait(function(rows){
					
					//add users to window.vc_fql_users
					for(var i=0; i < rows.length; i++){
						vc_fql_users.push(rows[i]);
					}
									
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div#pending_reservations div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div#pending_reservations div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}
					
					jQuery('div#pending_reservations table tbody tr.loading').remove();
					jQuery('div#pending_reservations table tbody tr').each(function(){
						
						if(jQuery(this).attr('id') != 'table_row_tpl')
							jQuery(this).css('display', '');
							
					});
					
					window.zebraRows();
													
				});
				
			}
			
		});
			
		*/
		
		// --------------------------------------------------------------------------------------------
		Models.PendingRequest = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.PendingRequest = Backbone.Model.extend(Models.PendingRequest);
		
		
		
		
		Collections.PendingRequests = {
			model: Models.PendingRequest,
			initialize: function(){
				
			}
		}; Collections.PendingRequests = Backbone.Collection.extend(Collections.PendingRequests);
		
		
		
		
		
		Views.PendingRequestsTR = {
			tagName: 'tr',
			initialize: function(){
				
			},
			render: function(){
				
				this.$el.html('fizzle!');
				return this;
			},
			events: {
				
			}
		}; Views.PendingRequestsTR = Backbone.View.extend(Views.PendingRequestsTR);
		Views.PendingRequests = {
			el: '#pending_reservations table',
			initialize: function(){
				
				jQuery.fbUserLookup(window.page_obj.users, '', function(rows){
					console.log('rows');
					console.log(rows);
				});
				
			},
			render: function(users){
								
				var tbody = this.$el.find('tbody');
				tbody.empty();
				_this = this;
								
				this.collection.each(function(m){
					tbody.append(new Views.PendingRequestsTR({
						model: m
					}).render().el);
				});
				
			},
			events: {
				
			}
		}; Views.PendingRequests = Backbone.View.extend(Views.PendingRequests);
		
		
		
		
		var pending_requests 		= new Collections.PendingRequests(window.page_obj.backbone_pending_reservations);		
		var view_pending_requests	= new Views.PendingRequests({
			collection: pending_requests
		});
		
		view_pending_requests.render();
		
		// --------------------------------------------------------------------------------------------
		
		window.foo_pending_requests = pending_requests;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	
	
	
	
	
	
	
	

	
		/*---------------- team presence channels ---------------------------*/
				
		Pusher.channel_auth_endpoint = '/ajax/pusher_presence/';
		
		var pusher = new Pusher(window.module.Globals.prototype.pusher_api_key);
		var team_user_presence = pusher.subscribe('presence-promotervisitors-' + window.module.Globals.prototype.user_oauth_uid);
		pusher_disconnect_channel.push(team_user_presence);
		
		
		var subscription_succeeded = function(members){
			var live_visitor_timeout_remove_queue = {};
			
			//fix box we care about
			jQuery('div#live_visitors').empty();
			
			var users = [];
			
			members.each(function(member){
				users.push(member.id);
			});
			
			if(users.length == 0)
				return;
			
			var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				for(var i = 0; i < rows.length; i++){
												
					var html = '<div class="live_visitor ' + rows[i].uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
				}
				
			});
		}
		team_user_presence.bind('pusher:subscription_succeeded', subscription_succeeded);
		
		
		
		
		
		
		
		
		
		
		var live_visitor_timeout_remove_queue = {};
		team_user_presence.bind('pusher:member_added', function(member){
		  	
		  	if(live_visitor_timeout_remove_queue[member.id]){
		  		clearTimeout(live_visitor_timeout_remove_queue[member.id]);
		  	}
		  	
		  	//make sure not already in list
		  	if(jQuery('div#live_visitors').find('div.' + member.id).length > 0)
		  		return;
		  	
		  	var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE uid = " + member.id;
		  	console.log(fql);
		  	var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				for(var i = 0; i < rows.length; i++){
					var html = '<div class="live_visitor ' + rows[i].uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
				}
				
			});
			
		  	console.log('promotervisitors member_added');
			console.log(member)
							  					  	
		});
		
		team_user_presence.bind('pusher:member_removed', function(member){
		  	
		  	live_visitor_timeout_remove_queue[member.id] = setTimeout(function(){
		  		jQuery('div#live_visitors').find('div.' + member.id).remove();
		  	}, 1000 * 5);
		  	
		  	console.log('promotervisitors member_removed');
		  	console.log(member);
		  	
		});
		/*---------------- END team presence channels ---------------------------*/
	
	


































		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			console.log(pusher_unbind_event);
			console.log(pusher_disconnect_channel);
			
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
			
			for(var i in pusher_unbind_event){
			//	pusher_unbind[i]
			}
			
			for(var i in pusher_disconnect_channel){
				pusher_disconnect_channel[i].disconnect();
			}
			
		}
		
	}
	
});