if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_clients_individual = function(){


		var Models 		= {};
		var Collections = {};
		var Views 		= {};


		Models.Client = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.Client = Backbone.Model.extend(Models.Client);
		
		Views.Client = {
			el: '#clients_individual_wrapper',
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				this.$el.find('*[data-client-name]').html(this.model.get('u_full_name'));
				this.$el.find('*[data-phone-number]').html(this.model.get('u_phone_number').replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3'));
				this.$el.find('*[data-email]').html(this.model.get('u_email'));
				this.$el.find('img#client_pic').attr({
					src: 'https://graph.facebook.com/' + this.model.get('u_oauth_uid') + '/picture?type=large'
				});
				
				this.$el.find('textarea.notes').cleditor({
					width: 1025,
					height: 200
				});
					
				
			},
			events: {
				
			}
		}; Views.Client = Backbone.View.extend(Views.Client);
		

		var model_client = new Models.Client(window.page_obj.client);
		var view_client = new Views.Client({
			model: model_client
		});


		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}
		
	}
	
});