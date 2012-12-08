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
	
	
	
	
		
	
	
	Views.FloorItem = {
		initialized: false,
		display_settings: null,
		className: 'item',
		initialize: function(){
			
			model_display_settings.on('change', this.render, this);
			
		},
		render: function(){
			
			var factor 		= model_display_settings.get('factor');
			var vlfi_pos_x 	= this.model.get('vlfi_pos_x');
			var vlfi_pos_y 	= this.model.get('vlfi_pos_y');
			var vlfi_width 	= this.model.get('vlfi_width');
			var vlfi_height	= this.model.get('vlfi_height');
			var item_type	= this.model.get('vlfi_item_type');
			
			this.$el.css({
				left: 	Math.floor(vlfi_pos_x * factor) 	+ 'px',
				top: 	Math.floor(vlfi_pos_y * factor) 	+ 'px',
				width: 	Math.floor(vlfi_width * factor) 	+ 'px',
				height: Math.floor(vlfi_height * factor) 	+ 'px'
			});
			this.$el.addClass(item_type);
			
			if(this.model.get('highlighted'))
				this.$el.addClass('highlighted');
			
			var template = EVT['tables/t_vlf_item'];
			
			var html = new EJS({
				text: template
			}).render(this.model.toJSON());
			
			this.$el.html(html);
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
	
	
	
	
	
	
	//initialize settings module
	var model_display_settings = new Models.DisplaySettings();

	//define models, collections, views instance vars
	var model_team_venue;
	var collection_floors;
	var views_venue_layout_wrapper;
	
	
	var ui_tables_options = {
		display_slider: true
	};
	
	
	//public api	
	var module_tables_display = {
		display_settings: model_display_settings,
		initialize: function(opts){
			
			//opts.factor
			//opts.display_target
			
			
			//opts.date
			//opts.team_venue
			//opts.team_venue.venue_floorplan			
			
			if(opts.options){
				jQuery.extend(ui_tables_options, opts.options);
			}
			
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
				model_team_venue			= new Models.TeamVenue(opts.team_venue);				
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
