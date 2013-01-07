if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_tables = function(){
		
		var EVT 				= window.ejs_view_templates_admin_managers;
		var globals 			= window.module.Globals.prototype;
		var datepicker;
							
							
							
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
				
				console.log(this.model.toJSON());
				return this;
				
				
				
				var html = new EJS({
					text: template
				}).render(jQuery.extend({
					collapsed: false
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
				
				if(this.options.subtype == 'tables'){
					
					var table_reservations = this.collection.filter(function(m){
						var vlfit_id = m.get('vlfit_id');
						return (vlfit_id != undefined && vlfit_id != null && vlfit_id != 'null')
							 && (m.get('tv_id') == _this.options.tv_id);			
					});
					
					_.each(table_reservations, function(m){
						
						var view = new Views.Reservation({
							model: m
						});
						
						_this.$el.find('tbody').append(view.el);
						view.render();
						
					});
					
					
				}else if(this.options.subtype == 'all'){
					
					this.collection.each(function(m){
						
						var view = new Views.Reservation({
							model: m
						});
						
						_this.$el.find('tbody').append(view.el);
						view.render();
						
					})
					
				}
							
				return this;
			},
			events: {
				
			}
		}; Views.ReservationsHolder = Backbone.View.extend(Views.ReservationsHolder);
		
		
		
		
		
		
		
		
		var initialize = function(tv){
			
			var team_venues = tv || window.page_obj.team_venues;
			
			//build up a collection of all reservations		
			var collection_reservations = new Collections.Reservations();
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
			
			
			for(var i in team_venues){
				var venue = team_venues[i];
				
				var view_tables = new Views.ReservationsHolder({
					el: 		'#tabs-' + venue.tv_id + '-1 table',
					collection: collection_reservations,
					subtype: 	'tables',
					tv_id:		venue.tv_id
				});			
				
				var view_all = new Views.ReservationsHolder({
					el: 		'#tabs-' + venue.tv_id + '-2 table',
					collection: collection_reservations,
					subtype: 	'all',
					tv_id:		venue.tv_id
				});
				
			}
			
			console.log(collection_reservations.toJSON());
		};
		initialize();
		
		
		
		
		
		
		
		
		
		
		
		jQuery('div#tabs').tabs({}).css('display', 'block').resizable();
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