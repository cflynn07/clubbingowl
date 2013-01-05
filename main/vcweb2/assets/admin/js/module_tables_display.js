(function(globals){
	
	var EVT = window.ejs_view_templates_admin_promoters || window.ejs_view_templates_admin_managers;
	
	var Factor 			= 0.5;
	var BaseFloorWidth 	= 800;
	var BaseFloorHeight = 600;
	
	var Models 		= {};
	var Collections = {};	
	var Views 		= {};
	
	
	Models.DisplaySettings = {
		initialize: function(){
			
		},
		defaults: function(){
			
			return {
				factor: 			Factor,
				baseFloorHeight: 	BaseFloorHeight,
				baseFloorWidth: 	BaseFloorWidth
			};
			
		}
	}; Models.DisplaySettings = Backbone.Model.extend(Models.DisplaySettings);


	
	Models.FloorItem = {
		initialize: function(){
			
		},
		defaults:{
			
		}
	}; Models.FloorItem = Backbone.Model.extend(Models.FloorItem);
	
	
	
	Models.Floor = {
		initialize: function(){
			
		},
		defaults:{
			
		}
	}; Models.Floor = Backbone.Model.extend(Models.Floor);
	
	
	
	Models.TeamVenue = {
		initialize: function(){
			
		},
		defaults:{
			
		}
	}; Models.TeamVenue = Backbone.Model.extend(Models.TeamVenue);
	
	
	
	
	
	
	
	
	Models.Reservation = {
		initialize: function(){
			
		},
		defaults:{
			
		}
	}; Models.Reservation = Backbone.Model.extend(Models.Reservation);
	
	Collections.Reservations = {
		model: Models.Reservation,
		initialize: function(){
			
		}
	}; Collections.Reservations = Backbone.Collection.extend(Collections.Reservations);	
	
	
	
	
	
	
	
	
	Collections.FloorItems = {
		model: Models.FloorItem,
		initialize: function(){
			
		}
	}; Collections.FloorItems = Backbone.Collection.extend(Collections.FloorItems);
	
	Collections.Floors = {
		model: Models.Floor,
		initialize: function(){
			
		}
	}; Collections.Floors = Backbone.Collection.extend(Collections.Floors);
	
	Collections.Reservation = {}; Collections.Reservation = Backbone.Collection.extend(Collections.Reservation);
	
	
		
	
	
	Views.FloorItem = {
		initialized: 		false,
		reservation: 		null,
		display_settings: 	null,
		className: 			'item',
		initialize: function(){
			
			model_display_settings.on('change', this.render, this);
			collection_reservations.on('reset', this.render, this);
		
			
			
		},
		render: function(){
			
			var isodate_to_date = function(isodate){
			
				var t = isodate.split(/[-]/);
				var d = new Date(t[0], t[1]-1, t[2], 0, 0, 0);
				return d;
				
			}
			
			var date 	= isodate_to_date(current_manual_date);
			var day		= date.getDay();
			
			
			var factor 		= model_display_settings.get('factor');
			var vlfi_pos_x 	= this.model.get('vlfi_pos_x');
			var vlfi_pos_y 	= this.model.get('vlfi_pos_y');
			var vlfi_width 	= this.model.get('vlfi_width');
			var vlfi_height	= this.model.get('vlfi_height');
			var item_type	= this.model.get('vlfi_item_type');
			var _this 		= this;
			
			this.$el.css({
				left: 	Math.floor(vlfi_pos_x * factor) 	+ 'px',
				top: 	Math.floor(vlfi_pos_y * factor) 	+ 'px',
				width: 	Math.floor(vlfi_width * factor) 	+ 'px',
				height: Math.floor(vlfi_height * factor) 	+ 'px'
			});
			this.$el.addClass(item_type);
			
			
			
			var template = EVT['tables/t_vlf_item'];
			
			var html = new EJS({
				text: template
			}).render(jQuery.extend({
				day: day
			}, this.model.toJSON()));
			
			this.$el.html(html);
			
			this.model.set({
				highlighted: false
			});
			
			
			
			if(item_type == 'table'){
				
					
				this.$el.droppable({
					hoverClass: 'highlighted',
					drop: function(e, obj){
						
						var el = jQuery(obj.draggable);
						
						var params 			= {};
						
						if(el.data('pglr_id'))
							params.pglr_id		= el.data('pglr_id');
						
						if(el.data('tglr_id'))
							params.tglr_id		= el.data('tglr_id');
						
						
						params.vc_method	= 'reservation_reassign';
						params.vlfit_id 	= _this.model.get('vlfit_id');
						params.iso_date 	= current_manual_date || model_team_venue.get('floorplan_iso_date')
						params.tv_id		= model_team_venue.get('tv_id');
						
						jQuery.background_ajax({
							data: 		params,
							success: 	function(data){
								
								console.log(data);
								refresh_table_layout(params.tv_id, params.iso_date);
								
							}
						});
												
						_this.$el.addClass('highlighted');
						
					}
				});
					
					
				//determine if this table exists in the pool of reserved tables
				var results = collection_reservations.where({
					vlfit_id: this.model.get('vlfit_id')
				});
							
				
				
				if(results.length){
					
					this.reservation = results[0];
					
					this.model.set({
						highlighted: true
					});		
					
					var _this = this;
					
					
					this.$el.bind('hover', function(e){
												
						if(e.type == 'mouseenter'){
						
							globals.module_reservation_display.display({
								el: _this.$el,
								reservation: _this.reservation,
								model_display_settings: model_display_settings
							});
						
						}						
						
					});
					
										
					console.log('this.reservation');
					console.log(this.reservation);
					
					var res_oauth_uid = this.reservation.get('pglr_user_oauth_uid') || this.reservation.get('tglr_user_oauth_uid');
					if(res_oauth_uid){
						this.$el.append('<img style="" src="https://graph.facebook.com/' + res_oauth_uid + '/picture" alt="" />');
					}else{
						this.$el.append('<img style="" src="' + window.module.Globals.prototype.admin_assets + 'images/icons/small_icons/People.png" alt="" />');
					}
					
					
					
					
					this.$el.find('img').data({
						vlfit_id: 	this.model.get('vlfit_id'),
						tglr_id:	this.reservation.get('tglr_id'),
						pglr_id:	this.reservation.get('pglr_id')						
					});
					
					
					
					
					
					this.$el.find('img').draggable({
						start: function(){
							
							globals.module_reservation_display.remove();
							
							
						},
						helper: function(){
							//this must return a helper dom element?
							
							var width = jQuery(this).width();
							var vlf = jQuery(this).parents('.vlf');
							var el = jQuery(this).clone();
							el.css({
								width: width
							});
							vlf.append(el);
							
							return el;
							
						},
						revert: 'invalid',
					
						zIndex: 10000,
						stop: function(){
							
							jQuery(this).parent('.table').addClass('highlighted');
							
						}
					}).css({
						'max-width': 	'50%',
						'border': 		'1px solid #CCC',
						'position': 	'absolute',
						'bottom': 		'5px',
						'right': 		'5px',
						'cursor':  		'move'
					})
					
									
				}else{
					this.$el.unbind('hover');
				}
				
			}
			
			
			
			if(this.model.get('highlighted'))
				this.$el.addClass('highlighted');
			else 
				this.$el.removeClass('highlighted');
				
				
				
			if(!this.initialized){
				
				var days = [
					'monday',
					'tuesday',
					'wednesday',
					'thursday',
					'friday',
					'saturday',
					'sunday'
				];
				
				for(var i in days){
					var key 	= 'day-price-' + days[i];
					var value 	= this.model.get('vlfit_' + days[i] + '_min');
					this.$el.attr(key, value);
				}
				this.initialized = true;
				
			}
			
		
			/*
			 <div class="day_price monday">US$ <?= number_format($item->vlfit_monday_min, 		0, '', ',') ?></div>
			<div class="day_price tuesday">US$ <?= number_format($item->vlfit_tuesday_min, 		0, '', ',') ?></div>
			<div class="day_price wednesday">US$ <?= number_format($item->vlfit_wednesday_min, 	0, '', ',') ?></div>
			<div class="day_price thursday">US$ <?= number_format($item->vlfit_thursday_min, 	0, '', ',') ?></div>
			<div class="day_price friday">US$ <?= number_format($item->vlfit_friday_min, 		0, '', ',') ?></div>
			<div class="day_price saturday">US$ <?= number_format($item->vlfit_saturday_min, 	0, '', ',') ?></div>
			<div class="day_price sunday">US$ <?= number_format($item->vlfit_sunday_min, 		0, '', ',') ?></div>
			<div class="max_capacity"><?= $item->vlfit_capacity ?></div>
			 * */
			
			
		
						
			return this;
		},
		events: {
			'highlighted': 'highlighted',
			'de-highlighted': 'de_highlighted'
		},
		highlighted: function(){
			
			console.log('highlighted');
			
			this.model.set({
				highlighted: true
			});
			this.render();
		},
		de_highlighted: function(){
			
			console.log('de-highlighted');
			
		//	this.model.set({
		//		highlighted: false
		//	});
		//	this.render();
		}
	}; Views.FloorItem = Backbone.View.extend(Views.FloorItem);
	
	Views.Floor = {
		initialized: false,
		items_collection: null,
		className: 'vlf',
		initialize: function(){
			
			model_display_settings.on('change', this.render, this);
			
		},
		render: function(){
			
			//set the dimensions of this floor item
			var factor 				= model_display_settings.get('factor');
			var base_floor_height 	= model_display_settings.get('baseFloorHeight');
			var base_floor_width 	= model_display_settings.get('baseFloorWidth');
			var _this = this;
			var floor_items 		= this.model.get('items');
			var template 			= EVT['tables/t_vlf'];
			
			
			this.$el.css({
				width: 	Math.floor(base_floor_width  * factor) + 'px',
				height: Math.floor(base_floor_height * factor) + 'px',
			});
			
			if(!this.initialized){
			
				var template = EVT['tables/t_vlf'];
				var html = new EJS({
					text: template
				}).render(this.model.toJSON());
				
				this.$el.html(html);
				
				//insert the items into the floor
				
				if(floor_items && floor_items.length){
					
					var items_collection  = new Collections.FloorItems(floor_items);
					this.items_collection = items_collection;
					items_collection.each(function(m){
						
						var view_item = new Views.FloorItem({
							model: m
						});
						_this.$el.append(view_item.el);
						view_item.render();
						
					});
				
				}
			
				this.initialized = true;
			}
			
			
			
			return this;
			
		},
		events: {
			
		}
	}; Views.Floor = Backbone.View.extend(Views.Floor);
	
	Views.VenueLayout = {
		initialized: false,
		attributes: function(){
			return {
				'class': 'vl'
			};
		},
		initialize: function(){
			
			model_display_settings.on('change', this.render, this);
			
			//why the hell do I have to do this?
			this.$el.addClass('vl');
			this.render();
		},
		render: function(){
			
			if(!this.initialized){
				
				var _this = this;
			
				//add each floor into this area
				this.collection.each(function(m){
					
					var view_floor = new Views.Floor({
						model: 				m,
						display_settings: 	this.display_settings
					});
					_this.$el.append(view_floor.el);
					view_floor.render();
					
				});
				
				this.initialized = true;
				
			}else{
				//do nothing...
			}
			
			
			return this;			
		},
		events: {
			
		}
	}; Views.VenueLayout = Backbone.View.extend(Views.VenueLayout);
	




	Views.VenueLayoutWrapper = {
		collection_floors: null,
		initialize: function(){
			
			this.model.on('change', this.render, this);
			this.render();
			
		},
		render: function(){
					
			var template = EVT['tables/t_wrapper']; 
			
			var html = new EJS({
				text: template
			}).render(jQuery.extend(
				this.model.toJSON(), ui_tables_options
			));
			
			this.$el.html(html);
			
			
			
			if(ui_tables_options.display_slider)
				this.$el.find('#slider-' + this.model.get('tv_id')).slider({
					value: 	Math.floor(model_display_settings.get('factor') * 100),
					min: 	30,
		            slide: 	function(event, ui){
		            	
		            	var val = ui.value / 100;
		            	model_display_settings.set({
		            		factor: val
		            	});
		            	
		            	//set all other sliders on page?
		            	jQuery('div[data-function=tv_size_slider]').slider('value', ui.value);
		            	
		            }
				});
			
			
			
			
			var venue_floorplan = this.model.get('venue_floorplan');
			if(venue_floorplan && venue_floorplan.length){
				
				var _this 				= this;
				
				if(!this.collection_floors){
					
					var collection_floors 	= new Collections.Floors(venue_floorplan);
					this.collection_floors 	= collection_floors;
					
					var venue_layout = new Views.VenueLayout({
						el: this.$el.find('#layout').get(0),
						collection: collection_floors
					});
					
				}else{
					
					var venue_layout = new Views.VenueLayout({
						el: this.$el.find('#layout').get(0),
						collection: collection_floors
					});
					
				}
				
								
			}			
			
			
			return this;			
		},
		events: {
			
		}
	}; Views.VenueLayoutWrapper = Backbone.View.extend(Views.VenueLayoutWrapper);
	
	
	
	
	
	
	
	var refresh_table_layout = function(tv_id, iso_date){
		
			
		var target = cached_target;
		jQuery(target).css({
			opacity: 0.5
		});
		
		
		/**
		 *
			vc_method:find_tables
			tv_id:1
			iso_date:2013-01-04
		 */
		
		jQuery.background_ajax({
			data: {
				vc_method: 	'find_tables',
				tv_id:		tv_id,
				iso_date: 	current_manual_date || iso_date
			},
			success: function(data){
				

				var venue = false;										
				for(var i in data.message.team_venues){
						
					if(parseInt(data.message.team_venues[i].tv_id) == parseInt(tv_id)){
						venue = data.message.team_venues[i];
						break;
					}
				}	
									
				if(!venue)
					return false;
					
					
					
					
																
				if(venue.venue_reservations){
					collection_reservations.reset(venue.venue_reservations);
				}else{
					collection_reservations.reset([]);
				}
				
				
				
				
				
				jQuery(target).css({
					opacity: 1
				});
				
				
				
			}
		});
		

	};
	
	
	
	
	
	
	
	
	
	
	
	//initialize settings module
	var model_display_settings = new Models.DisplaySettings();

	//define models, collections, views instance vars
	var model_team_venue;
	var collection_floors;
	var views_venue_layout_wrapper;
	var collection_reservations;
	
	
	
	var cached_target;
	var cached_tv_id;
	var current_manual_date;
	
	
	
	var ui_tables_options = {
		display_slider: true
	};
	
	
	//public api	
	var module_tables_display = {
		
		
		manual_date: 			function(arg){
			current_manual_date = arg;
		},
		refresh_table_layout: 	refresh_table_layout,
		
		
		
		display_settings: model_display_settings,
		initialize: function(opts){
			
			
			
			if(opts.options){
				jQuery.extend(ui_tables_options, opts.options);
			}
			
			cached_target = opts.display_target;
			
			if(opts.team_venue){
				
				//if it's an object, convert to array for backbone
				if(typeof opts.team_venue.venue_floorplan === 'object'){
					var floorplan = [];
					for(var i in opts.team_venue.venue_floorplan){
						floorplan.push(opts.team_venue.venue_floorplan[i]);
					}
					opts.team_venue.venue_floorplan = floorplan;
				}
				
				
				if(opts.factor)
					model_display_settings.set({
						factor: opts.factor
					});
				
				
				//collection_floors = new Collections.Floors(opts.floors);
				model_team_venue		= new Models.TeamVenue(opts.team_venue);
				current_manual_date		= model_team_venue.get('floorplan_iso_date');
				
				
				var venue_reservations 	= model_team_venue.get('venue_reservations');
				if(venue_reservations){
					collection_reservations = new Collections.Reservations(model_team_venue.get('venue_reservations'));
				}else{
					collection_reservations = new Collections.Reservations([]);
				}
				
				
				views_venue_layout_wrapper 	= new Views.VenueLayoutWrapper({
					el: 				opts.display_target,
					model:				model_team_venue
				});
				
				
				
			}else{
				//pull from server
				
				if(!opts.tv_id){
					return false;
				}
				
				//use opts.tv_id...
				
				
				
			}			
			
		}
	};
	
	globals.module_tables_display = module_tables_display;

}(window.module.Globals.prototype));
