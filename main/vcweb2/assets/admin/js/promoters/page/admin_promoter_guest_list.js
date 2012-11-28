if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	
	window.vc_page_scripts.admin_promoter_guest_list = function(){
						
						
		var EVT = window.ejs_view_templates_admin_promoters;
		var globals = window.module.Globals.prototype;
						
		var unbind_callbacks = [];



		var Models = {};
		var Collections = {};
		var Views = {};
		
		
		
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
				collapsed: false,
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
				
				var _this = this;
				
				this.render_loading();
				jQuery.background_ajax({
					data: {
						vc_method: 	'manual_add_find_tables',
						pgla_id: 	this.model.get('id'),
						tv_id:		this.model.get('tv_id')
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
						
						
						
						// Find the prices of tables for the day
						// --------------------------------------------------------------------
						//array of unique day prices
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
						
						
						
						
						var template = EVT['guest_lists/gl_manual_add_table'];
						var html = new EJS({
							text: template
						}).render({
							day_prices: day_prices,
							venue: 		venue,
							pgla_day: 	pgla_day
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
				'click a[data-action]': 'events_click_data_action',
				'change select#table_min_price': 'events_change_select_min_price'
			},
			events_change_select_min_price: function(e){
				
				
				
				var el 		= jQuery(e.currentTarget);
				var value 	= el.val();
				
				this.table_min_spend = value;
				
				var pgla_day 	= this.model.get('pgla_day');
				pgla_day 		= pgla_day.slice(0, -1);
				
				this.$el.find('div.item.table.highlighted').removeClass('highlighted');
				this.$el.find('div.item.table[day-price-' + pgla_day + '=' + value + ']').addClass('highlighted')
						
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
								pgla_id:			_this.model.get('pgla_id'),
								up_id: 				window.page_obj.promoter.up_id,
								group: 				_this.collections_group.toJSON(),
								table_request: 		_this.table_request,
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
						pgla_day: day
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
					
						window.location.hash = first[0].get('pgla_name').replace(' ', '_');
						
					}
					
				}else{
					jQuery(window).trigger('hashchange');
				}
				
				return this;
			},
			events: {
				'click span[data-pgla_id]': 'events_click_active'
			},
			events_click_active: function(e){
				
				var el = jQuery(e.currentTarget);
				var pgla_id = el.attr('data-pgla_id');
				var res = this.collection.where({
					pgla_id: pgla_id
				});
				
				if(res.length){
					this.collection.each(function(m){
						m.set({
							active: false
						});
					});
					
					res[0].set({
						active: true
					});
					
					window.location.hash = res[0].get('pgla_name').replace(' ', '_');
					
					pending_requests_change();
					
				}
				
			},
			change_collection: function(model, event){
				
				if(event.changes && event.changes.active === true){
					
					this.ul.find('span[data-pgla_id]').css({
						'font-weight': 'normal',
						color: 'black'
					});
					
					this.ul.find('span[data-pgla_id=' + model.get('pgla_id') + ']').css({
						'font-weight': 'bold',
						color: 'blue'
					});
					
					var img_style = {
						'max-width': '200px',
						border: '1px solid #CCC'
					};
					var img_base = window.module.Globals.prototype.s3_uploaded_images_base_url;
					this.gl_img.attr('src', 	img_base + 'guest_lists/' 		+ model.get('pgla_image') 	+ '_p.jpg').css(img_style);
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
								pgla_id: 	_this.model.get('pgla_id')
							},
							success: function(data){
								
								if(data.success){
									status.html('<span style="color:blue;">' + data.message.status + '</span>');
									_this.$el.find('span#glas_last_updated').html(data.message.human_date);
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
			render: function(){
				
				var _this = this;
				var template = EVT['guest_lists/gl_reservations_table'];
				
				
				var active_list = this.collection.where({
					active: true
				});
				if(!active_list.length)
					return false;
					
					
					
				active_list = active_list[0];
				this.active_list = active_list;

				//insert list status
				var view_status = new Views.Status({
					model: this.active_list
				});		
				
				
				var html = new EJS({
					text: template
				}).render(active_list.toJSON());
				this.$el.html(html);
				
				
				//this.$el.tabs();
				this.$el.find('input.guest_list_datepicker').datepicker({
					dateFormat: 	'DD m/d/y',
					maxDate: 		'+6d',
				//	minDate: 		'-3y',
					beforeShowDay: 	function(date){
						
						var cal_day, pgla_day, pgla_day_int;
						
						cal_day = date.getDay();
    					pgla_day = _this.active_list.get('pgla_day');
    											
						switch(pgla_day){
							case 'sundays':
								pgla_day_int = 0;
								break;
							case 'mondays':
								pgla_day_int = 1;
								break;
							case 'tuesdays':
								pgla_day_int = 2;
								break;
							case 'wednesdays':
								pgla_day_int = 3;
								break;
							case 'thursdays':
								pgla_day_int = 4;
								break;
							case 'fridays':
								pgla_day_int = 5;
								break;
							case 'saturdays':
								pgla_day_int = 6;
								break;
						}
						
						if(cal_day != pgla_day_int){
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
						var pgla_day, pgla_day_int, pgla_next_occurance_date, datepicker_selected_date, weeks_apart;
						
						pgla_day = _this.active_list.get('pgla_day');
    											
						switch(pgla_day){
							case 'sundays':
								pgla_day_int = 0;
								break;
							case 'mondays':
								pgla_day_int = 1;
								break;
							case 'tuesdays':
								pgla_day_int = 2;
								break;
							case 'wednesdays':
								pgla_day_int = 3;
								break;
							case 'thursdays':
								pgla_day_int = 4;
								break;
							case 'fridays':
								pgla_day_int = 5;
								break;
							case 'saturdays':
								pgla_day_int = 6;
								break;
						}
						
						pgla_next_occurance_date = nextDay(pgla_day_int);
						datepicker_selected_date = jQuery(this).datepicker('getDate');

						//number of weeks apart
						weeks_apart = Math.abs(Math.round((datepicker_selected_date - pgla_next_occurance_date) / 1000 / 60 / 60 / 168));
						_this.fetch_week(weeks_apart);
								        
					}
				});
						
				var tbody  = this.$el.find('tbody');
				var groups = this.active_list.get('groups');
				if(!groups.length){
					
					var html = new EJS({
						text: EVT['guest_lists/gl_tr_no_reservations']
					}).render({});
					tbody.html(html);
					return this;
					
				}

				this.collection_reservations = new Collections.Reservations(groups);
				this.collection_reservations.each(function(m){
					
					var view_reservation = new Views.Reservation({
						model: m
					});
					tbody.append(view_reservation.el);
					view_reservation.render();
					
				});
				
				
				tbody.find('> tr:odd').addClass('odd');
				
				
				this.custom_events_add_fb_data();
				
				return this;		
				
			},
			events: {
				'click a[data-action]': 	'events_click_data_action',
				'custom-event-add-fb-data': 'custom_events_add_fb_data'
			},
			fetch_week: function(weeks_apart){
				
				var _this 	= this;
				var pgla_id = this.active_list.get('pgla_id');				
				
				jQuery.background_ajax({
					data: {
						vc_method: 		'retrieve_guest_lists',
						pgla_id: 		pgla_id,
						weeks_offset: 	weeks_apart
					},
					success: function(data){
														
						if(data.success){
							
							var weekly_guest_list = data.message.weekly_guest_lists;
							for(var i in weekly_guest_list){
								weekly_guest_list = weekly_guest_list[i];
								break;
							}
							
							console.log(data);
							_this.active_list.set(weekly_guest_list);
							
							window.page_obj.users = data.message.users;
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
				
				if(this.users === null){
					
					//add fb queried names
					jQuery.fbUserLookup(window.page_obj.users, '', function(rows){
						_this.users = rows;
						fb_names(rows);
					});
					
				}else{
					fb_names(this.users);
				}
						
						
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
					
						if(this.collection_reservations)
							this.collection_reservations.each(function(m){
								
								console.log(m);
								m.set({
									collapsed: !m.get('collapsed')
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
				if(Math.abs(timestamp - parseInt(this.model.get('time'))) < 10)
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
						vc_method: 		'update_promoter_reservation_host_notes',
						pglr_id: 		_this.model.get('id'),
						host_message:	new_notes
					},
					success: function(data){
						
						//_this.$el.find('img.message_loading_indicator').hide();
						
						if(data.success){
							
							if(new_notes == _this.model.get('pglr_host_message')){
								_this.model.trigger('change');
								_this.$el.trigger('custom-event-add-fb-data');
								return;
							}
							
							_this.model.set({
								pglr_host_message: new_notes
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
		
		
	
				
		var collection_guest_lists = new Collections.GuestLists(window.page_obj.weekly_guest_lists);
		var view_left_menu = new Views.LeftMenu({
			collection: collection_guest_lists,
			el: '#left_menu'
		});
		var view_guest_list = new Views.GuestList({
			collection: collection_guest_lists,
			el: '#lists_container'
		});





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
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			team_chat_object.individual_channel.unbind('pending-requests-change', pending_requests_change);
			jQuery(window).unbind('hashchange', hash_change_callback)
			
		}


		return;

/*



		jQuery('.tabs').tabs();
		jQuery('div.datepicker').datepicker();
		
		jQuery('input.guest_list_datepicker').datepicker({
			dateFormat: 'DD MM d, yy',
			maxDate: '+6d',
			minDate: '-3y',
			onSelect: function(dateText, inst){
				
				var dateObj = {
		        	currentYear: inst.selectedYear,
		        	currentMonth: inst.selectedMonth,
		        	currentDay: inst.selectedDay
		        }
		        
			}
		});
		
		
*/
		
		
		
		
		
		
		
		
		
		
		var display_approve_deny_dialog = function(ui_element){
			
			var pglr_id = jQuery(ui_element).parent().parent().find('td.pglr_id').html();
			var pglr_head_user = jQuery(ui_element).parent().parent().find('td.pglr_head_user').html();
			
			var fql_user;
			//find head user in vc_fql_users
			for(var i = 0; i < vc_fql_users.length; i++){
				
				if(vc_fql_users[i].uid == pglr_head_user){
					fql_user = vc_fql_users[i];
					break;
				}
				
			}
					
			jQuery('div#dialog_actions img.pic_square').attr('src', fql_user.pic_square);
			jQuery('div#dialog_actions span.name').html(fql_user.name);
							
			jQuery('div#dialog_actions').dialog({
				title: 'Approve or Decline Request',
				height: 420,
				width: 320,
				modal: true,
				resizable: false,
				draggable: false,
				buttons: [{
					text: 'Approve',
					id: 'ui-approve-button',
					click: function(){
						app_dec_function(true);
					}
				},{
					text: 'Decline',
					click: function(){
						app_dec_function(false);
					}
				}]
			});
			
			var app_dec_function = function(approved){
			
				var element1 = jQuery(ui_element).parent().parent().find('table.user_messages td.response_message');
				
				jQuery('div#dialog_actions').find('div#dialog_actions_loading_indicator').css('display', 'block');
				
				//cross-site request forgery token, accessed from session cookie
				//requires jQuery cookie plugin
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				var message = jQuery.trim(jQuery('div#dialog_actions textarea[name = message]').val());
				
				jQuery.ajax({
					url: window.location,
					type: 'post',
					data: {
							ci_csrf_token: cct,
							vc_method: 'list_request_app_dec',
							app_dec: approved,
							pglr_id: pglr_id,
							message: message
							},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
						if(data.success){
							
							jQuery('div#dialog_actions').find('div#dialog_actions_loading_indicator').css('display', 'none');
							jQuery('div#dialog_actions').find('textarea').val('');
							
							jQuery(ui_element).replaceWith((approved) ? '<span style="color: green;">Approved</span>' : '<span style="color: red;">Declined</span>');
													
							if(message.length > 0)
								element1.html(message);
							
							jQuery('div#dialog_actions').dialog('close');
							
							
						}
						
					},
					failure: function(){
						console.log('failure');
					}
				});
				
			};
			
		};
		
		jQuery('span.app_dec_action').live('click', function(){
			display_approve_deny_dialog(this);
		});
		
		
		
		
		
		
		
		
		jQuery('ul.sitemap li').bind('click', function(){
		
			jQuery('ul.sitemap li').css('font-weight', 'normal');
			jQuery(this).css('font-weight', 'bold');
		
			var pgla_id = jQuery(this).find('span.pgla_id').html();
			jQuery('div#lists_container div.list').css('display', 'none');
			
			jQuery('div#pgla_' + pgla_id).css('display', 'block');
			
			//show relevant gl status box
			jQuery('div.gl_status').css('display', 'none');
			jQuery('div.gl_status_' + pgla_id).css('display', 'block');
						
		});
		
		
		
		
		
		
		
		
		
		
		
		
		fbEnsureInit(function(){
			
			console.log('okay fb init');
			console.log(window.page_obj.users);
			
		
			//Display the first guest list by default
			var display_first = function(){
				jQuery('div#loading_indicator').remove();
				jQuery('div#guest_list_content').css('display', 'block');
			
				//display first guest list by default
				var list1_id = jQuery('ul.sitemap li:first span.pgla_id').html();
				
				jQuery('ul.sitemap').children("li:first").css('font-weight', 'bold'); 
				
				jQuery('div#lists_container div.list').css('display', 'none');
				jQuery('div#pgla_' + list1_id).css('display', 'block');
				
				//show relevant gl status box
				jQuery('div.gl_status').css('display', 'none');
				jQuery('div.gl_status_' + list1_id).css('display', 'block');
			};
			
			//var users = eval('<?= $users ?>');
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
					
					vc_fql_users = rows;
					
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
			
		});
			
	//	manual_add_exclude_ids = eval('<?= $users ?>');
		manual_add_exclude_ids = window.page_obj.users;
		
		
		
		
		
		
		
		
		
		
		
		
		//------- fb friends invite ----------
		jQuery('td.facebook_gl_invite').bind('click', function(){
			
			var table_body = jQuery(this).parent().parent();
			var pgla_id = table_body.find('td.pgla_id').html();
					
			fbEnsureInit(function(){
				
				FB.ui({
					method: 'apprequests',
					message: 'I\'ve added you to my guest list on ClubbingOwl',
					title: 'Add friends to your Guest List',
					max_recipients: 20,
					exclude_ids: manual_add_exclude_ids
				}, function(response){
					
					if(typeof response === 'undefined')
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
								vc_method: 'promoter_list_manual_add',
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
										
																
										console.log(rows);
												
										//populate divs with FB data
										for(var i = 0; i < rows.length; i++){
											
											var table_html = '<tr class="new_add_manual">';
											table_html += '<td class="pglr_id hidden hidden" style="display:none">' + data.message[rows[i].uid].pglr_id + '</td>';
											table_html += '<td class="pglr_head_user hidden" style="display:none">' + rows[i].uid + '</td>';
								
											table_html += '<td><span class="name_' + rows[i].uid + '"></span></td>';
											table_html += '<td><div class="pic_square_' + rows[i].uid + '"></div></td>';
											
											
											table_html += '<td>';
											table_html += '<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
											table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
											table_html += '		<tr><td> - </td></tr>';
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
											table_html += '</table>';
											table_html += '</td>';
											
											table_html += '<td><span style="color:red;">No</span></td>';
											table_html += '<td><span style="color: green;">Approved</span></td>';
											table_html += '<td style="white-space: nowrap; width:244px;"><p>No Entourage</p></td>';
											table_html += '</tr>';
											
											jQuery(table_html).insertBefore(table_body.find('tr:last'));
																	
											jQuery('div#lists_container div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
											jQuery('div#lists_container span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
																					
										}
																			
										table_body.find('tr.waiting').remove();
										
										//increment count
										var count_res = parseInt(jQuery('ul.sitemap li.' + pgla_id + ' span.wgl_groups_count').html());
										count_res += rows.length;
										jQuery('ul.sitemap li.' + pgla_id + ' span.wgl_groups_count').html(count_res);
										
										var offset = jQuery('tr.new_add_manual:last').offset();
										jQuery(document).scrollTop(offset.top - 50);
										
										jQuery('tr.new_add_manual td').show('highlight', { color:'red' }, 1500, function(){
											jQuery('tr.new_add_manual').removeClass('new_add_manual');
											
											window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
											
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
							vc_method: 'promoter_list_manual_add',
							pgla_id: pgla_id,
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
		
		
		
		
		
		(function(){
			
			var characters = 160;
			jQuery("span#dialog_actions_message_remaining").html("You have <strong>" + characters + "</strong> characters remaining");
			
			jQuery("div#dialog_actions textarea[name = message]").keyup(function(){
			    if(jQuery(this).val().length > characters){
			        jQuery(this).val(jQuery(this).val().substr(0, characters));
			    }
			        
			    var remaining = characters - jQuery(this).val().length;
				jQuery("span#dialog_actions_message_remaining").html("You have <strong>" + remaining + "</strong> characters remaining");
			});
			
		})();
		
		
		
		
		
		
		
		
		
		
		
		jQuery('div#guest_list_content').bind('click', function(event){
			
			var target = jQuery(event.target);
			if(!target.is('li.host_notes'))
				return;
								
			var _this = event.target;
			var original = jQuery.trim(jQuery(_this).find('span.original').html());
			
			jQuery(_this).find('span.original').css('display', 'none');
			jQuery(_this).find('div.edit').css('display', 'block');
							
			if(original != '<span style="font-weight: bold;">Edit Message</span>')
				jQuery(_this).find('div.edit textarea').val(original);
								
			var characters = 160 - jQuery(_this).find('div.edit textarea').val().length;
			jQuery(_this).find('div.edit span.message_remaining').html("<strong>" + characters + "</strong> char remaining");
			
			jQuery(_this).find('div.edit textarea').keyup(function(){
			    if(jQuery(_this).val().length > 160){
			        jQuery(_this).val(jQuery(_this).val().substr(0, 160));
			    }
			        
			    var remaining = 160 - jQuery(_this).val().length;
				jQuery(_this).parent().parent().find('span.message_remaining').html("<strong>" + remaining + "</strong> char remaining");
			});
			
			jQuery(_this).find('div.edit textarea').focus();
			jQuery(_this).die('click');
			
		});
		
		
		
		
		
	
		var submit_host_message = function(me){
			var new_text = jQuery.trim(jQuery(me).val());
			var parent = jQuery(me).parent().parent()
			
			if(parent.find('div.edit').css('display') != 'block')
				return;
			
			parent.find('div.edit').css('display', 'none');
			parent.find('span.original').css('display', 'none');
			parent.find('img.message_loading_indicator').css('display', 'block');
			
			var pglr_id = parent.parent().parent().parent().parent().parent().find('td.pglr_id').html();
				
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					ci_csrf_token: cct,
					vc_method: 'update_promoter_reservation_host_notes',
					pglr_id: pglr_id,
					host_message: new_text
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					if(data.success){
						
						parent.find('div.edit textarea').unbind('blur');
						parent.find('div.edit textarea').unbind('keydown');							
						
						if(new_text.length == 0){
							parent.find('span.original').html('<span style="font-weight: bold;">Edit Message</span>');
						}else{
							parent.find('span.original').html(new_text);
						}
						parent.find('span.original').css('display', 'block').focus();
						parent.find('img.message_loading_indicator').css('display', 'none');
						parent.live('click', host_notes_click);
					}
					
				}
			});	
		};
		
		
		
		
		var callback1 = function(){
			submit_host_message(this);
		}
		jQuery('td.host_notes textarea').live('blur', callback1);
		unbind_callbacks.push(function(){
			console.log('unbind 1');
			jQuery('td.host_notes textarea').die('blur', callback1);
		});
		
		
		
		
		var callback2 = function(){
			if(e.keyCode == 13){
				
				e.preventDefault();
				submit_host_message(this);
			}
		}
		jQuery('td.host_notes textarea').live('keydown', callback2);
		unbind_callbacks.push(function(){
			console.log('unbind 2');
			jQuery('td.host_notes textarea').die('keydown', callback2);
		});
		















	
		
	}
	
});