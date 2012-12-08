if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_dashboard = function(){
						

		var EVT = window.ejs_view_templates_admin_managers;
			
		var Models 		= {};
		var Collections = {};
		var Views 		= {};

		Views.TeamAnnoucements = {
			el: '#team_announcements',
			team_managers: [],
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var oauth_uids = [];
				
				//gather all uids
				this.$el.find('*[data-oauth_uid]').each(function(){
					
					var oauth_uid = jQuery(this).attr('data-oauth_uid');
					if(_.indexOf(oauth_uids, oauth_uid) == -1)
						oauth_uids.push(oauth_uid);
					
				});
				
				var _this = this;
				jQuery.fbUserLookup(oauth_uids, '', function(rows){
					
					for(var i in rows){
						_this.$el.find('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="" />');
						_this.$el.find('.name_' + rows[i].uid).html(rows[i].name);												
					}
					
					_this.$el.find('#messages_loading_indicator').hide();
					_this.$el.find('#team_announcements_content').show();
					
				});
				jQuery('#resize_box').resizable({
				  maxWidth:1050,
				  minWidth:1050
				});
				
				
			},
			events: {
				'click *[data-action]': 'click_data_action'
			},
			click_data_action: function(e){
				
				e.preventDefault();
				
				var el 		= jQuery(e.currentTarget);
				var action 	= el.attr('data-action');
				switch(action){
					case 'create-announcement':
					
						if(!jQuery('div#announcement_dialog').hasClass('ui-dialog'))
							jQuery('div#announcement_dialog').dialog({
								resizable: 	false,
								height:		340,
								modal: 		true,
								buttons: [{
									text: 'Cancel',
									click: function(){
										
										
										
										jQuery(this).dialog('close');
									}
								},{
									text: 'Okay',
									'class': 'btn-confirm',
									click: function(){
										
										var _this = this;
										
										var message = jQuery('textarea#manager_announcement_textarea').val();
										if(message.length == 0){
											jQuery('p#manager_announcement_msg').html('Message can not be blank');
											return;
										}
										
										jQuery('div#announcement_dialog .loading_indicator').show();
										
										jQuery.background_ajax({
											data: {
												vc_method: 'announcement_create',
												message: 	message
											},
											success: function(data){
												jQuery('div#announcement_dialog .loading_indicator').hide();
												
												if(data.success){
													
													jQuery(_this).dialog('close').remove();
													jQuery('#primary_left .li_dashboard a').trigger('click');
													
												}else{
													
													if(data.message)
														jQuery('p#manager_announcement_msg').html(data.message);
													
												}
											}
										});
										
										jQuery(this).dialog('close');
									}
								}]
							});
				}
			}
		}; Views.TeamAnnoucements = Backbone.View.extend(Views.TeamAnnoucements);
		
		Views.TeamStatistics = {
			el: '#team_statistics',
			initialize: function(){
				
				
				if(window.page_obj.team.team_completed_setup == '1')
					this.render();
					
			},
			render: function(){
				
				var count = 0;
				var retrieve_function = function(){
					
					console.log('retrieve_function called');
					
					if(count > 4){
						count = 0;
						jQuery('div.team_stats_tabs img#loading_gif').remove();
						jQuery('div.team_stats_tabs div#tabs-1').html('<span style="color:red">We\'re sorry, something went wrong. We\'ll get it fixed as soon as possible, please try again in a few minutes.</span>').css('display', 'block');
						return;
					}
					
					jQuery.background_ajax({
						data: {
							vc_method: 		'stats_retrieve',
							status_check: 	true,
						},
						success: function(data){
							
							if(!data.success){
								count++;
								setTimeout(retrieve_function, 1000);
								return;
							}
							count = 0;
							
							
							var categories 		= [];
							var visits 			= [];
							var unique_visitors = [];
							
							if(typeof data.message.visits.length == undefined)
								return; //error
								
							var counter = 0;
							for(key in data.message.visits){
															
								if(!(counter % 2))
									categories.push(key.toString().substring(5, 10).replace('-', '/'));
								else
									categories.push(' ');
								
								counter++;
								
								visits.push(data.message.visits[key]);
								unique_visitors.push(data.message.unique_visitors[key]);
							}

							jQuery('img#loading_gif').remove();
							
							jQuery('div#tabs-1').css('display', 'block');
												
							visitors_chart = new Highcharts.Chart({
								chart: {
									renderTo: 'tabs-1',
									defaultSeriesType: 'area'
								},
								tooltip:{
									enabled: false
								},
								width: '100%',
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
							
						}
					});
				};
				
				retrieve_function();
				
				
				
				
				trailing_requests_chart = new Highcharts.Chart({
					chart: {
						renderTo: 'tabs-2',
						type: 'column',
						width: 1048
					},
					title: {
						text: ' '
					},
					margin: [0, 0, 0, 0],
					xAxis: {
						categories: window.page_obj.trailing_gl_requests_categories
					},
					yAxis: {
						title: {
							text: null
						}
					},
					tooltip:{
						enabled: false
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
						data: window.page_obj.trailing_gl_requests_values
			
					}]
				});

			}
		}; Views.TeamStatistics = Backbone.View.extend(Views.TeamStatistics);

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
				
				console.log(this.model.get('request_type'));
				console.log(this.model.toJSON());
				
				var html = new EJS({
					text: EVT.pending_reservation_request_dashboard
				}).render(this.model.toJSON());
				
				this.$el.html(html);
				return this;
			},
			events: {
				'click *[data-action]': 'click_data_action'				
			},
			event_request_responded: function(obj){
				
				var el 		= obj.el;
				var action 	= obj.action;
				
			},
			click_data_action: function(e){
				
				e.preventDefault();
				
				var el 				= jQuery(e.currentTarget);
				var action 			= el.attr('data-action');
				var head_user 		= this.model.get('head_user') || this.model.get('tglr_user_oauth_uid');
				var globals 		= window.module.Globals.prototype;
				var _this 			= this;
				var table_display 	= (this.model.get('request_type') == 'promoter' || this.model.get('tglr_table_request') == '1');
				var Views = {};




				switch(action){
					case 'request-respond':


						



						var display_tables_helper = function(){
							
							var target = '#dialog_actions_floorplan';
							jQuery(target).hide();
							
							jQuery.background_ajax({
								data: {
									vc_method: 	'find_tables',
									tv_id:		_this.model.get('tv_id'),
									
								},
								success: function(data){
																		
									var venue = false;
									for(var i in data.message.team_venues){
										
										console.log(_this.model.get('tv_id'));
										console.log(data.message.team_venues[i]);
										
										if(data.message.team_venues[i].tv_id == _this.model.get('tv_id')){
											venue = data.message.team_venues[i];
											break;
										}
									}
									if(!venue)
										return false;
								
																					
									
									// Find the prices of tables for the day
									// --------------------------------------------------------------------
							/*		//array of unique day prices
									var day_prices = [];
									
									var pgla_day = _this.model.get('pgla_day');
									pgla_day = pgla_day.slice(0, -1);
									
									for(var i in venue.venue_floorplan){
										var floor = venue.venue_floorplan[i]
										
										for(var k in floor.items){
											var item = floor.items[k];
											
											if(item.vlfi_item_type == 'table'){
			
												//what day do we care about?
												var table_day_price = item['vlfit_' + pgla_day + '_min'];
												console.log(table_day_price);
												
												if(jQuery.inArray(table_day_price, day_prices) === -1){
													day_prices.push(table_day_price);
												}
												
											}	
										}
									}
									
									day_prices = day_prices.sort();
									console.log(day_prices);
									// --------------------------------------------------------------------
							*/		
							
									var tv_display_module 	= jQuery.extend(globals.module_tables_display, {});
									var target = '#dialog_actions_floorplan';
									tv_display_module
										.initialize({
											display_target: 	target, //'#' + _this.$el.attr('id'),
											team_venue: 		venue,
											factor: 			0.45,
											options: {
												display_slider: true
											}
										});
									jQuery(target).show();	
									
									
									//_this.modal_view.dialog('option', {
									//	width: 	900						
									//});
									//_this.modal_view.dialog('option', {
									//	position: 'center center'
									//});
									
									//_this.$el.find('select#table_min_price').trigger('change');
														
								}
							});
						}
	
			





						var respond_callback = function(resp){
							
							jQuery('div#dialog_actions').find('textarea[name=message]').val('');
							
							if(resp.action == 'approve'){
								_this.$el.css({
									background: 'green'
								});
							}else{
								_this.$el.css({
									background: 'red'
								});
							}
							
							jQuery.background_ajax({
								data: {
									vc_method: 	'update_pending_requests',
									pglr_id: 	_this.model.get('id'),
									action: 	resp.action,
									message: 	resp.message
								},
								success: function(data){
									
									console.log(data);
																	
									_this.$el.animate({
										opacity: 0
									}, 500, 'linear', function(){
										//_this.$el.trigger('request-responded');
										_this.$el.remove();
									});
									
								}
							});
							
						};
						
						
						
						
						
						
															
									
												
						Views.DialogActions = {
							initialize: function(){
								//insert user name into dialog				
								if(head_user){
									jQuery('div#dialog_actions').find('*[data-name]').attr('data-name', head_user);				
									jQuery('div#dialog_actions').find('*[data-pic]').attr('data-pic', 	head_user);				
									jQuery.fbUserLookup([head_user], 'name, uid, third_party_id', function(rows){							
										for(var i in rows){
											
											var user = rows[i];
											if(user.uid != head_user)
												continue;
												
											jQuery('div#dialog_actions').find('*[data-name=' + head_user + ']').html(user.name);				
											jQuery('div#dialog_actions').find('*[data-pic=' + head_user + ']').attr('src', 	'https://graph.facebook.com/' + head_user + '/picture?width=50&height=50');
										}
									});
								}else{
									jQuery('div#dialog_actions').find('*[data-name]').html(_this.model.get('pglr_supplied_name'));				
									jQuery('div#dialog_actions').find('*[data-pic]').attr('src', window.module.Globals.prototype.admin_assets + 'images/unknown_user.jpeg');	
								}		
							},
							events: {
								'click .item.table': 'click_item_table'
							},
							click_item_table: function(e){
								
								var el = jQuery(e.currentTarget);
								el.trigger('highlighted');
								
								this.$el.find('.table').each(function(){
									jQuery(this).trigger('de-highlighted');
								})
																
							},
							close: function(){
								
								console.log('view close');
								this.$el.unbind();
								
							}
						}; Views.DialogActions = Backbone.View.extend(Views.DialogActions);
						
						
						
						
						
						
						
						
						var dialog_options = {
							title: 		'Approve or Decline Request',
							modal: 		true,
							resizable: 	true,
							draggable: 	true,
							open: 		function(){
								
								display_tables_helper();
								
							},
							close: 		function(){
								
								if(view_dialog_actions && view_dialog_actions.close)
									view_dialog_actions.close();
								
								if(tv_display_module && tv_display_module.destroy)
									tv_display_module.destroy();
								
							},
							buttons: [{
								text: 'Decline',
								click: function(){
									respond_callback({
										action: 'decline',
										message: jQuery(this).find('textarea[name=message]').val()
									});
									jQuery(this).dialog('close');
								}
							},{
								text: 'Approve',
								id: 'ui-approve-button',
								'class': 'btn-confirm',
								click: function(){
									respond_callback({
										action: 'approve',
										message: jQuery(this).find('textarea[name=message]').val()
									});
									jQuery(this).dialog('close');
								}
							}]
						};
						
						
						
						if(!table_display){
							dialog_options.height 	= 420;
							dialog_options.width 	= 320;
						}else{
							dialog_options.height 	= 540;
							dialog_options.width 	= 720;
						}
						
						
						
						
						
						jQuery('div#dialog_actions').dialog(dialog_options);
						var view_dialog_actions = new Views.DialogActions({
							el: '#dialog_actions'
						});
							
							
							
															
						break;
				}
	
			}
		}; Views.PendingRequestsTR = Backbone.View.extend(Views.PendingRequestsTR);
		Views.PendingRequests = {
			el: '#pending_reservations table',
			initialize: function(){

				this.collection.on('reset', this.pre_render, this);
				this.pre_render();
				
			},
			pre_render: function(){
								
				
				var _this = this;
				var html;
				
				if(this.collection.length){
					html = new EJS({
						text: EVT.tr_loading
					}).render({
						colspan: 12
					});			
					
				}else{
					
					html = new EJS({ 
						text: EVT['pending_reservation_none']
					}).render({
						colspan: 12
					});
					
				}
				
				this.$el.find('tbody').empty().append(jQuery('<tr></tr>').html(html));
				
				this.render();
			},
			render: function(){
				
				var _this = this;
				var oauth_uids = [];
				var tbody = this.$el.find('tbody');
				
				tbody.empty();
				
				this.collection.each(function(m){
				
					tbody.append(new Views.PendingRequestsTR({
						model: m
					}).render().el);
				
				});
				
				
				
				
				//hunt down all the oauth-uids and supply user names'
				this.$el.find('*[data-oauth_uid]').each(function(){
					
					var oauth_uid = jQuery(this).attr('data-oauth_uid');
					if(_.indexOf(oauth_uids, oauth_uid) == -1){
						
						if(oauth_uid != 'null' && oauth_uid != '')
							oauth_uids.push(oauth_uid);
						
					}
					
				});
				jQuery.fbUserLookup(oauth_uids, '', function(rows){
					for(var i in rows){
						var user = rows[i];
						_this.$el.find('*[data-name="' + user.uid + '"]').html(user.name);
					}
				});
										
								
			},
			events:{
				'request-responded': 'update_collection'
			},
			update_collection: function(e){
				
				var _this = this;
				jQuery.background_ajax({
					data: {
						vc_method: 'retrieve_pending_requests'
					},
					success: function(data){
						
						console.log('retrieve_pending_requests');
						console.log(data);
						
						window.page_obj.pending_reservations_users = data.message.users;
						_this.collection.reset(data.message.reservations);
						
					}
				})
				
			}
		}; Views.PendingRequests = Backbone.View.extend(Views.PendingRequests);
		
		var pending_requests 		= new Collections.PendingRequests(window.page_obj.statistics.pending_requests);		
		var view_pending_requests	= new Views.PendingRequests({
			collection: pending_requests
		});
		
		//window.foo_pending_requests = view_pending_requests;
		
		
		
		
		// --------------------------------------------------------------------------------------------
		var pending_requests_change = function(data){
			console.log('pending-requests-change');
			
			
			console.log('data');
			console.log(data);
			view_pending_requests.update_collection();
		}
		team_chat_object.individual_channel.bind('pending-requests-change', pending_requests_change);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var views_team_announcements = new Views.TeamAnnoucements({});
		var views_team_statistics	 = new Views.TeamStatistics({});
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		if(page_obj.statistics.top_visitors.length == 0){
			jQuery('div#top_visitors').empty();
		}else{
			jQuery.fbUserLookup(page_obj.statistics.top_visitors, '', function(rows){
				
				jQuery('div#top_visitors').empty();
				
				for(var i in rows){
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div class="top_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#top_visitors').append(html);
				
				}
				
			});
		}
		
		
		if(page_obj.statistics.recent_visitors.length == 0){
			jQuery('div#recent_visitors').empty();
		}else{
			jQuery.fbUserLookup(page_obj.statistics.recent_visitors, '', function(rows){
				
				jQuery('div#recent_visitors').empty();
				
				for(var i in rows){
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
						
					var html = '<div class="recent_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#recent_visitors').append(html);
					
				}
				
			});
		}
		
		
		
		
		
		
		
		
		
		
		
		Pusher.channel_auth_endpoint = '/ajax/pusher_presence/';
				
		Pusher.authorizers.ajax = function(pusher, callback){
			var self = this, xhr;
	
		    if (Pusher.XHR) {
		      xhr = new Pusher.XHR();
		    } else {
		      xhr = (window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
		    }
		
		    xhr.open("POST", Pusher.channel_auth_endpoint, true);
		    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		    xhr.onreadystatechange = function() {
		      if (xhr.readyState == 4) {
		        if (xhr.status == 200) {
		          var data, parsed = false;
		
		          try {
		            data = JSON.parse(xhr.responseText);
		            parsed = true;
		          } catch (e) {
		            callback(true, 'JSON returned from webapp was invalid, yet status code was 200. Data was: ' + xhr.responseText);
		          }
		
		          if (parsed) { // prevents double execution.
		            callback(false, data);
		          }
		        } else {
		          Pusher.warn("Couldn't get auth info from your webapp", status);
		          callback(true, xhr.status);
		        }
		      }
		    };
		    
		    var csrf_token = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
		    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token);
		};
		
		
		var pusher = new Pusher(window.module.Globals.prototype.pusher_api_key);
		var team_user_presence = pusher.subscribe('presence-teamvisitors-' + window.page_obj.team.team_fan_page_id);
		
		team_user_presence.bind('pusher:subscription_succeeded', function(members){
			
		
			//fix box we care about
			jQuery('div#live_visitors').empty();
			
			var users = [];
			
			members.each(function(member){
				users.push(member.id);
			});
			
			if(users.length == 0)
				return;
				
			jQuery.fbUserLookup(users, '', function(rows){
				
				for(var i in rows){
					
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div data-user_oauth_uid="' + user.uid + '" class="live_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
					
				}
				
			});
			
			
		});
		
		team_user_presence.bind('pusher:member_added', function(member){
		  	
		  	
		  	if(jQuery('div#live_visitors').find('div[data-user_oauth_uid="' + member.id + '"]').length == 0){
		  		
		  		jQuery.fbUserLookup([member.id], '', function(rows){
		  			
		  			for(var i in rows){
						
						var user = rows[i];
						
						if(user.uid == window.page_obj.users_oauth_uid)
							continue;
						
						var html = '<div data-user_oauth_uid="' + user.uid + '" class="live_visitor ' + user.uid + '">';
						html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
						html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
						html += '</div>';
						jQuery('div#live_visitors').append(html);
						
					}
		  			
		  		});
		  		
		  	}
		  	
		  	 	
		});
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			
		}
		
		

	}
	
});