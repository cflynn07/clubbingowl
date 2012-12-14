if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_host_dashboard = function(){
		
		if(!window.page_obj || !window.page_obj.backbone)
			return false;
		
		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		var EVT 		= window.ejs_view_templates_admin_hosts;
					
		Models.GuestListMember = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.GuestListMember = Models.GuestListMember = Backbone.Model.extend(Models.GuestListMember);
		
		Collections.GuestListMembers = {
			model: Models.GuestListMember,
			initialize: function(){
				
			}
		}; Collections.GuestListMembers = Backbone.Collection.extend(Collections.GuestListMembers);
		
		
		
		
		Models.Promoter = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.Promoter = Backbone.Model.extend(Models.Promoter);
		
		Collections.Promoters = {
			model: Models.Promoter,
			initialize: function(){
				
			}
		}; Collections.Promoters = Backbone.Collection.extend(Collections.Promoters);
		
		
		
		
		Models.TeamVenue = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.TeamVenue = Backbone.Model.extend(Models.TeamVenue);
		
		Collections.TeamVenues = {
			model: Models.TeamVenue,
			initialize: function(){
				
			}
		}; Collections.TeamVenues = Backbone.Collection.extend(Collections.TeamVenues);
		
		
		
		
		
		
		Views.GuestList = {
			initialize: function(){
				
			},
			render: function(){
				
				var html = new EJS({
					text: EVT['guest_list_wrapper']
				}).render(this.model.toJSON());
				this.$el.html(html);
				
				return this;
				
			},
			events: {
				
			}
		}; Views.GuestList = Backbone.View.extend(Views.GuestList);
		
		
		
		Views.ListsWrapper = {
			el: '#lists_wrapper',
			initialize: function(){
				
				
				
				var _this = this;
				var hash_change_callback = function(){
					console.log('hashchange');
					_this.render_active_tv(window.location.hash.replace('#', ''));
				};
				jQuery(window).bind('hashchange', hash_change_callback);
				
				//extend unbind callback
				var temp = window.module.Globals.prototype.unbind_callback;
				window.module.Globals.prototype.unbind_callback = function(){
					jQuery(window).unbind('hashchange', hash_change_callback);
					temp();
				};
				
				this.render();
			},
			render: function(){
				
				var template = EVT['lists_wrapper'];
				var html = new EJS({
					text: 			template
				}).render({
					team_venues: 	collection_team_venues.toJSON()
				});
				
				this.$el.html(html);
				
				//show first venue or #hash indicated venue
				if(window.location.hash){
					var tv_id = window.location.hash.replace('#', '');
					this.$el.find('select#venue_select').val(tv_id);
					this.render_active_tv(tv_id);	
				}else{
					var tv = collection_team_venues.at(0);
					window.location.hash = tv.get('tv_id');
				}
							
				return this;
				
			},
			render_active_tv: function(tv_id){
				
				var active_venue_wrapper = this.$el.find('div#active_venue_wrapper');
				var team_venue = collection_team_venues.where({
					tv_id: tv_id
				});
				if(!team_venue.length)
					team_venue = collection_team_venues.at(0);
				else
					team_venue = team_venue[0];
				
				
				//load_venue_header_view
				var html = new EJS({
					text: EVT['active_tv_header']
				}).render(team_venue.toJSON());
				active_venue_wrapper.html(html);
				
				//Load GL view for each promoter
				collection_promoters.each(function(m){
					
					var view_promoter_gl = new Views.GuestList({
						model: m
					});
					active_venue_wrapper.append(view_promoter_gl.el);
					view_promoter_gl.render();
					
				});
				
				
				//Load House GL view
				
				//Bind the event.
				
				window.location.hash = tv_id;
				return this; 
			},
			events: {
				'change select#venue_select': 'events_change_venue_select'
			},
			events_change_venue_select: function(e){
				console.log('events_change_venue_select');
				
				var el 		= this.$el.find('select#venue_select');
				var tv_id 	= el.val();
			//	this.render_active_tv(tv_id);
				window.location.hash = tv_id;
			
				
			}
		}; Views.ListsWrapper = Backbone.View.extend(Views.ListsWrapper);
		
		var collection_team_venues	= new Collections.TeamVenues(window.page_obj.backbone.team_venues);
		var collection_promoters	= new Collections.TeamVenues(window.page_obj.backbone.promoters);
		
		var view_listsWrapper 		= new Views.ListsWrapper({});
		
		
		
		
		
		var hash_change_callback = function(){
			//jQuery('select#venue_select').val(window.location.hash.replace('#', '')).trigger('change');
		};
		jQuery(window).bind('hashchange', hash_change_callback);
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			jQuery(window).unbind('hashchange', hash_change_callback);
		}
		
	}; 

});