if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_checkin_categories = function(){
		
		
		var EVT = window.ejs_view_templates_admin_managers;
		var Models 		= {};
		var Collections = {};			
		var Views 		= {};
		
								
		Models.Category = {
			initialize: function(){
				
			},
			defaults: {
				editing: false,
				
			}
		}; Models.Category = Backbone.Model.extend(Models.Category);		
		
		
		

		Collections.Categories = {
			model: Models.Category,
			initialize: function(){
				
			}
		}; Collections.Categories = Backbone.Collection.extend(Collections.Categories);
		
		
		
		
		Views.Category = {
			tagName: 'tr',
			initialize: function(){
				
				this.model.on('change', this.render, this);
				
			},
			render: function(){
				
				var template, editing = this.model.get('editing');
				if(editing){
					template 	= EVT['settings/settings_category_edit'];
				}else{
					template 	= EVT['settings/settings_category'];
				}
				
				var html		= new EJS({
					text: template
				}).render(this.model.toJSON());
				this.$el.html(html);
				
				
				
				if(editing){
					this.$el.find('*[data-key="hcc_amount"]').priceFormat({
						prefix: 			'US$ ',
						limit: 				4,
						thousandsSeparator: ',',
						centsSeparator: 	'',
						centsLimit: 		0
					});
				}
				
				
				return this;
				
			},
			events: {
				'click *[data-action]': 'click_data_action'
			},
			click_data_action: function(e){
				
				e.preventDefault();
				
				var el 		= jQuery(e.currentTarget);
				var action 	= el.attr('data-action');
				var _this 	= this;
				
				switch(action){
					case 'edit':
					
						var res = collections_categories.where({
							editing: true
						});						
						if(res.length)
							return false;
							
							
						this.model.set({
							editing: true
						});
					
						break;
					case 'save':
					
						
						this.model.set({
							hcc_title: 		this.$el.find('*[data-key="hcc_title"]').val(),
							hcc_amount: 	this.$el.find('*[data-key="hcc_amount"]').val(),
							hcc_details: 	this.$el.find('*[data-key="hcc_details"]').val()
						});
						
						
						if(!this.model.get('hcc_title') || !this.model.get('hcc_amount')){
							alert('Please enter a category title & amount');
							return false;
						}
					
					
						el.replaceWith('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" />');
					
					
						jQuery.background_ajax({
							data: {
								vc_method: 	'save_category',
								category: 	_this.model.toJSON()
							},
							success: function(data){
								
								if(data.success){
									
									window.page_obj.categories = data.message
									initialize();
									
								}else{
									
									alert(data.message);
									initialize();
									
								}
								
							}
						});
					
						break;
					case 'delete':
					
						this.model.set({
							hcc_deleted: 1
						});
					
						el.replaceWith('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" />');
					
						jQuery.background_ajax({
							data: {
								vc_method: 	'save_category',
								category: 	_this.model.toJSON()
							},
							success: function(data){
								
								if(data.success){
									
									window.page_obj.categories = data.message
									initialize();
									
								}else{
									
									alert(data.message);
									initialize();
									
								}
								
							}
						});
					
						break;
				}
				
				return false;
				
			}
		}; Views.Category = Backbone.View.extend(Views.Category);
		
		Views.Categories = {
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var _this = this;
				
				if(this.collection.length){
					this.$el.find('tbody').empty();
				}
				
				this.collection.each(function(m){
										
					var category = new Views.Category({
						model: m
					});
					_this.$el.find('tbody').append(category.el);
					category.render();
					
				});
				
			},
			events: {
				
			}
		}; Views.Categories = Backbone.View.extend(Views.Categories);
			
			
			
			
			
		var collections_categories;
		var initialize = function(){
			
			collections_categories 	= new Collections.Categories(window.page_obj.categories);
			var view_categories		= new Views.Categories({
				el: 'table#categories',
				collection: collections_categories
			});	
			
		};
		initialize();
		
		
		
		
		
		
		jQuery('a[data-action="add"]').bind('click', function(e){
			
			e.preventDefault();
			
			var res = collections_categories.where({
				editing: true
			});
			
			console.log(res);
			
			if(res.length)
				return false;
			
			window.page_obj.categories.push({
				editing: true,
				hcc_title: 		'',
				hcc_amount: 	'',
				hcc_details: 	''
			});
			initialize();
			
			return false;
			
		});
		


		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
		}
		
	}
	
});