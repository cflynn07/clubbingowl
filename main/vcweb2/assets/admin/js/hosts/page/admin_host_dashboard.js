if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	window.vc_page_scripts.admin_host_dashboard = function(){

		var EVT 				= window.ejs_view_templates_admin_hosts;
		var globals 			= window.module.Globals.prototype;
		var datepicker;
		var collapsed			= true;
		
		
		var pusher_team_channel	= window.team_chat_object.pusher.channels.channels['presence-' + window.team_fan_page_id];				
		
		var unbind_events = [];
		jQuery('#notify_container').notify();
		if(jQuery.isMobile()){
			jQuery('#notify_container').addClass('mobile-notifications');
		}
		
		var notify_scroll_callback = function(){
			if(jQuery(window).scrollTop() > 45){
				jQuery('#notify_container').css({
					top: '10px'
				});
			}else{
				jQuery('#notify_container').css({
					top: '45px'
				});
			}
		};
		jQuery(window).scroll(notify_scroll_callback);
		jQuery(window).bind('touchmove', notify_scroll_callback);
		
		
		
							
		if(!window.page_obj.team_venues)
			return;
			
			
		
		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		
		Models.Reservation = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.Reservation = Backbone.Model.extend(Models.Reservation);
		
		
		
		
		
		Collections.Reservations = {
			model: Models.Reservation,
			initialize: function(){
				
			}
		}; Collections.Reservations = Backbone.Collection.extend(Collections.Reservations);
		
		
		
		
		Views.Reservation = {
			tagName: 'tr',
			initialize: function(){
				
				pusher_team_channel.bind('host_events', function(data){
					
					
					
				});
				
			},
			render: function(){
				
				var template = EVT['reservations_overview/ro_reservation'];
				if(this.options.reservations_all)
					template = EVT['reservations_overview/ro_reservation_all'];
				
								
				var html = new EJS({
					text: template
				}).render(jQuery.extend({
					collapsed: collapsed
				}, this.model.toJSON()));
							
				
				this.$el.html(html);
				
				return this;
				
			},
			events: {
				
			}
		}; Views.Reservation = Backbone.View.extend(Views.Reservation);
		
		Views.ReservationsHolder = {
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var _this = this;
				
				
				
				var reservations_all = false;
				var template = EVT['reservations_overview/ro_reservations_table'];
				if(this.options.subtype == 'all' && this.options.tv_id === false){
					template = EVT['reservations_overview/ro_reservations_table_all'];
					reservations_all = true;
				}
				
				
						
				
				
				
				
				
				
				var html = new EJS({
					text: template
				}).render({});
				this.$el.html(html);
				
				
				if(this.options.subtype == 'tables'){
					
					
					var table_reservations = this.collection.filter(function(m){
						
						var vlfit_id 	= m.get('vlfit_id');
					//	var approved 	= ((m.get('pglr_approved') == '1' && m.get('pglr_manager_table_approved') == '1') || m.get('tglr_approved') == '1');
						
						var approved;
						if(m.get('pglr_approved') !== undefined){
							
							if(m.get('pglr_approved') == '1' && m.get('pglr_manager_table_approved') == '1')
								approved = true;
							else
								approved = false;
							
						}else{
							
							if(m.get('tglr_approved') == '1')
								approved = true;
							else
								approved = false;
							
						}
						
						
						
						
						
						
						if(_this.options.tv_id !== false){
							
							var vlfit_id_check 	= (vlfit_id != undefined && vlfit_id != null && vlfit_id != 'null');
							var tv_id_check		= (m.get('tv_id') == _this.options.tv_id);
							var match 			= approved && vlfit_id_check && tv_id_check;
							
							return match;
								 
						}else{
							
							return approved;
							
						}
							 	
					});
					
					
					_.each(table_reservations, function(m){
						
						var view = new Views.Reservation({
							model: 				m,
							reservations_all: 	reservations_all
						});
						
						_this.$el.find('table[data-top_table] > tbody:first').append(view.el);
						view.render();
						
					});
					
					
				}else if(this.options.subtype == 'all'){
					
					
					//uses callbacks defined below
					var all_reservations = all_approved_reservations(this);
					
					
					
					_.each(all_reservations, function(m){
						
						var view = new Views.Reservation({
							model: 				m,
							reservations_all: 	reservations_all
						});
						
						_this.$el.find('table[data-top_table] > tbody:first').append(view.el);
						view.render();
						
					});
					
					
				}
				
				var _this = this;
				jQuery.populateFacebook(this.$el, function(){
					
					_this.$el.find('table[data-top_table]').dataTable({
						bJQueryUI: true,
						bDestroy: 	true,
						bAuthWidth: true
					});
					
					_this.$el.find('table[data-top_table]').css({
						width: '100%'
					});
					
				});
				
				
				return this;
			},
			events: {
				
			}
		}; Views.ReservationsHolder = Backbone.View.extend(Views.ReservationsHolder);
		
		
		
		
		
		
		
		
		
		
		
		
		/**
		 * Helper
		 */
		var all_approved_reservations = function(_this){
						
			var all_reservations = _this.collection.filter(function(m){
										
				var approved;
				if(m.get('pglr_approved') !== undefined){
					//promoter request
					
					if(m.get('pglr_table_request') == '1'){
						approved = (m.get('pglr_approved') == '1' && m.get('pglr_manager_table_approved') == '1');
					}else{
						approved = (m.get('pglr_approved') == '1');
					}
												
				}else{
					//team request
					
					if(m.get('tglr_approved') == '1')
						approved = true;
					else
						approved = false;
					
				}
				
				
				if(_this.options.tv_id !== false){
					
					var tv_id_check		= (m.get('tv_id') == _this.options.tv_id);
					var match 			= approved && tv_id_check;
					
					return match;
						 
				}else{
					
					return approved;
					
				}
				
			});
						
			return all_reservations;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//cached last checkin for updating efficiency
		var last_checkin_val;	
		var reservation_iterator = 0;
		
		Views.ReservationCheckinEnt = {
			tagName: 'tr',
			initialize: function(){
				
				var _this = this;
				var callback = function(data){
								
				
								
					//promoter or team reservation
					if(_this.model.get('pgl_id')){
						
						//Does this event apply to this reservation?
						if(typeof data.pglre_id !== 'undefined' && data.pglre_id == _this.model.get('pglre_id')){
							
							
							switch(data.event){
								case 'check_in':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', true).trigger('change').button('refresh');
									_this.$el.find('select[name=category]').val(data.checkin_category);
								
									jQuery('#notify_container').notify('create', 0, {
										host_oauth_uid: 	504405294,
										host_name: 			'Casey Flynn',
										guest_name: 		'John Doe',
										team_or_promoter:	'Waldos'
									});
								
									break;
								case 'check_out':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', false).trigger('change').button('refresh');
									
									break;
							}
							
						}
						
						
					}else{
						
						//Does this event apply to this reservation?
						if(typeof data.tglre_id !== 'undefined' && data.tglre_id == _this.model.get('tglre_id')){
							
							switch(data.event){
								case 'check_in':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', true).trigger('change').button('refresh');
									_this.$el.find('select[name=category]').val(data.checkin_category);
									
									jQuery('#notify_container').notify('create', 0, {
										host_oauth_uid: 	504405294,
										host_name: 			'Casey Flynn',
										guest_name: 		'John Doe',
										team_or_promoter:	'Waldos'
									});
									
									break;
								case 'check_out':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', false).trigger('change').button('refresh');
									
									break;
							}
							
						}
						
						
					}	
				
									
				
				};
				
				
				pusher_team_channel.bind('host_emit', callback);
				unbind_events.push({
					event: 		'host_emit',
					callback: 	callback
				});
				
				
				
			},
			render: function(){
				
				
				console.log(this.model.toJSON());
				
				var template = EVT['reservations_checkin/reservations_checkin_reservation_entourage'];
				var html = new EJS({
					text: template
				}).render(jQuery.extend({
					collapsed: 				collapsed,
					reservation_iterator: 	reservation_iterator
				}, this.model.toJSON()))
				
				this.$el.html(html);
				reservation_iterator++;
				
				
				this.$el.find('input[type=checkbox]').bind('change', function(e){
					
					var el 		= jQuery(e.currentTarget);
					var checked = el.is(':checked');
				
					if(checked){
				
						el.parents('tr').find('div.additional_checkin_info').css({
							opacity: 1
						});
						el.parents('tr').find('div.additional_checkin_info select').removeAttr('disabled');
				
					}else{
						
						el.parents('tr').find('div.additional_checkin_info').css({
							opacity: 0.4
						});
						el.parents('tr').find('div.additional_checkin_info select').attr('disabled', 'disabled');
					
					}
				
				});
				
				
				
				this.$el.find('input[type=checkbox].checkin_button').button();
				
				
			},
			events: {				
				'change select[name=category]': 			'events_change_select_category',
				'change select[name=additional_guests]': 	'events_change_select_additional_guests',
				'change input.checkin_button': 				'events_change_arrived_checkbox'
			},
			events_change_select_additional_guests: function(e){
				
			},
			events_change_arrived_checkbox: function(e){
				
				var _this 	= this;
				var el 		= jQuery(e.currentTarget);
				var checked 		= el.is(':checked');
				var auto_triggered 	= (el.data('auto_triggered') === true) ? true : false;
				
				if(!auto_triggered)
					el.button('disable').button('refresh');
				
				if(!auto_triggered)
					if(typeof last_checkin_val !== 'undefined' && checked)
						this.$el.find('select[name=category]').val(last_checkin_val);
				
				
				
				var category 			= this.$el.find('select[name=category] 			:selected').val();
				var category_value		= this.$el.find('select[name=category] 			:selected').attr('data-category_value');
				var additional_guests	= this.$el.find('select[name=additional_guests] :selected').val();				
				
				
				if(!auto_triggered)
					jQuery.background_ajax({
						data: {
							vc_method: 			'checkin_event',
							user_type: 			'entourage',
							list_type: 			((_this.model.get('pglr_id') == undefined) ? 'team' : 'promoter'),
							pglr_id: 			_this.model.get('pglr_id'),
							pglre_id: 			_this.model.get('pglre_id'),
							tglr_id: 			_this.model.get('tglr_id'),
							tglre_id: 			_this.model.get('tglre_id'),
							checked: 			checked,
							category: 			category,
							category_value: 	category_value,
							additional_guests: 	additional_guests,
							hcd_id: 			_this.model.get('hcd_id'),
							tv_id: 				_this.model.get('tv_id'),
							socket_id:			window.team_chat_object.pusher.connection.socket_id
						}, 
						success: function(data){
							
							el.button('enable').button('refresh');
							
							if(data.success)
								if(checked){
									_this.model.set({
										hc_id: true
									});
								}else{
									_this.model.set({
										hc_id: null
									});
								}
							

						}
					});
					
					
					
				el.data({
					auto_triggered: false
				});
				
							
			},
			events_change_select_category: function(e){
				
				last_checkin_val = jQuery(e.currentTarget).val();
				
			}
		}; Views.ReservationCheckinEnt = Backbone.View.extend(Views.ReservationCheckinEnt);
		
		
		
		
		
		
		Views.ReservationCheckin = {
			tagName: 'tr',
			initialize: function(){
				
				
				var _this = this;
				var callback = function(data){
								
				
								
					//promoter or team reservation
					if(_this.model.get('pgl_id')){
						
						//Does this event apply to this reservation?
						if(typeof data.pglr_id !== 'undefined' && data.pglr_id == _this.model.get('pglr_id')){
							
							
							switch(data.event){
								case 'check_in':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', true).trigger('change').button('refresh');
									_this.$el.find('select[name=category]').val(data.checkin_category);
									
									jQuery('#notify_container').notify('create', 0, {
										host_oauth_uid: 	504405294,
										host_name: 			'Casey Flynn',
										guest_name: 		'John Doe',
										team_or_promoter:	'Waldos'
									});
									
									break;
								case 'check_out':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', false).trigger('change').button('refresh');
								
									break;
							}
							
						}
						
						
					}else{
						
						//Does this event apply to this reservation?
						if(typeof data.tglr_id !== 'undefined' && data.tglr_id == _this.model.get('tglr_id')){
							
							switch(data.event){
								case 'check_in':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', true).trigger('change').button('refresh');
									_this.$el.find('select[name=category]').val(data.checkin_category);
								
									if(this.model.get('tglr_user_oauth_uid') == NULL){
										
									}else{
										
									}
									
									
									jQuery.fbUserLookup([], '', function(rows){
										
										jQuery('#notify_container').notify('create', 0, {
											host_oauth_uid: 	504405294,
											host_name: 			'Casey Flynn',
											guest_name: 		'John Doe',
											team_or_promoter:	'Waldos'
										});
										
									});
									
								
									break;
								case 'check_out':
								
									_this.$el.find('input.checkin_button').data({auto_triggered: true}).attr('checked', false).trigger('change').button('refresh');
								
									break;
							}
							
						}
						
						
					}	
				
									
				
				};
				
				
				pusher_team_channel.bind('host_emit', callback);
				unbind_events.push({
					event: 		'host_emit',
					callback: 	callback
				});
				
				
			},
			render: function(){
				
				
				
				
				var template = EVT['reservations_checkin/reservations_checkin_reservation'];
				var html = new EJS({
					text: template
				}).render(jQuery.extend({
					collapsed: 				collapsed,
					reservation_iterator:	reservation_iterator
				}, this.model.toJSON()))
				
				this.$el.html(html);
				reservation_iterator++;
				
				
				this.$el.find('input[type=checkbox]').bind('change', function(e){
					
					var el 		= jQuery(e.currentTarget);
					var checked = el.is(':checked');
				
					if(checked){
				
						el.parents('tr').find('div.additional_checkin_info').css({
							opacity: 1
						});
						el.parents('tr').find('div.additional_checkin_info select').removeAttr('disabled');
				
					}else{
						
						el.parents('tr').find('div.additional_checkin_info').css({
							opacity: 0.4
						});
						el.parents('tr').find('div.additional_checkin_info select').attr('disabled', 'disabled');
					
					}
				
				});
				
				
				this.$el.find('input[type=checkbox].checkin_button').button();
				
				
				
			},
			events: {
				'change select[name=category]': 			'events_change_select_category',
				'change select[name=additional_guests]': 	'events_change_select_additional_guests',
				'change input.checkin_button': 				'events_change_arrived_checkbox'
			},
			events_change_select_additional_guests: function(e){
				
			},
			events_change_arrived_checkbox: function(e){
								
				var _this 	= this;
				var el 		= jQuery(e.currentTarget);
				var checked 		= el.is(':checked');
				var auto_triggered 	= (el.data('auto_triggered') === true) ? true : false;
				
				if(!auto_triggered)
					if(typeof last_checkin_val !== 'undefined' && checked)
						this.$el.find('select[name=category]').val(last_checkin_val);
				
				if(!auto_triggered)
					el.button('disable').button('refresh');
				
				var category 			= this.$el.find('select[name=category] 			:selected').val();
				var category_value		= this.$el.find('select[name=category] 			:selected').attr('data-category_value');
				var additional_guests	= this.$el.find('select[name=additional_guests] :selected').val();				
				
				
				if(!auto_triggered)
					jQuery.background_ajax({
						data: {
							vc_method: 			'checkin_event',
							user_type: 			'head_user',
							list_type: 			((_this.model.get('pglr_id') == undefined) ? 'team' : 'promoter'),
							pglr_id: 			_this.model.get('pglr_id'),
							pglre_id: 			_this.model.get('pglre_id'),
							tglr_id: 			_this.model.get('tglr_id'),
							tglre_id: 			_this.model.get('tglre_id'),
							checked: 			checked,
							category: 			category,
							category_value: 	category_value,
							additional_guests: 	additional_guests,
							hcd_id: 			_this.model.get('hcd_id'),
							tv_id: 				_this.model.get('tv_id'),
							socket_id:			window.team_chat_object.pusher.connection.socket_id
						}, 
						success: function(data){
							console.log(data);
							
							if(data.success)
								if(checked){
									_this.model.set({
										hc_id: true
									});
								}else{
									_this.model.set({
										hc_id: null
									});
								}
							
							
							el.button('enable').button('refresh');
							
							
						}
					});
				
				el.data({
					auto_triggered: false
				});
				
							
			},
			events_change_select_category: function(e){
				
				last_checkin_val = jQuery(e.currentTarget).val();
				
				
				
				
			}
		}; Views.ReservationCheckin = Backbone.View.extend(Views.ReservationCheckin);
		
		
		
		
		
		/**
		 * Each promoter + house guest lists == a group
		 */
		Views.ReservationCheckinGroup = {
			className: 'ui-widget ui-widget-content ui-helper-clearfix ui-corner-all full_width',
			initialize: function(){
				
				
			},
			render: function(){
				
				var _this = this;
				var added_oauth_uids = [];
				
				//set up this group holder w/ information from first reservation
				var model = this.collection.first();
				var template = EVT['reservations_checkin/reservations_checkin_group'];
				var html = new EJS({
					text: template
				}).render(model.toJSON());
				this.$el.html(html);
				
				//dont know why i put this here
				this.$el.find('tbody:first').empty();
				
				
				
				
				

				//first loop through and take all head-users
				this.collection.each(function(m){
					//append each tr
					
					var view_reservation_checkin_individual = new Views.ReservationCheckin({
						model: m
					});
					
					_this.$el.find('tbody:first').append(view_reservation_checkin_individual.el);
					view_reservation_checkin_individual.render();
					
					added_oauth_uids.push(m.get('pglr_user_oauth_uid') || m.get('tglr_user_oauth_uid'))
					
				}); 
				
				


				//now go for entourage users
				this.collection.each(function(m){
					
					//check each oauth_uid of each bloke that's already been added to avoid duplicates
					var entourage 	= m.get('entourage');
					var temp 		= m.toJSON();
					delete temp.entourage;
										
					for(var i in entourage){
						
						
						var row_obj 			= jQuery.extend({}, temp, entourage[i]);
						row_obj.ent_reservation = true;
						
						
						if(_.indexOf(added_oauth_uids, entourage[i].oauth_uid) == -1){
							
							var model = new Models.Reservation(row_obj);
						
							var view_reservation_checkin_individual_entourage = new Views.ReservationCheckinEnt({
								model: model
							});
							
							_this.$el.find('tbody:first').append(view_reservation_checkin_individual_entourage.el);
							view_reservation_checkin_individual_entourage.render();
									
							added_oauth_uids.push(entourage[i].oauth_uid);
							
						}
						
						
					}					
							
				});
				
				
				
				
				
				
				
				
			},
			events: {
				
			}
		}; Views.ReservationCheckinGroup = Backbone.View.extend(Views.ReservationCheckinGroup);
		
		
		
		Views.ReservationsCheckinHolder = {
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				this.$el.empty();
				var _this = this;
				
				
				
				
				
				
				//organize all the reservations into groups
				var reservation_groupings = this.collection.groupBy(function(m){
					if(m.get('pglr_id') !== undefined){
						return m.get('up_users_oauth_uid');
					}else{
						return 'team';
					}
				});
				
				
				
				
				
				
				//display no reservations msg
				if(this.collection.length === 0){
					this.$el.html('<h2 style="width:100%; text-align:center; color:#000; margin-top:10px; margin-top: 20px; border-top: 1px dashed #CCC; border-bottom: 1px dashed #CCC; padding: 10px 0 10px 0;">No Reservations</h2>');
				}
				
				
				
				
				
				//turn each array of reservations into a collection
				for(var i in reservation_groupings){
					
					var group 		= reservation_groupings[i];
					var collection 	= new Collections.Reservations(group);
					
					var view_reservation_checkin_group = new Views.ReservationCheckinGroup({
						collection: collection
					});

					_this.$el.append(view_reservation_checkin_group.el);
					view_reservation_checkin_group.render();
							
				};
				
				
				
				//now that all the reservations are laid out, find all oauth_uid's that have been checked in anywhere -- and remove them from places where
				//they appear for a second time since they've already been checked in
				this.remove_duplicates();
								
				jQuery.populateFacebook(this.$el, function(){
						
					_this.$el.find('table.reservations_holder').dataTable({
						bJQueryUI: 		true,
						bDestroy: 		true,
						bAuthWidth: 	true,
						 "aLengthMenu": [
					         [-1, 		20, 50, 100],
					         ["All", 	20, 50, 100]
					     ]
					});
	
	
				
					
					
					
					_this.$el.find('label.ui-button').css({
						'max-width': '150px',
						'min-width': '100px'
					});
					
					
					
					if(jQuery.isMobile()){
						
						_this.$el.find('label.ui-button').each(function(){
							//speed shit up
							new NoClickDelay(this);
						});
						_this.$el.find('div[data-top_min]').each(function(){
							//speed shit up
							new NoClickDelay(this);
						});
						new NoClickDelay(jQuery('#team_chatbox_header_tab').get(0));
						
						
						jQuery('label.ui-button').css({
							padding: '8px 0 8px 0'
						});
						
						jQuery('div[data-top_min]').css({
							padding: '14px 0 14px 0'
						});
						
										
						jQuery('*[data-mobile_font]').each(function(){
							jQuery(this).css({
								'font-size': jQuery(this).attr('data-mobile_font')
							});
						});
						
					}
						
				});
				


				this.$el.find('div[data-top_min]').bind('click', function(){		
					
					if(jQuery(this).find('span.ui-icon-circle-triangle-n').length){
						jQuery(this).find('span.ui-icon-circle-triangle-n').switchClass('ui-icon-circle-triangle-n', 'ui-icon-circle-triangle-s', 0);
					}else{
						jQuery(this).find('span.ui-icon-circle-triangle-s').switchClass('ui-icon-circle-triangle-s', 'ui-icon-circle-triangle-n', 0);
					}
								
					jQuery(this).parents('div.ui-widget:first').find('.dataTables_wrapper').toggle();									
				});
				
				
				
			},
			
			remove_duplicates: function(){
				
				
				
			},
			
			events: {
				
			}
		}; Views.ReservationsCheckinHolder = Backbone.View.extend(Views.ReservationsCheckinHolder);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		jQuery('ul.ui-tabs-nav li').each(function(){
			//speed shit up
			new NoClickDelay(this);
		});
		
		
		
		
		var initialize = function(tv){
			
			//unbind events
			for(var i in unbind_events){
				
				var event_obj = unbind_events[i];
				pusher_team_channel.unbind(event_obj.event, event_obj.callback);
				
			}
			
			
			
			
			
			
			var team_venues = tv || window.page_obj.team_venues;
			
			//build up a collection of all reservations		
			var collection_reservations 	= new Collections.Reservations();
			
			
			for(var i in team_venues){
				var venue = team_venues[i];
								
				var temp = jQuery.extend({}, venue);
				delete temp.venue_all_upcoming_reservations;
				delete temp.venue_floorplan;
				delete temp.venue_reservations;
				
				for(var k in venue.venue_reservations){
			
					var reservation = jQuery.extend({}, temp, venue.venue_reservations[k]);
					
					collection_reservations.add(reservation);
					
				}
				
			}
			
			var views = [];
			
			for(var i in team_venues){
				var venue = team_venues[i];
				
				
				
				
				
				
				
				var view_tables = new Views.ReservationsHolder({
					el: 		'#tabs-' + venue.tv_id + '-1',
					collection: collection_reservations,
					subtype: 	'tables',
					tv_id:		venue.tv_id
				});			
				
				
				
				
				
				var view_check_in = new Views.ReservationsCheckinHolder({
					el: 		'#tabs-' + venue.tv_id + '-2 div[data-checkin_tv=' + venue.tv_id + ']',
					collection: collection_reservations,
					subtype: 	'all',
					tv_id:		venue.tv_id
				});
				
				views.push(view_tables);
				views.push(view_check_in);
				
			}
			
			
			
			
			jQuery('a[data-action="expand-collapse-all"]').unbind('click').bind('click', function(e){
				e.preventDefault();
			
				collapsed = !collapsed;
				
				for(var i in views){
					var view = views[i];
					view.render();
				}
				
				return false;
			});
				
			
			


		};
		initialize();
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		jQuery('div#tabs').tabs({}).css('display', 'block');//.resizable();
		jQuery('div#tabs div.tabs_tables').tabs();
				
		jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
			
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));	
			
			var selected_tv_id = jQuery('select.venue_select option[value=' + jQuery(this).val() + ']').attr('data-tv_id');
			
			
			
			
			jQuery('input.table_datepicker').each(function(){
				if(jQuery(this).hasClass('hasDatepicker'))
					jQuery(this).datepicker('destroy');	
			});
			jQuery('div[data-clear-zone]').empty();
		
			
		
		
			var tv_display_module;
			for(var i in window.page_obj.team_venues){
				
				var venue 				= window.page_obj.team_venues[i];
				if(venue.tv_id != selected_tv_id)
					continue;
				
				tv_display_module 	= jQuery.extend(true, {}, globals.module_tables_display);
				tv_display_module
					.initialize({
						display_target: 	'#tabs-' + venue.tv_id + '-0',
						team_venue: 		venue,
						factor: 			0.5,
						options: {
							display_slider: true
						}
					});
								
				break;
	
			}
			
			
			
			datepicker = jQuery('div#tabs div[data-tv_id=' + selected_tv_id + '] input.table_datepicker').datepicker({
				dateFormat: 'DD MM d, yy',
				maxDate: '+6d',
				minDate: '-3y',
				defaultDate: new Date(),
				onSelect: function(dateText, inst){
										
					var iso_date = jQuery.datepicker.formatDate('yy-mm-dd', jQuery(this).datepicker('getDate'));
					tv_display_module.manual_date(iso_date);
					
					
					jQuery('div[data-checkin_tv="' + selected_tv_id + '"]').html('<div style="width:100%; text-align:center;"><img style="margin:20px auto 15px auto;" src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" /></div>');


					tv_display_module.refresh_table_layout(selected_tv_id, iso_date, function(data){
						
						if(!data.success)
							return;
						
						initialize(data.message.team_venues);
						
					});
					jQuery('#displayed_layout_date').html(jQuery(this).val());
					
		       }
			});
			datepicker.datepicker('setDate', '0 days');			
									
		}).trigger('change');
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			pusher_team_channel.unbind('host_events');
			
			jQuery('div[data-clear-zone]').empty();
			
			jQuery('input.table_datepicker').each(function(){
				if(jQuery(this).hasClass('hasDatepicker'))
					jQuery(this).datepicker('destroy');	
			});
			
			jQuery('div#tabs > div.ui-widget-header select.venue_select').unbind('change');

		}



	}
});