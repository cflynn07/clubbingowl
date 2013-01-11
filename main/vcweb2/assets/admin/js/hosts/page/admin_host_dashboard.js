if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	window.vc_page_scripts.admin_host_dashboard = function(){

		var EVT 				= window.ejs_view_templates_admin_hosts;
		var globals 			= window.module.Globals.prototype;
		var datepicker;
		var collapsed			= false;
							
							
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
					
					
					
					var all_reservations = this.collection.filter(function(m){
						
				//		var approved 	= ((m.get('pglr_approved') == '1' && m.get('pglr_manager_table_approved') == '1') || m.get('tglr_approved') == '1');
						
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
		
		
		
		
		Views.ReservationCheckin = {
			tagName: 'tr',
			initialize: function(){
				
			},
			render: function(){
				
				var template = EVT['reservations_checkin/reservations_checkin_reservation'];
				var html = new EJS({
					text: template
				}).render(jQuery.extend({
					collapsed: collapsed
				}, this.model.toJSON()))
				
				this.$el.html(html);
				
				
				
				
			},
			events: {
				
			}
		}; Views.ReservationCheckin = Backbone.View.extend(Views.ReservationCheckin);
		
		
		
		
		
		
		Views.ReservationCheckinGroup = {
			className: 'ui-widget ui-widget-content ui-helper-clearfix ui-corner-all full_width',
			initialize: function(){
				
				
			},
			render: function(){
				
				var _this = this;
				var model = this.collection.first();
				
				var template = EVT['reservations_checkin/reservations_checkin_group'];
				var html = new EJS({
					text: template
				}).render(model.toJSON());
							
				this.$el.html(html);
				
				
				
				
				
				
				
				this.collection.each(function(m){
					//append each tr
					
					var view_reservation_checkin_individual = new Views.ReservationCheckin({
						model: m
					});
					
					_this.$el.find('tbody:first').append(view_reservation_checkin_individual.el);
					view_reservation_checkin_individual.render();
					
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
				
				
				
				var reservation_groupings = this.collection.groupBy(function(m){
					if(m.get('pglr_id') !== undefined){
						return m.get('up_users_oauth_uid');
					}else{
						return 'team';
					}
				});
				
				
				
				for(var i in reservation_groupings){
					
					var group 		= reservation_groupings[i];
					var collection 	= new Collections.Reservations(group);
					
					
					var view_reservation_checkin_group = new Views.ReservationCheckinGroup({
						collection: collection
					});


					_this.$el.append(view_reservation_checkin_group.el);
					view_reservation_checkin_group.render();
					
				
					
				};
				
				
				
				
				
				
				jQuery.populateFacebook(this.$el, function(){
						
					_this.$el.find('table.reservations_holder').dataTable({
						bJQueryUI: 		true,
						bDestroy: 		true,
						bAuthWidth: 	true
					});
					
					_this.$el.find('input[type=checkbox]').iphoneStyle({
						checkedLabel: 	'Yes',
						uncheckedLabel: 'No'
					});
					
				});
				
				
				
				
				
				
			//	this.$el.sortable();
				
				
			},
			events: {
				
			}
		}; Views.ReservationsCheckinHolder = Backbone.View.extend(Views.ReservationsCheckinHolder);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var initialize = function(tv){
			
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
			
			
			
			
			
			
			
			/*
			var collection_all_reservations = new Collections.Reservations();
			for(var i in window.page_obj.team_venues){
				var venue = window.page_obj.team_venues[i];
				
				var temp = jQuery.extend({}, venue);
				delete temp.venue_all_upcoming_reservations;
				delete temp.venue_floorplan;
				delete temp.venue_reservations;
				
				
				for(var k in venue.venue_all_upcoming_reservations){
				
					var reservation = jQuery.extend({}, temp, venue.venue_all_upcoming_reservations[k]);
					collection_all_reservations.add(reservation);
					
				}
					
			}
			
			var view_all_upcoming = new Views.ReservationsHolder({
				el: 		'#all_upcoming_reservations',
				collection: collection_all_reservations,
				subtype: 	'all',
				tv_id:		false
			});		
			views.push(view_all_upcoming);
			
			*/
			
			
			
			
			
			
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
			
			jQuery('div[data-clear-zone]').empty();
			
			jQuery('input.table_datepicker').each(function(){
				if(jQuery(this).hasClass('hasDatepicker'))
					jQuery(this).datepicker('destroy');	
			});
			
			jQuery('div#tabs > div.ui-widget-header select.venue_select').unbind('change');

		}



	}
});