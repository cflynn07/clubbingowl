if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_checkin_categories = function(){
						
				
		var Models 		= {};
		var Collections = {};			
		var Views 		= {};
		
								
		Models.Category = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.Category = Backbone.Model.extend(Models.Category);		
		
		
		

		Collections.Categories = {
			model: Models.Category,
			initialize: function(){
				
			}
		}; Collections.Categories = Backbone.Collection.extend(Collections.Categories);
		
		
		
		
		Views.Category = {
			initialize: function(){
				
			},
			render: function(){
				
			},
			events: {
				
			}
		}; Views.Category = Backbone.View.extend(Views.Category);
		
		Views.Categories = {
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				
				this.collection.each(function(m){
					
				});
				
			},
			events: {
				
			}
		}; Views.Categories = Backbone.View.extend(Views.Categories);
				


		var collections_categories = new Collections.Categories(window.page_obj.categories);
		var view_categories			= new Views.Categories({
			el: 'table#categories',
			collection: collections_categories
		});

		




		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){

			
			
		}
		
	}
	
});