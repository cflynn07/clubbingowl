if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_guest_lists = function(){
								
		var EVT 				= window.ejs_view_templates_admin_managers;
		var globals 			= window.module.Globals.prototype;
		var page_collapsed 		= false;	
		var unbind_callbacks 	= [];

		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		
		
		Models.Status = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		};
		Models.Status				= Backbone.Model.extend(Models.Status);
		
		Models.Reservation = {
			initialize: function(){
				
			},
			defaults: {
				collapsed: page_collapsed,
				show_pictures: true
			}
		};
		Models.Reservation 			= Backbone.Model.extend(Models.Reservation);
		
		Models.GuestList = {
			initialize: function(){
				
			},
			defaults: {
				active: false
			}
		};
		Models.GuestList			= Backbone.Model.extend(Models.GuestList);
		
		
		
		
			
		
		
		Models.TeamVenue = {
			initialize: function(){
				
			},
			defaults: {
				active: false
			}
		};
		Models.TeamVenue			= Backbone.Model.extend(Models.TeamVenue);
		Collections.TeamVenues = {
			model: Models.TeamVenue,
			initialize: function(){
				
			}
		}; Collections.TeamVenues = Backbone.Collection.extend(Collections.TeamVenues);
		
		
		
		
		
		
		
		
		Collections.Reservations = {
			model: Models.Reservation,
			initialize: function(){
				
			}
		};
		Collections.Reservations 	= Backbone.Collection.extend(Collections.Reservations);
		
		Collections.GuestLists = {
			model: Models.GuestList,
			initialize: function(args){
								
			}
		};
		Collections.GuestLists		= Backbone.Collection.extend(Collections.GuestLists);



		Views.ManualAddModal = {
			
			vlfit_id: 			false,
			
			modal_view: 		null,
			selected_head_user: null,
			exclude_ids: 		[],
			
			table_request: 		0,
			table_min_spend:	0,
			
			//backbone used within sub-views
			Models: 		{},
			Collections: 	{},
			Views: 			{},
			collections_group: 	null,
						
			
			el: 			'#manual_add_modal',
			initialize: function(){
				
				this.$el.empty();
				var _this = this;
				
				this.modal_view = this.$el.dialog({
					modal: 		true,
					width: 		500,
					height: 	'auto',
					title: 		(this.model !== null) ? this.model.get('pgla_name') : '',
					resizable: 	false,
					close: function(){
						
						//updates the guest-lists from server
						pending_requests_change();
						_this.destroy_view();
						
					}
				});
				
				this.render();
			},
			destroy_view: function() {

			    //COMPLETELY UNBIND THE VIEW
			    this.undelegateEvents();
			
			    //jQuery(this.el).removeData().unbind(); 
			
			    //Remove view from DOM
			    //this.remove();  
			    //Backbone.View.prototype.remove.call(this);
			
		  	},
			render: function(){
				
				var template = EVT['guest_lists/gl_manual_add_base'];
				var _this = this;
				
				var html = new EJS({
					text: template
				}).render({});
				
				this.$el.html(html);
				
			},
			render_loading: function(){
				var template = EVT['guest_lists/gl_manual_add_loading'];
				
				var html = new EJS({
					text: template
				}).render({});
				this.$el.html(html);
				
			},
			render_table_flow_1: function(){
				
				var _this 		= this;
				var iso_date 	= this.model.get('iso_date');
				
				
				this.render_loading();
				jQuery.background_ajax({
					data: {
					//	vc_method: 	'manual_add_find_tables',
						vc_method: 	'find_tables',
						pgla_id: 	this.model.get('id'),
						tv_id:		this.model.get('tv_id'),
						iso_date: 	iso_date
					},
					success: function(data){
						
						console.log('complete---');
						console.log(data);
						
						var venue;
						for(var i in data.message.team_venues){
							venue = data.message.team_venues[i];
						}
						

						
					//	console.log('venue');
					//	console.log(venue);
					//	console.log(_this.model.toJSON());
						
						var tgla_day = _this.model.get('tgla_day');
						tgla_day = tgla_day.slice(0, -1);
						
						// Find the prices of tables for the day
						// --------------------------------------------------------------------
			/*			//array of unique day prices
						var day_prices = [];
						
						var tgla_day = _this.model.get('tgla_day');
						tgla_day = tgla_day.slice(0, -1);
						
						for(var i in venue.venue_floorplan){
							var floor = venue.venue_floorplan[i]
							
							for(var k in floor.items){
								var item = floor.items[k];
								
								if(item.vlfi_item_type == 'table'){

									//what day do we care about?
									var table_day_price = item['vlfit_' + tgla_day + '_min'];
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
						
						
						var template = EVT['guest_lists/gl_manual_add_table'];
						var html = new EJS({
							text: template
						}).render({
						//	day_prices: day_prices,
							venue: 		venue,
							tgla_day: 	tgla_day
						});
						_this.$el.html(html);
						
						
						
						
						var tv_display_module 	= jQuery.extend(globals.module_tables_display, {});
						
						tv_display_module
							.initialize({
								display_target: 	'#floorplan_holder', //'#' + _this.$el.attr('id'),
								team_venue: 		venue,
								factor: 			0.5,
								options: {
									display_slider: true
								}
							});
						
						_this.modal_view.dialog('option', {
							width: 	900						
						});
						_this.modal_view.dialog('option', {
							position: 'center center'
						});
						
						_this.$el.find('select#table_min_price').trigger('change');
											
					}
				});
							
								
			},
			render_guestlist_flow_1: function(){

				this.render_loading();
				var _this = this;
				
				this.modal_view.dialog('option', {
					width: 	500						
				});
				this.modal_view.dialog('option', {
					position: 'center center'
				});
										
				var step1_complete = false;
				var step2_complete = false;
				var clients;
				var friends;
							
						
						
						
														
				this.Models.User = {
					initialize: function(){
						
					},
					defaults: {
						head_user: 	false,
						oauth_uid:	null,
						name: 		''
					}
				}; this.Models.User = Backbone.Model.extend(this.Models.User);
				
				this.Collections.Group = {
					model: this.Models.User,
					initialize: function(){
						
					}
				}; this.Collections.Group = Backbone.Collection.extend(this.Collections.Group);
								
				this.Views.User = {
					tagName: 'tr',
					initialize: function(){
						
						//this.render();
					},
					render: function(){
						
						var template = EVT['guest_lists/gl_manual_add_guestlist_friendspick_tr'];											
						var html = new EJS({
							text: template
						}).render(this.model.toJSON());
						
						this.$el.html(html);
						
						return this;
					},
					events: {
						'click *[data-action]': 'click_data_action'
					},
					click_data_action: function(e){
						
						var el = jQuery(e.currentTarget);
						var action = el.attr('data-action');
						switch(action){
							case 'delete-entourage-user':
								
								_this.collections_group.remove(this.model);
								this.unbind();
								this.remove();
								
								break;
						}
						
					}
				}; this.Views.User = Backbone.View.extend(this.Views.User);
				
				this.collections_group = new this.Collections.Group();
			
			
							
				// -------------------------------------------------------------------------
				var sync_complete_callback = function(){
					
					if(!(step1_complete && step2_complete))
						return;
					
					//merge friends & clients arrays w/ no duplicates
					console.log(clients);
					for(var i in clients){
						var client_found = false;
						for(var k in friends){
							if(clients[i].uid == friends[k].value){
								client_found = true;
								break;
							}
						}
						if(!client_found){
							friends.push({
								label: 	clients[i].name,
								value:	clients[i].uid
							});
						}
						
					}
					
					
					
					
					var template = EVT['guest_lists/gl_manual_add_guestlist_friendspick'];
					var html = new EJS({
						text: template
					}).render({
						table_request: 		_this.table_request,
						table_min_spend: 	_this.table_min_spend
					});
					
					
					
					
					_this.$el.html(html);
					
					_this.$el.find('input').autocomplete({
						source: 	friends,
						delay: 		20,
						focus: 		function(event, ui){
							
							//jQuery(this).val(ui.item.label);
							return false;
							
						},
						select: 	function(event, ui){
							
							console.log('select');
							console.log(_this.selected_head_user);
							
							
							//ui.item.value
							jQuery(this).val(ui.item.label);						
							_this.$el.find('#selected_pic').attr({
								src: 'https://graph.facebook.com/' + ui.item.value + '/picture?width=50&height=50'
							}).show();
							
							console.log('_this.collections_group.where({head_user: true}).length');
							console.log(_this.collections_group.where({head_user: true}).length);
							
							if(_this.collections_group.where({head_user: true}).length){
								_this.selected_head_user = {
									oauth_uid: 	ui.item.value,
									name: 		ui.item.label,
									head_user: 	false
								};
							}else{
								_this.selected_head_user = {
									oauth_uid: 	ui.item.value,
									name: 		ui.item.label,
									head_user: 	true
								};
							}
							
							console.log(_this.selected_head_user);
							
							return false;
															
						}
					}).data( "autocomplete" )._renderItem = function(ul, item){
						
						var html = '<a><table style="margin:0;"><tr>';
						html += '<td>';
						html += '<img src="https://graph.facebook.com/' + item.value + '/picture?width=50&height=50" />';								
						html += '</td>';
						html += '<td>';
						html += item.label;								
						html += '</td>';
						html += '</tr></table></a>';
													
			            return jQuery("<li></li>")
			                .data( "ui-autocomplete-item", item )
			                .append( html )
			                .appendTo( ul );
			                
			        };
				};
				// -------------------------------------------------------------------------
				
				
				
				
				
				
				jQuery.fbUserLookup(window.page_obj.clients, '', function(rows){
					
					clients 		= rows;
					step2_complete 	= true;
					sync_complete_callback();
					
				});
				fbEnsureInit(function(){

					FB.api('/me/friends', function(result){
						var user_friends = [];
						if(result.data){
							for(var i in result.data){
								user_friends.push({
									label: 	result.data[i].name,
									value:	result.data[i].id
								});
							}
						}
						friends 		= user_friends;
						step1_complete 	= true;
						sync_complete_callback();
					});
					
				});
				
				
			},
			events: {
				'click a[data-action]': 			'events_click_data_action',
				'change select#table_min_price': 	'events_change_select_min_price',
				'event-reorganize-tables': 			'reorganize_tables',
				'click div.item.table': 			'click_item_table'
			},
			click_item_table: function(e){
				
				
				
				
				
				
				var el = jQuery(e.currentTarget);
				
				if(el.data('reserved')){
					return false;
				}
								
				this.vlfit_id 			= el.data('vlfit_id');
				this.table_min_spend 	= el.data('table_min_spend');
				this.table_request		= 1;
				
				jQuery('div#manual_add_modal span#assigned_table').show();
				jQuery('div#manual_add_modal span#assigned_table div').html(el.html()).show();
							
				
			},
			reorganize_tables: function(){
				
				console.log('--- reorg tables ---');
				this.vlfit_id = false;
				jQuery('div#manual_add_modal span#assigned_table div').empty();
				
				
			},
			events_change_select_min_price: function(e){
				
				
				
				var el 		= jQuery(e.currentTarget);
				var value 	= el.val();
				
				this.table_min_spend = value;
				
				var tgla_day 	= this.model.get('tgla_day');
				tgla_day 		= tgla_day.slice(0, -1);
				
				
				
				this.$el.find('div.item.table').each(function(){
					jQuery(this).trigger('de-highlighted');
				});
				this.$el.find('div.item.table[day-price-' + tgla_day + '=' + value + ']').each(function(){
					jQuery(this).addClass('highlighted');
				});
				
				
						
			},
			events_click_data_action: function(e){
				
				e.preventDefault();
				
				this.$el.find('#message').html('');
				
				var el = jQuery(e.currentTarget);
				var action = el.attr('data-action');
				switch(action){
					case 'init-table-flow':
					
						this.table_request = 1;
						
						//find available tables (have user select price group)
						this.render_table_flow_1();
						
						//add friends
						
						
						//confirm w/ manager approval indication
						
						//ajax push to server & refresh
					
						break;
					case 'init-gl-flow':
					
						if(this.table_request == 1){
							if(this.vlfit_id === false){
								this.$el.find('p#message').html('Please select a table.')
								return false; 
							}
						}
												
						//add friends
						this.render_guestlist_flow_1();
						
						//simple confirm
						
						//ajax push to server & refresh
						
						break;
					case 'gl-flow-add-head':
						/**
						 * Add the head user to a guest-list group
						 */
						console.log('gl-flow-add-head');
						console.log(this.selected_head_user);
						
						
						//check for selected user
						if(!this.selected_head_user){
							
							//non FB client
							console.log('non-fb');
							var name = this.$el.find('input').val();
							console.log(name);
							if(!name.length){
								this.$el.find('#message').html('Please enter a name.');
								return false;
							}
							this.selected_head_user = {
								oauth_uid: 	null,
								name: 		name,
								head_user: 	true
							};
														
						}else{
							
							//FB client
							console.log('fb');
														
						}
						
						
						this.collections_group.reset([this.selected_head_user]);
						console.log(this.collections_group);
						this.selected_head_user = null;
						
													
						this.$el.find('#reservations_holder').show();
						this.$el.find('#reservations_holder_entourage').show();
						this.$el.find('#reservations_holder tbody').empty();
						this.$el.find('#reservations_holder_entourage tbody').empty();
						this.$el.find('#head_user_message').hide();
						this.$el.find('#ent_user_message').show();
						this.$el.find('a[data-action="gl-flow-add-final"]').show();
						
						this.$el.find('#selected_pic').hide();
						this.$el.find('input[type=text].sf').val('');
						
						var _this = this;				
						//display view w/ selected user and ask for entourage
						this.collections_group.each(function(m){
							
							var view = new _this.Views.User({
								model: m
							}).render().el;
							_this.$el.find('#reservations_holder tbody').append(view);
							
							_this.$el.find('*[data-role="head_user_name"]').html(m.get('name'));
														
																						
						});
						
						this.modal_view.dialog('option', {
							position: 'center center'
						});
						
						this.$el.find('a[data-action="gl-flow-add-head"]').attr({
							'data-action': 'gl-flow-add-entourage'
						});
						
						break;
					case 'gl-flow-add-entourage':
						/**
						 * Add an optional entourage user to a guest-list group
						 */
						
						console.log('gl-flow-add-entourage');					
						console.log(this.selected_head_user);
						
						
						//check for selected user
						if(!this.selected_head_user){
							
							//non FB client
							console.log('non-fb');
							var name = this.$el.find('input[type="text"].sf').val();

							if(!name.length){
								this.$el.find('#message').html('Please enter a name.');
								return false;
							}
							this.selected_head_user = {
								oauth_uid: 	null,
								name: 		name,
								head_user: 	false
							};
														
						}else{
							
							//FB client
							console.log('fb');
							
						}
						
						this.collections_group.add([this.selected_head_user]);
						console.log(this.collections_group);
						this.selected_head_user = null;
						
						
						this.$el.find('#selected_pic').hide();
						this.$el.find('input[type="text"].sf').val('');
						
						var _this = this;
						_this.$el.find('#reservations_holder_entourage tbody').empty();			
						//display view w/ selected user and ask for entourage
						this.collections_group.each(function(m){
							
							if(m.get('head_user'))
								return;
							
							var view = new _this.Views.User({
								model: m
							}).render().el;
							_this.$el.find('#reservations_holder_entourage tbody').append(view);
																			
						});
						
						this.modal_view.dialog('option', {
							position: 'center center'
						});
						
												
						//-----
						
						break;
					case 'gl-flow-add-final':
						/**
						 * Add the group to the guest-list
						 */
						
						//execute ajax call to add to 
						
						if(el.attr('disabled'))
							return;
						el.attr('disabled', 'disabled');
						el.hide();
						this.$el.find('#loading_img').show();
						
						var _this = this;
						
						jQuery.background_ajax({
							data: {
								vc_method: 			'manual_add_final',
								tgla_id:			_this.model.get('tgla_id'),
							//	up_id: 				window.page_obj.promoter.up_id,
								group: 				_this.collections_group.toJSON(),
								table_request: 		_this.table_request,
								vlfit_id: 			_this.vlfit_id,
								table_min_spend: 	_this.table_min_spend
							},
							success: function(data){
								
								console.log('data');
								console.log(data);

								_this.modal_view.dialog('close');
								
							}
						})
						
						break;
				}
				
				return false;				
			}
		}; Views.ManualAddModal = Backbone.View.extend(Views.ManualAddModal);



		Views.LeftMenu = {
			
			//cached DOM references
			gl_img: 	null,
			venue_img: 	null,
			ul: 		null,
			
			initialize: function(){
				
				this.ul  		= this.$el.find('ul:first');
				this.gl_img 	= this.$el.find('#left_menu_gl_img');
				this.venue_img 	= this.$el.find('#left_menu_venue_img');
							
				this.collection.on('change', this.change_collection, this);
								
				this.render();
				
		

			},
			render: function(){
				
				console.log(this.collection);
				
				var template 	= EVT['guest_lists/gl_left_menu'];
				var _this 		= this;
				var weekdays 	= [
					'mondays',
					'tuesdays',
					'wednesdays',
					'thursdays',
					'fridays',
					'saturdays',
					'sundays'
				];
						
				this.ul.empty();
				_.each(weekdays, function(day){
										
					var day_title = day;
					
					var day_lists = _this.collection.where({
						tgla_day: day
					});
					for(var i in day_lists){
						day_lists[i] = day_lists[i].toJSON();
					}
					
					var html = new EJS({
						text: template
					}).render({
						day_title: day_title,
						day_lists: day_lists
					});
										
					_this.ul.append(html);
										
				});




				if(!window.location.hash.length){
					
					var first = this.collection.first(1);
					if(first.length){
						first[0].set({
							active: true
						});
					
						//window.location.hash = first[0].get('tgla_id'); //; + '|' first[0].get('tgla_name').replace(/ /g, '_');
						window.location.hash = first[0].get('tgla_id') + '-' + first[0].get('tgla_name').replace(/ /g, '_');
					}
					
				}else{
					jQuery(window).trigger('hashchange');
				}
				
				
				
				
				return this;
			},
			events: {
				'click span[data-tgla_id]': 'events_click_active'
			},
			events_click_active: function(e){
				
				var el = jQuery(e.currentTarget);
				var tgla_id = el.attr('data-tgla_id');
				var res = this.collection.where({
					tgla_id: tgla_id
				});
				
				if(res.length){
					this.collection.each(function(m){
						m.set({
							active: false
						});
					});
					
			//		res[0].set({
			//			active: true
			//		});
					
					window.location.hash = res[0].get('tgla_id') + '-' + res[0].get('tgla_name').replace(/ /g, '_'); //res[0].get('tgla_name').replace(/ /g, '_');
					
				//	pending_requests_change();
					
				}
				
			},
			change_collection: function(model, event){
				
				if(event.changes && event.changes.active === true){
					
					this.ul.find('span[data-tgla_id]').css({
						'font-weight': 	'normal',
						color: 			'black'
					});
					
					this.ul.find('span[data-tgla_id=' + model.get('tgla_id') + ']').css({
						'font-weight': 	'bold',
						color: 			'blue'
					});
					
					var img_style = {
						'max-width': 	'200px',
						border: 		'1px solid #CCC'
					};
					var img_base = window.module.Globals.prototype.s3_uploaded_images_base_url;
				
					this.gl_img.attr('src', 	img_base + 'guest_lists/' 		+ model.get('tgla_image') 	+ '_p.jpg').css({
						'max-width': 	'188px',
						'max-height': 	'266px',
						border: 		'1px solid #CCC'
					});
					this.venue_img.attr('src', 	img_base + 'venues/banners/' 	+ model.get('tv_image') 	+ '_t.jpg').css(img_style);
															
				}
												
			}
		};
		Views.LeftMenu 				= Backbone.View.extend(Views.LeftMenu);
		
		
		Views.Status = {
			el: '#list_status',
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var _this = this;
				var template = EVT['guest_lists/gl_status'];
				this.$el.unbind();
				
				var html = new EJS({
					text: template
				}).render(jQuery.extend(this.model.toJSON(), this.model.get('status')));
				
				this.$el.html(html);
				
				return this;
			},
			events: {
				'click a[data-action]': 'events_click_data_action'
			},
			events_click_data_action: function(e){
				
				e.preventDefault();
				
				var el = jQuery(e.currentTarget);
				var action = el.attr('data-action');
				switch(action){
					case 'update-status':
					
						var _this = this;
						var textarea 	= this.$el.find('textarea#insert_new_status');
						var status		= this.$el.find('#current_status');
						var new_status 	= jQuery.trim(textarea.val());
						textarea.val('');
						
						//show loading indicator
						status.html('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." />');
						
						jQuery.background_ajax({
							data: {
								vc_method: 	'update_list_status',
								status: 	new_status,
								tgla_id: 	_this.model.get('tgla_id')
							},
							success: function(data){
								
								if(data.success){
									status.html('<span style="color:blue;">' + data.message.status + '</span>');
									_this.$el.find('span#glas_last_updated').html(data.message.human_date);
								}else{
									status.html('');
								}
							}
						})
							
						break;
				}
				
				return false;
			}
		};
		Views.Status 				= Backbone.View.extend(Views.Status);
		
		
		
		
		
		
		
		Views.GuestList = {
			users: 						null,
			collection_reservations: 	null,
			active_list: 				null,
			className: 'list tabs',
			initialize: function(){
				
				this.collection.on('change', this.render, this);
				this.render();
				
			},
			render: function(spec_list){
				
				
				var _this 		= this;
				var template 	= EVT['guest_lists/gl_reservations_table'];
				
				
				
				console.log('typeof');
				console.log(typeof spec_list);
				console.log(spec_list);
				console.log(spec_list instanceof Models.GuestList);
				
				
				if(spec_list instanceof Models.GuestList){
					
					this.active_list = spec_list;
					var active_list = spec_list;
					
				}else{
					
					var active_list = this.collection.where({
						active: true
					});
					if(!active_list.length)
						return false;
											
					active_list 		= active_list[0];
					this.active_list 	= active_list;
					
				}
				
				
				
				


				//insert list status
				var view_status = new Views.Status({
					model: this.active_list
				});		
				
				
				
				//todo - delete
				console.log('--- active_list ---');
				console.log(this.active_list.toJSON()); 
				
				
				
				var html = new EJS({
					text: template
				}).render(active_list.toJSON());
				this.$el.html(html);
				
				
				
				
				var tbody 	 	 = this.$el.find('tbody');
				var current_list = active_list.get('current_list');

				
				
				
				
				
				
				
				
				
				
				this.$el.find('input.guest_list_datepicker').datepicker({
					dateFormat: 	'DD m/d/y',
					maxDate: 		'+6d',
				//	minDate: 		'-3y',
					beforeShowDay: 	function(date){
						
						var cal_day, tgla_day, tgla_day_int;
						
						cal_day 	= date.getDay();
    					tgla_day 	= _this.active_list.get('tgla_day');
    											
						switch(tgla_day){
							case 'sundays':
								tgla_day_int = 0;
								break;
							case 'mondays':
								tgla_day_int = 1;
								break;
							case 'tuesdays':
								tgla_day_int = 2;
								break;
							case 'wednesdays':
								tgla_day_int = 3;
								break;
							case 'thursdays':
								tgla_day_int = 4;
								break;
							case 'fridays':
								tgla_day_int = 5;
								break;
							case 'saturdays':
								tgla_day_int = 6;
								break;
						}
						
						if(cal_day != tgla_day_int){
							return [false, 'co-datepicker-unselectable'];
						}else{
							return [true, ''];
						}
						
					},
					onSelect: 		function(dateText, inst){

						//http://stackoverflow.com/questions/11339884/php-form-checking-2-dates-arent-too-far-apart
						//http://stackoverflow.com/questions/1579010/get-next-date-from-weekday-in-javascript
						var nextDay = function(x){
						    var now = new Date();    
						    now.setDate(now.getDate() + (x+(7-now.getDay())) % 7);
						    return now;
						}
						
												
						//var next_occurance_date = new Date(this.active_list.get('human_date'));
						var tgla_day, tgla_day_int, tgla_next_occurance_date, datepicker_selected_date, weeks_apart;
						
    					tgla_day 	= _this.active_list.get('tgla_day');
    											
						switch(tgla_day){
							case 'sundays':
								tgla_day_int = 0;
								break;
							case 'mondays':
								tgla_day_int = 1;
								break;
							case 'tuesdays':
								tgla_day_int = 2;
								break;
							case 'wednesdays':
								tgla_day_int = 3;
								break;
							case 'thursdays':
								tgla_day_int = 4;
								break;
							case 'fridays':
								tgla_day_int = 5;
								break;
							case 'saturdays':
								tgla_day_int = 6;
								break;
						}
						
						tgla_next_occurance_date = nextDay(tgla_day_int);
						datepicker_selected_date = jQuery(this).datepicker('getDate');

						//number of weeks apart
						weeks_apart = Math.abs(Math.round((datepicker_selected_date - tgla_next_occurance_date) / 1000 / 60 / 60 / 168));
						_this.fetch_week(weeks_apart);
								        
					}
				});
				
				
				
				
				
				
				
				
				
				
				
				
				
				if(current_list && current_list.groups){
						
						
					this.collection_reservations = new Collections.Reservations(current_list.groups);
					this.collection_reservations.each(function(m){
						
						var view_reservation = new Views.Reservation({
							model: m
						});
						tbody.append(view_reservation.el);
						view_reservation.render();
						
					});
					
					
					tbody.find('> tr:odd').addClass('odd');
					this.custom_events_add_fb_data();

				
					
				}else{
					
					var html = new EJS({
						text: EVT['guest_lists/gl_tr_no_reservations']
					}).render({});
					tbody.html(html);
					
					jQuery('#lists_container > table').width(jQuery('#lists_container').width());
					
				}

				this.$el.find('tbody').css({
					opacity: 1
				});
				
				return this;
				
				
			},
			events: {
				'click a[data-action]': 	'events_click_data_action',
				'custom-event-add-fb-data': 'custom_events_add_fb_data'
			},
			fetch_week: function(weeks_apart){
				
				this.$el.find('tbody').css({
					opacity: 0.5
				});
				
				var _this 	= this;
				var tgla_id = this.active_list.get('tgla_id');		
				var tv_id	= this.active_list.get('tv_id');		
				
				jQuery.background_ajax({
					data: {
						vc_method: 		'retrieve_guest_lists',
						tgla_id: 		tgla_id,
						tv_id:			tv_id,
						weeks_offset: 	weeks_apart
					},
					success: function(data){
										
											
						if(data.success){
							
							var team_venue;
							for(var i in data.message.team_venues){
								team_venue = data.message.team_venues[i];
							}
							
							
							console.log('team_venue');
							console.log(team_venue);
							
							
							var tv_gla;
							for(var i in team_venue.tv_gla){
								tv_gla = team_venue.tv_gla[i];
							}
							
							
							
							
							
							
						//	var team_venue = data.message.weekly_guest_lists;
						//	for(var i in weekly_guest_list){
						//		weekly_guest_list = weekly_guest_list[i];
						//		break;
						//	}
							
						//	console.log(data);
							_this.active_list.set(new Models.GuestList(tv_gla));
							
							
							
							
						//	window.page_obj.users = data.message.users;
							_this.users = null;
								
							_this.render();			
							_this.custom_events_add_fb_data();
							
							
							
							
						}else{
							
						}
						
						
						
												
					}
				});
						    
			},
			custom_events_add_fb_data: function(e){
				
				
				
				var _this = this;
				
				var fb_names = function(rows){
					for(var i in rows){
						var user = rows[i];
						_this.$el.find('*[data-name=' + user.uid + ']').html(user.name);
					}
				}				
				
				console.log('custom_events_add_fb_data');
				console.log('this.users');
				console.log(this.users);
				
				
				
				
				
		//		if(this.users === null){
					
					
					
					var users = [];
					//find all users in html
					this.$el.find('*[data-oauth_uid]').each(function(){
						
						var oauth_uid = jQuery(this).attr('data-oauth_uid');
						if(!oauth_uid)
							return;
							
						users.push(oauth_uid);
						
					});
					users = _.uniq(users);
					console.log('users');
					console.log(users);
					
					
					//add fb queried names
					jQuery.fbUserLookup(users, '', function(rows){
						_this.users = rows;
						fb_names(rows);
						
						
						jQuery('#lists_container > table').width(jQuery('#lists_container').width());
						
						
					});
					
		//		}else{
		//			fb_names(this.users);
		//		}
				
				
				
						
			},
			events_click_data_action: function(e){
				
				e.preventDefault();
				var el = jQuery(e.currentTarget);
				var action = el.attr('data-action');
				
				switch(action){
					case 'return-current-week':
					
						this.fetch_week(0);
					
						break;
					case 'manually-add':
					
						var manual_add_modal = new Views.ManualAddModal({
							model:		this.active_list,
							collection: this.collection_reservations
						});
					
						break;
					case 'expand-collapse-all':
						
						page_collapsed = !page_collapsed;
					
						if(this.collection_reservations)
							this.collection_reservations.each(function(m){
								
								console.log(m);
								m.set({
									collapsed: page_collapsed //!m.get('collapsed')
								});
																								
							});
						
						this.custom_events_add_fb_data();
						
						break;
				}
				
				return false;
			}
		};
		Views.GuestList 	= Backbone.View.extend(Views.GuestList);
		
		
		Views.Reservation = {
			tagName: 'tr',
			initialize: function(){
				
				this.model.set({
					collapsed: page_collapsed
				});
				this.model.on('change', this.render, this);
				
			},
			render: function(){
				
				console.log('Views.Reservation.render()');
				
							
				
				var template = EVT['guest_lists/gl_reservation'];
				var html = new EJS({
					text: template
				}).render(this.model.toJSON());
				
				html = jQuery(html);
				
				html.find('td:not(table.user_messages td)').css({
					'font-size': '12px'
				});
				
				this.$el.html(html);
				
				var timestamp = Math.floor(new Date().getTime() / 1000);
				if(Math.abs(timestamp - parseInt(this.model.get('tglr_create_time'))) < 10)
					this.$el.effect('highlight', {}, 2000, function(){
						
					});
					
				return this;
				
			},
			events: {
				'click span.original': 					'events_click_host_notes',
				'click a[data-action=update-notes]': 	'events_update_host_notes',
				'click a[data-action=request-respond]': 'events_click_request_respond',
			},
			events_click_request_respond: function(e){
				e.preventDefault();
				
				
				globals.module_manager_accept_guest_list_display.initialize(this);
				
				
				
				/*
				
				
				var _this = this;
				var head_user = this.model.get('head_user');
				
				var respond_callback = function(resp){
							
					jQuery('div#dialog_actions').find('textarea[name=message]').val('');
											
					jQuery.background_ajax({
						data: {
							vc_method: 	'update_pending_requests',
							pglr_id: 	_this.model.get('id'),
							action: 	resp.action,
							message: 	resp.message
						},
						success: function(data){
							
							////uhhhhhhhhh
							if(data.success)
								_this.model.set({
									pglr_response_msg: 	resp.message,
									pglr_approved:		(resp.action == 'approve') ? '1' : '-1'
								});
														
						}
					});
					
				};
				
				jQuery('div#dialog_actions').dialog({
					title: 		'Approve or Decline Request',
					height: 	'auto',
					width: 		320,
					modal: 		true,
					resizable: 	false,
					//draggable: 	false,
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
				});
				
				jQuery('div#dialog_actions').find('*[data-name]').attr('data-name', head_user);				
				jQuery('div#dialog_actions').find('*[data-pic]').attr('data-pic', 	head_user);				
				
				jQuery.fbUserLookup(window.page_obj.users, 'name, uid, third_party_id', function(rows){							
					
					for(var i in rows){
						var user = rows[i];
						if(user.uid != head_user)
							continue;
						
						jQuery('div#dialog_actions').find('*[data-name=' + head_user + ']').html(user.name);				
						jQuery('div#dialog_actions').find('*[data-pic=' + head_user + ']').attr('src', 	'https://graph.facebook.com/' + head_user + '/picture?width=50&height=50');
					
					}
					
				});
				
				
				
				*/
				
				
				
				
				
				
				
				
				
				return false;
			},
			events_update_host_notes: function(e){
				
				e.preventDefault();
				
				var new_notes = jQuery.trim(this.$el.find('td.host_notes div.edit textarea').val());
				this.$el.find('td.host_notes div.edit').hide();
				this.$el.find('img.message_loading_indicator').show();
				
				var _this = this;
				jQuery.background_ajax({
					data: {
						vc_method: 		'update_reservation_host_notes',
						tglr_id: 		_this.model.get('tglr_id'),
						host_message:	new_notes
					},
					success: function(data){
						
						//_this.$el.find('img.message_loading_indicator').hide();
						
						if(data.success){
							
							if(new_notes == _this.model.get('tglr_host_message')){
								_this.model.trigger('change');
								_this.$el.trigger('custom-event-add-fb-data');
								return;
							}
							
							_this.model.set({
								tglr_host_message: new_notes
							});
							_this.$el.trigger('custom-event-add-fb-data');
							
						}
						
					}
				});			
				
				return false
				
			},
			events_click_host_notes: function(e){
				e.preventDefault();
								
				var el = jQuery(e.currentTarget);
				el.hide();
				this.$el.find('td.host_notes div.edit').show();
				this.$el.find('td.host_notes div.edit textarea').val(this.model.get('pglr_host_message')).focus();
				
				return false;
			}
		};
		Views.Reservation 			= Backbone.View.extend(Views.Reservation);
		
		
		/*

		var hash_change_callback = function(){
			//window.location.hash;
			
			if(window.location.hash.length > 0)
			var pgla_name = window.location.hash.replace('_', ' ').replace('#', '');

			var res = collection_guest_lists.where({
				pgla_name: pgla_name
			});
						
			if(res.length){

				collection_guest_lists.each(function(m){
					m.set({
						active: false
					});
				});
				
				res[0].set({
					active: true
				});
				
			}
			
		}
		jQuery(window).bind('hashchange', hash_change_callback)
		jQuery(window).trigger('hashchange');

		*/


		var prc_fetching = false;
		var pending_requests_change = function(data){
			console.log('pending-requests-change');
			console.log('update-guest-lists');
			
			if(prc_fetching)
				return;
			prc_fetching = true;
			
			var pgla_id_active = collection_guest_lists.where({
				active: true
			});
			if(!pgla_id_active.length)
				return;
			pgla_id_active = pgla_id_active[0].get('pgla_id')
			
			jQuery.background_ajax({
				data: {
					vc_method: 'retrieve_guest_lists'
				},
				success: function(data){
					
					prc_fetching = false;
					
					if(data.success){
						view_guest_list.users = null;
						collection_guest_lists.reset(data.message.weekly_guest_lists, {
							silent: true
						});
						window.page_obj.users = data.message.users;
						
						var active_gl = collection_guest_lists.where({
							pgla_id: pgla_id_active
						});
						if(active_gl.length){
							
							collection_guest_lists.each(function(m){
								m.set({
									active: false
								});
							})
							
						//	active_gl[0].set({
						//		active: true
						//	});
							
							
							
						}
						
						view_guest_list.render();
						view_left_menu.render();
												
					}
										
				}
			});
		};
		team_chat_object.individual_channel.bind('pending-requests-change', pending_requests_change);
		
		
		
		
		
		
		
		
		
		
		
		var collection_team_venues;
		var collection_guest_lists;
		var view_left_menu;
		
		var prep_and_launch = function(){
			
			var PO_clone = jQuery.extend(window.page_obj, {});
			
			var lists 				= [];
			collection_team_venues 	= new Collections.TeamVenues(PO_clone.team_venues);
			collection_team_venues.each(function(m){
				
				var tv_gla 		= m.get('tv_gla');
				var tv_props 	= m.toJSON();
				delete tv_props.tv_gla;
				
				for(var i in tv_gla){
					lists.push(jQuery.extend(tv_gla[i], tv_props));
				}
					
			});
		
			
			collection_guest_lists 		= new Collections.GuestLists(lists);
			view_left_menu 				= new Views.LeftMenu({
				collection: collection_guest_lists,
				el: 		'#left_menu'
			});
			var view_guest_list = new Views.GuestList({
				collection: collection_guest_lists,
				el: 		'#lists_container'
			});


		};
		prep_and_launch();
		
		
		
		
		
		
		
		
		
		
		var hash_change_callback = function(){
			
			if(window.location.hash.length === 0)
				return;
				
			//var tgla_name = window.location.hash.replace('_', ' ').replace('#', '');
			var tgla_id = window.location.hash.replace('#', '');
			tgla_id = tgla_id.split('-')[0];
			
			
			var res = collection_guest_lists.where({
			//	tgla_name: tgla_name
				tgla_id: tgla_id
			});
						
			if(res.length){

				collection_guest_lists.each(function(m){
					m.set({
						active: false
					});
				});
				
				res[0].set({
					active: true
				});
				
			}
		};
		jQuery(window).bind('hashchange', hash_change_callback);
		jQuery(window).trigger('hashchange'); //<-- necessary, don't delete
		
		
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			//team_chat_object.individual_channel.unbind('pending-requests-change', pending_requests_change);
			jQuery(window).unbind('hashchange', hash_change_callback)
			
		}
		
		
		
		
		
		return;
		
		
		
		
		
		
		
		
		
		
		
		
		var unbind_callbacks = [];		
			
		
		var fb_operation_complete = false;
		
		jQuery('ul.sitemap li').bind('click', function(){
			
			jQuery(this).parents('ul.sitemap').find('li').css('font-weight', 'normal');
			jQuery(this).css('font-weight', 'bold');
	
			var tgla_id = jQuery(this).find('span.tgla_id').html();
			jQuery(this).parents('div.guest_list_content').find('div.list').css('display', 'none');
			jQuery(this).parents('div.guest_list_content').find('div.gl_status').css('display', 'none');
			
			jQuery('div#tgla_' + tgla_id).css('display', 'block');
			
			jQuery('div.gl_status_' + tgla_id).css('display', 'block');
						
		});
		
		fbEnsureInit(function(){
			
			//Display the first guest list by default
			var display_first = function(){
				jQuery('div#main_loading_indicator').remove();
				
				if(window.page_obj.team_venues.length > 0)
					jQuery('div#tabs').tabs().css('display', 'block');
					
				jQuery('ul.sitemap').each(function(){
					
					jQuery(this).children('li:first').css('font-weight', 'bold');
					var first_tgla_id = jQuery(this).children('li:first').find('span.tgla_id').html();
					
					jQuery('div#tgla_' + first_tgla_id).css('display', 'block');
					
					jQuery('div.gl_status_' + first_tgla_id).css('display', 'block');
					
				});
									
			};
			
		//	var users = eval('<?= json_encode($users) ?>');
			var users = window.page_obj.users;
			
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
					
					console.log(rows);
					
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
					
					display_first();
									
				});
				
			}else{
				
				display_first();
				
			}
			
			zebraRows();
	
			fb_operation_complete = true;
			
		});























		jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		jQuery('div#tabs > div.ui-widget-header > ul').css('display', 'none');
		
		if(!jQuery.isIpad())
			jQuery('table.guestlists > tbody > tr').hover(function(){
				jQuery(this).addClass('hovered');
			}, function(){
				jQuery(this).removeClass('hovered');
			});
		
		var host_notes_click = function(){
			var original = jQuery.trim(jQuery(this).find('span.original').html());
			
			jQuery(this).find('span.original').css('display', 'none');
			jQuery(this).find('div.edit').css('display', 'block');
			
			if(original != '<span style="font-weight: bold;">Edit Message</span>')
				jQuery(this).find('div.edit textarea').val(original);
								
			var characters = 160 - jQuery(this).find('div.edit textarea').val().length;
			jQuery(this).find('div.edit span.message_remaining').html("<strong>" + characters + "</strong> char remaining");
			
			jQuery(this).find('div.edit textarea').keyup(function(){
			    if(jQuery(this).val().length > 160){
			        jQuery(this).val(jQuery(this).val().substr(0, 160));
			    }
			        
			    var remaining = 160 - jQuery(this).val().length;
				jQuery(this).parent().parent().find('span.message_remaining').html("<strong>" + remaining + "</strong> char remaining");
			});
			
			jQuery(this).find('textarea').focus();
			jQuery(this).unbind('click');
			
		};
		
		
		
		
		jQuery('td.host_notes').live('click', host_notes_click);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes').die('click', host_notes_click);
		});
		
		
		
		
		
		var submit_host_message = function(me){
			var new_text = jQuery.trim(jQuery(me).val());
			var parent = jQuery(me).parent().parent();
			
			if(parent.find('div.edit').css('display') != 'block')
				return;
			
			parent.find('div.edit').css('display', 'none');
			parent.find('span.original').css('display', 'none');
			parent.find('img.message_loading_indicator').css('display', 'block');
			
			var tglr_id = parent.parent().parent().parent().parent().parent().find('td.tglr_id').html();
				
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					ci_csrf_token: cct,
					vc_method: 'update_reservation_host_notes',
					tglr_id: tglr_id,
					host_message: new_text
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					if(data.success){
						if(new_text.length == 0){
							parent.find('span.original').html('<span style="font-weight: bold;">Edit Message</span>');
						}else{
							parent.find('span.original').html(new_text);
						}
						parent.find('span.original').css('display', 'block').focus();
						parent.find('img.message_loading_indicator').css('display', 'none');
						parent.live('click', host_notes_click);
						unbind_callbacks.push(function(){
							parent.die('click', host_notes_click);
						});
					}
					
				}
			});	
		};
		
		var textarea_callback = function(){
			submit_host_message(this);
		}
		jQuery('td.host_notes textarea').live('blur', textarea_callback);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes textarea').die('blur', textarea_callback);
		});
		
		
		
		var keydown_callback = function(e){
			if(e.keyCode == 13){
				
				e.preventDefault();
				submit_host_message(this);
			}
		}
		jQuery('td.host_notes textarea').live('keydown', keydown_callback);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes textarea').die('keydown', keydown_callback);
		});
		
		
	//	manual_add_exclude_ids = eval('<?= json_encode($users) ?>');
		manual_add_exclude_ids = window.page_obj.users;
	
		//------- fb friends invite ----------
		jQuery('td.facebook_gl_invite').bind('click', function(){
			
			var table_body = jQuery(this).parent().parent();
			var tgla_id = table_body.find('td.tgla_id').html();
			var tv_id = table_body.find('td.tv_id').html();
			var venue_name = table_body.find('td.venue_name').html();
			var date = table_body.find('td.date').html();
			
			fbEnsureInit(function(){
				
				FB.ui({
					
					method: 'apprequests',
					message: 'I\'ve added you to my guest list on ClubbingOwl',
					title: 'Add friends to your Guest List',
					max_recipients: 20,
				//	exclude_ids: manual_add_exclude_ids
					
				}, function(response){
					
					if(response == undefined)
						return;
					
					var users = response.to;
					
					if(users.length == 0)
						return;
					
					//exclude additional users
					for(var i=0; i < users.length; i++){
						manual_add_exclude_ids.push(users[i]);
					}
					
					table_body.find('tr.no_reservations').remove();
					table_body.find('tr:last').before('<tr class="waiting"><td colspan="7"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></td></tr>');
					
					//------ retrieve function --------
					var iterator = 0;
					var retrieve_function = function(){
						
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
									ci_csrf_token: cct,
									vc_method: 'list_manual_add',
									status_check: true
									},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
								console.log(data);
								
								if(data.success){
									//job complete
									iterator = 0;
									
									if(!data.message){
										alert('Unknown error, Facebook indicates you are not friends with the users you added.');
										return;
									}
									
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
											window.vc_fql_users.push(rows[i]);
										}
										
										console.log(rows);
												
										//populate divs with FB data
										for(var i = 0; i < rows.length; i++){
											
											var table_html = '<tr class="new_add_manual">';
											table_html += '<td class="request_type" style="display:none;">manager</td>';
											table_html += '<td class="table" style="display:none;">0</td>';
											table_html += '<td class="tglr_id hidden" style="display:none;">' + data.message[rows[i].uid].tglr_id + '</td>';
											table_html += '<td class="tglr_head_user hidden" style="display:none;">' + rows[i].uid + '</td>';
											table_html += '<td class="tv_id" style="display:none;">' + tv_id + '</td>';
											table_html += '<td class="venue_name" style="display:none;">' + venue_name + '</td>';
											table_html += '<td class="date" style="display:none;">' + date + '</td>';
											
											//fields not shown on GL page but shown on dash page
											table_html += '<td class="venue" style="display:none;">' + venue_name + '</td>'; //TODO <-- make sure matches venue html on dashboard page
											table_html += '<td class="promoter" style="display:none;"> - </td>';
											table_html += '<td class="min_spend" style="display:none;">$500</td>';
													
											table_html += '<td class="user_name visual"><span class="name_' + rows[i].uid + '"></span></td>';
											table_html += '<td class="user_pic visual"><div class="pic_square_' + rows[i].uid + '"></div></td>';
											table_html += '<td class="visual">';
											table_html += '	<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
											table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
											table_html += '		<tr><td class="request_msg"> - </td></tr>';
											table_html += '		<tr><td class="message_header">Response Message:</td></tr>';
											table_html += '		<tr><td class="response_message"> - </td></tr>';
											table_html += '		<tr><td class="message_header">Host Notes:</td></tr>';
											table_html += '		<tr style="max-width:122px;">';
											table_html += '			<td class="host_notes" style="max-width:122px;">';
											table_html += '				<div class="edit" style="display:none;">';
											table_html += '					<textarea></textarea>';
											table_html += '					<br>';
											table_html += '					<span class="message_remaining"></span>';
											table_html += '				</div>';
											table_html += '				<span class="original">';
											table_html += '					<span style="font-weight: bold;">Edit Message</span>';
											table_html += '				</span>';
											table_html += '				<img class="message_loading_indicator" style="display:none;" src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." />';
											table_html += '			</td>';
											table_html += '		</tr>';
											table_html += '	</table>';
											table_html += '</td>';
											
											table_html += '<td class="visual"><span style="color:red;">No</span></td>';
											table_html += '<td class="visual"><span style="color: green;">Approved</span></td>';
											table_html += '<td class="entourage visual" style="white-space:nowrap; width:244px;"><p>No Entourage</p></td>';
											table_html += '</tr>';
											
											jQuery(table_html).insertBefore(table_body.find('tr:last'));
											
											jQuery('div#lists_container div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
											jQuery('div#lists_container span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
																							
										}
																					
										//tv_id
										var name = jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.name').html();
										var tv_count = parseInt(jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.count').html());
										tv_count += rows.length;
										jQuery('div#tabs div.ui-widget-header select.venue_select option.' + tv_id).html(name + ' (' + tv_count + ')');
										jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.count').html(tv_count);
										
										//tgla_id
										var tgla_count = parseInt(jQuery('div#tabs ul.sitemap li.' + tgla_id + ' span.count_tgla_id').html());
										tgla_count += rows.length;
										jQuery('div#tabs ul.sitemap li.' + tgla_id + ' span.count_tgla_id').html(tgla_count);
										
										table_body.find('tr.waiting').remove();
										
										var offset = jQuery('tr.new_add_manual:last').offset();
										jQuery(document).scrollTop(offset.top - 50);
										
										jQuery('tr.new_add_manual td.visual').show('highlight', { color:'red' }, 1500, function(){
											jQuery('tr.new_add_manual').removeClass('new_add_manual');
											jQuery('tr.visual').removeClass('visual');
											
											window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
											
											if(!jQuery.isIpad())
												jQuery('table.guestlists > tbody > tr').hover(function(){
													jQuery(this).addClass('hovered');
												}, function(){
													jQuery(this).removeClass('hovered');
												});
										});
										
									});
									
								}else{
									//not complete
									
									iterator++;
									console.log(iterator);
									setTimeout(retrieve_function, 1000);
									
								}
								
							}
						});
					
					};
					//------ end retrieve function --------
					
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							ci_csrf_token: cct,
							vc_method: 'list_manual_add',
							tgla_id: tgla_id,
							oauth_uids: users
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
													
							window.setTimeout(retrieve_function, 1000);
							
						}
					});
					
				});
					
			});
			
		});

































		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});