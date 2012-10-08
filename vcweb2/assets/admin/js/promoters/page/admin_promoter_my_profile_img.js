if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
		
	window.vc_page_scripts.admin_promoter_my_profile_img = function(){
		
		var unbind_callbacks = [];
		
		
		console.log('admin promoter my profile image');

		
		(function(exports) {
			
			//class constructor
			var MyProfileImg = function() {
			
			};
			
			var crop_init_helper = function(scope){
				MyProfileImg.prototype.crop_object = jQuery(scope).imgAreaSelect({
					instance: true,
					handles: true,
					aspectRatio: '3:4',
					show: true,
					persistent: true,
					minWidth: 240,
					
					x1: MyProfileImg.prototype.crop_dimensions.x0,
					y1: MyProfileImg.prototype.crop_dimensions.y0,
					x2: MyProfileImg.prototype.crop_dimensions.x1,
					y2: MyProfileImg.prototype.crop_dimensions.y1,
					imageHeight: jQuery(scope).height(),
					imageWidth: jQuery(scope).width(),
					
					onSelectChange: function(img, selection){
						
						MyProfileImg.prototype.crop_dimensions.x0 = selection.x1;
						MyProfileImg.prototype.crop_dimensions.y0 = selection.y1;
						MyProfileImg.prototype.crop_dimensions.x1 = selection.x2;
						MyProfileImg.prototype.crop_dimensions.y1 = selection.y2;
						
					}
				});
				
				unbind_callbacks.push(function(){
					MyProfileImg.prototype.crop_object.remove();
				});
				
			}
			
			MyProfileImg.prototype.crop_dimensions = {
				x0: 	window.page_obj.promoter.up_x0, //<?= $promoter->up_x0 ?>,
				y0: 	window.page_obj.promoter.up_y0, //<?= $promoter->up_y0 ?>,
				x1: 	window.page_obj.promoter.up_x1, //<?= $promoter->up_x1 ?>,
				y1: 	window.page_obj.promoter.up_y1  //<?= $promoter->up_y1 ?>
			}
			
			MyProfileImg.prototype.crop_object = null;
			
			/*
			 * initializes crop on profile picture with the coordinates provided by the server
			 * allows user to resize/adjust the crop area
			 * 
			 * @return	null
			 */
			MyProfileImg.prototype.initialize_crop = function(){
				
				jQuery('img#original_profile_pic').bind('load', function(){
					crop_init_helper(this);
				});
				
			}
			
			/**
			 * initialize one-click-upload for promoters to upload new profile pictures
			 * without a page refresh
			 * 
			 * @return	null
			 */
			MyProfileImg.prototype.initialize_ocupload = function(){
				
				//initialize 1-click-image upload with iframe
			    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			    	    	    
		        //initialize one-click upload for profile picture
		        myUpload = jQuery('#ocupload_button').upload({
			        name: 'file',
			        action: window.location,
			        enctype: 'multipart/form-data',
			        params: {
			        	ocupload: true,
			        	ci_csrf_token: cct
			        },
			        autoSubmit: true,
			        onSubmit: function(){
			        	
			        	jQuery('#ajax_loading').css('display', 'inline-block');
			        	jQuery('#ajax_complete').css('display', 'none');
			        	
			        },
			        onComplete: function(response){
			        	
			        	response = jQuery.parseJSON(response);
			        	
			        	if(!response.success){
			        		alert(response.message);
			        		return;
			        	}
			        	
			        	//load new profile image
			        	jQuery('img#original_profile_pic').attr('src', window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/originals/' + response.image_data.profile_img + '.jpg').bind('load', function(){
			        		
			        		jQuery('#ajax_loading').css('display', 'none');
			        		jQuery('#ajax_complete').css('display', 'inline-block');
			        		
			        		MyProfileImg.prototype.crop_object.cancelSelection();
			        		        		
			        		MyProfileImg.prototype.crop_dimensions.x0 = response.image_data.x0;
							MyProfileImg.prototype.crop_dimensions.y0 = response.image_data.y0;
							MyProfileImg.prototype.crop_dimensions.x1 = response.image_data.x1;
							MyProfileImg.prototype.crop_dimensions.y1 = response.image_data.y1;      		
			        		
			        		crop_init_helper(this);
			        		
			        	});
			      
			        	
			        },
			        onSelect: function(){
			        	
			        }
			    });
			    
			}
			
			/**
			 * calls server with crop coordinates of image
			 * 
			 * @return	null
			 */
			MyProfileImg.prototype.submit_crop = function(){
				
				jQuery('#ajax_loading').css('display', 'inline-block');
			    jQuery('#ajax_complete').css('display', 'none');
		
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				var data = MyProfileImg.prototype.crop_dimensions;
				data.width = jQuery('img#original_profile_pic').width();
				data.height = jQuery('img#original_profile_pic').height();
				data.ci_csrf_token = cct;
				
				jQuery.ajax({
					url: window.location,
					type: 'post',
					data: data,
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
						console.log(data);
						if(data.success){
							
							jQuery('#ajax_loading').css('display', 'none');
			    			jQuery('#ajax_complete').css('display', 'inline-block');
							
							window.location = '/admin/promoters/my_profile/';
						}
						
					},
					failure: function(){
						//ToDo: improve message
						alert('AJAX Failure, server failed to respond');
					}
				});
			    
			}
			
			/* ---------------------- class helper methods ---------------------- */
		
			/* ---------------------- / class helper methods ---------------------- */
			
			exports.module = exports.module || {};
			exports.module.MyProfileImg = MyProfileImg;
		
		})(window);
				
		
		
		
		
		
		
		
		window.module.MyProfileImg.prototype.initialize_crop();
	
		//rediculous temporary workaround to get this damn thing to work
		window.module.MyProfileImg.prototype.initialize_ocupload();
		window.module.MyProfileImg.prototype.initialize_ocupload();
		
		//bind crop button to image crop
		jQuery('#crop_button').bind('click', function(){
			window.module.MyProfileImg.prototype.submit_crop();
			return false;
		});
		
			
		
		


		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});