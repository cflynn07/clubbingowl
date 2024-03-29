if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_guest_lists_edit = function(){
						
		
		var Views = {};
		Views.NewGLForm = {
			el: '#guest_list_new_form',
			ocupload_instance: null,
			image_data: false,
			initialize: function(){
				
				this.$el.find('input.iphone').iphoneStyle();
				this.initialize_ocupload();
				
					
				this.initialize_crop({
					image: 	window.page_obj.guest_list.tgla_image,
					x0:		window.page_obj.guest_list.tgla_x0,
					x1:		window.page_obj.guest_list.tgla_x1,
					y0:		window.page_obj.guest_list.tgla_y0,
					y1:		window.page_obj.guest_list.tgla_y1,
					live: 	true
				});
				
				
				
				
			},
			initialize_ocupload: function(){
				
				var _this_view = this;
				var _this = this;
				
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				//initialize one-click upload for profile picture
		        window.ocupload_instance = jQuery('#upload_new_image').upload({
			        name: 		'file',
			        action: 	window.location.href,
			        enctype: 	'multipart/form-data',
			        params: {
			        	ocupload: 		true,
			        	ci_csrf_token: 	cct
			        },
			        autoSubmit: true,
			        onSubmit: 	function(){
			        	
			        	console.log('onSubmit');
			        	jQuery('#upload_new_image').hide();
			        	jQuery('#ajax_loading_image').show();
			        	
			        },
			        onComplete: function(response){
			        	
			        	console.log('onComplete');
			        	jQuery('#ajax_loading_image').hide();
			        	jQuery('#upload_new_image').hide();
			        	jQuery("input[name=file]").val('');
			         	
			        	
			         	  	
			        	response = jQuery.parseJSON(response);
			        	
			        	if(!response.success){
			        		alert(response.message);
			        		return;
			        	}
			        	
			        	if(crop_object && crop_object.remove)
							crop_object.remove();
			        	
			        	response.image_data.live = false;
			        	window.module.Globals.prototype.unbind_callback();
			        	
			      		_this_view.initialize_crop(response.image_data);
			      		
			       
			        },
			        onSelect: function(){
			        	
			        }
			    });
				
			},
			initialize_crop: function(response){
				
					var _this = this;
					var _this_view = this;
				  	_this.image_data = response;
			        	
		        	var src = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/originals/' + ((!response.live) ? 'temp/' : '') + response.image + '.jpg';
		        	var img = jQuery('<img></img>').attr('src', src).bind('load', function(){ 
		        		console.log('image_loaded'); 
		        		
		        		var _this = this;
		        		
		        		window.crop_object = jQuery(this).imgAreaSelect({
		        			instance: 		true,
		        			handles:		true,
		        			aspectRatio:	'3:4',
		        			show:			true,
		        			persistent:		true,
		        			minWidth: 		188,
							minHeight: 		266,
							aspectRatio: 	'188:266',
							x1: 			response.x0,
							y1:				response.y0,
							x2:				response.x1,
							y2: 			response.y1,
							imageHeight: 	jQuery(this).height(),
							imageWidth: 	jQuery(this).width(),
							onSelectChange: function(img, selection){
										
								_this_view.image_data.x0 = selection.x1;
								_this_view.image_data.y0 = selection.y1;
								_this_view.image_data.x1 = selection.x2;
								_this_view.image_data.y1 = selection.y2;
								
							}
		        		});
		        		
		        		
		        	});
		        	jQuery('div#image_holder').html(img);
				
			},
			render: function(){
				
			},
			display_error: function(message){
				
				this.$el.find('#display_message').html(message);
				
			},
			events: {
				'submit form$guest_list_new_form': 		'form_submit',
				'click input#submit_new_guest_list': 	'form_submit',
				'keyup input[name=guest_list_name]': 	'change_input_guest_list_name'
			},
			change_input_guest_list_name: function(e){
				
				var el = jQuery(e.currentTarget);
				
			//	if(el.val().length !== el.val().replace(/\W/g, '').length)
			//		this.$el.find('p#guest_list_name_error').show();
			//	else
			//		this.$el.find('p#guest_list_name_error').hide();
								
								
			},
			form_submit: function(e){
				
				var _this = this;
				
				console.log(jQuery(e.currentTarget).find('input[type=file]').length);
				if(jQuery(e.currentTarget).find('input[type=file]').length)
					return true;
				
				e.preventDefault();
				
				var form_object = this.$el.serializeObjectPHP();
				
				
		
				
				if(!form_object.guest_list_description){
					this.display_error('Please supply a guest list description.');
					return false;
				}
				
				if(!form_object.guest_list_reg_cover){
					this.display_error('Please supply a regular cover charge.');
					return false;
				}
				
				if(!form_object.guest_list_gl_cover){
					this.display_error('Please supply a guest list cover charge.');
					return false;
				}
				
				if(!this.image_data){
					this.display_error('Please choose an image to represent your guest list.');
					return false;
				}
				
				
			//	this.$el.find('input#submit_new_guest_list').hide();
				this.$el.find('#ajax_loading').show();
				
								
				form_object.image_data = this.image_data;
				
				jQuery.background_ajax({
					data: {
						vc_method: 	'edit_team_guest_list',
						gl_form: 	form_object
					},
					success: function(data){
						
						if(data.success){
							
							
							window.module.Globals.prototype.unbind_callback();
							jQuery('#back').trigger('click');
							
						}else{
							
							_this.display_error(data.message);
							
							_this.$el.find('input#submit_new_guest_list').show();
							_this.$el.find('#ajax_loading').hide();
							
						}
						
						console.log('data');
						console.log(data);
						
						
					}
				});
							
				
				return false;
			}
		}; Views.NewGLForm = Backbone.View.extend(Views.NewGLForm);						
				
		var views_newglform = new Views.NewGLForm({});			
					
					
				


		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('edit unbind callback');
			
			if(window.crop_object && window.crop_object.remove)
				window.crop_object.remove();
			
		}
		
	}
	
});