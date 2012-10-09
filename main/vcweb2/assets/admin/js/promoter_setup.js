jQuery(function(){
	//Bind necessary events to form submission
	jQuery('#promoter_setup_form').find('input[type = submit]').bind('click', function(){
		window.module.PromoterSetup.prototype.form_disable();
		window.module.PromoterSetup.prototype.form_submit();
		return false;
	});
	
	/* ------------------------------------------------- */
	
	//show loading indicator
	jQuery('#ajax_loading').ajaxStart(function(){
		jQuery(this).css('visibility', 'visible');
	});
	
	//hide loading indicator
	jQuery('#ajax_complete').ajaxStop(function(){
		jQuery('#ajax_loading').css('display', 'none');
		jQuery(this).css('visibility', 'visible');
	});
});

(function(exports) {

	//class constructor
	var PromoterSetup = function() {
	
	};
	
	/**
	 * Update server with promoter results from page 1 of signup
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.form_submit = function(){		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: jQuery('#promoter_setup_form').serialize(),
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				console.log(data);
				if(data.success)
					window.module.PromoterSetup.prototype.load_step('2');
				else
					alert(data.message);
			}
		});
	};
	
	/**
	 * Disables all form elements on form submit to prevent duplicate submits
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.form_disable = function(){
		jQuery('#my_profile_form input[type = submit]').attr('disabled', 'disabled');
	};
	
	/**
	 * Enables all form elements after ajax form submission completion
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.form_enable = function(){
		jQuery('#my_profile_form input[type = submit]').removeAttr('disabled');
	};
	
	/**
	 * changes contents of visible div after user successfully moves to step 2
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.load_step = function(number){
		
		//swap content
		
		var visual_area = jQuery('#visible_content');
		var staging_area = jQuery('#step_' + number + '_content');
		
		//swap contents between areas with fade effect
		visual_area.fadeOut(750, function(){
								jQuery(this).empty();
								jQuery(this).html(staging_area.html());
								staging_area.html('');
							}).fadeIn(750, function(){
								//initializations for step 2
								if(number == 2)
									window.module.PromoterSetup.prototype.initialize_ocupload();
								
								//initializations for step 3
								if(number == 3){
									jQuery('#continue_completed_setup').bind('click', function(){
										window.location.reload(true);
									});
								}
								
							});
	};
	

	/*
	 * initializes crop on profile picture with the coordinates provided by the server
	 * allows user to resize/adjust the crop area
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.initialize_crop = function(){
		window.module.PromoterSetup.prototype.crop_object = jQuery('#profile_pic').imgAreaSelect({
			instance: true,
			handles: true,
			aspectRatio: '3:4',
			show: true,
			persistent: true,
			x1: parseInt(jQuery('#my_profile_pic_form input[name = x0]').val()),
			y1: parseInt(jQuery('#my_profile_pic_form input[name = y0]').val()),
			x2: parseInt(jQuery('#my_profile_pic_form input[name = x1]').val()),
			y2: parseInt(jQuery('#my_profile_pic_form input[name = y1]').val()),
			imageHeight: parseInt(jQuery('#height').val()),
			imageWidth: parseInt(jQuery('#width').val()),
			minWidth: 200,
			onSelectChange: function(img, selection){
				jQuery('#my_profile_pic_form input[name = x0]').val(selection.x1);
				jQuery('#my_profile_pic_form input[name = y0]').val(selection.y1);
				jQuery('#my_profile_pic_form input[name = x1]').val(selection.x2);
				jQuery('#my_profile_pic_form input[name = y1]').val(selection.y2);
				jQuery('#my_profile_pic_form input[name = width]').val(selection.width);
				jQuery('#my_profile_pic_form input[name = height]').val(selection.height);
			}
		});
	}
	
	/*
	 * initialize one-click-upload for promoters to upload new profile pictures
	 * without a page refresh
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.initialize_ocupload = function(){
		
		//initialize 1-click-image upload with iframe
	    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
	    
	    jQuery('#crop_button').ajaxStart(function(){
			jQuery('#ajax_loading').css('visibility', 'visible');
		    jQuery('#ajax_complete').css('visibility', 'hidden');
		});
		
		jQuery('#crop_button').ajaxStop(function(){
			jQuery('#ajax_loading').css('visibility', 'hidden');
		    jQuery('#ajax_complete').css('visibility', 'visible');
		});
	    
        //initialize one-click upload for profile picture
        myUpload = jQuery('#ocupload_button').upload({
	        name: 'file',
	        action: window.location,
	        enctype: 'multipart/form-data',
	        params: {'ocupload': true,
	        		'vc_method': 'image_upload',
	        		'ci_csrf_token': cct},
	        autoSubmit: true,
	        onSubmit: function(){
	        	
	        	jQuery('#ajax_loading').css('visibility', 'visible');
	        	jQuery('#ajax_complete').css('visibility', 'hidden');
	        	
	        },
	        onComplete: function(response){
	        	
	        	jQuery('#ajax_loading').css('visibility', 'hidden');
	        	jQuery('#ajax_complete').css('visibility', 'visible');
	        	
	        	response = jQuery.parseJSON(response);
	        	
	        	if(!response.success){
	        		alert(response.message);w
	        		return;
	        	}
	        	
	        	//load new profile image
	        	jQuery('#profile_pic').attr('src', window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/originals/' + response.image_data.profile_img + '.jpg');
	        	
	        	//display crop button & bind click event to it
	        	jQuery('#crop_button').css('display', 'inline-block');
	        	jQuery('#crop_button').bind('click', function(){
	        		window.module.PromoterSetup.prototype.submit_crop();
	        		return false;
	        	});
	        	
				
	        	//update crop form
	        	jQuery('#my_profile_pic_form input[name = x0]').val(response.image_data.x0);
				jQuery('#my_profile_pic_form input[name = y0]').val(response.image_data.y0);
				jQuery('#my_profile_pic_form input[name = x1]').val(response.image_data.x1);
				jQuery('#my_profile_pic_form input[name = y1]').val(response.image_data.y1);
				jQuery('#my_profile_pic_form input[name = width]').val(response.image_data.original_width);
				jQuery('#my_profile_pic_form input[name = height]').val(response.image_data.original_height);	
				
				PromoterSetup.prototype.initialize_crop();
					        		        	
	        },
	        onSelect: function(){
	        	
	        }
	    });
	    
	}
	
	/*
	 * calls server with crop coordinates of image
	 * 
	 * @return	null
	 */
	PromoterSetup.prototype.submit_crop = function(){
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				vc_method:	'crop_action',
				ci_csrf_token: cct,
				x0: 	jQuery('#my_profile_pic_form input[name = x0]').val(),
				y0: 	jQuery('#my_profile_pic_form input[name = y0]').val(),
				x1: 	jQuery('#my_profile_pic_form input[name = x1]').val(),
				y1: 	jQuery('#my_profile_pic_form input[name = y1]').val(),
				width: 	jQuery('#my_profile_pic_form input[name = width]').val(),
				height: jQuery('#my_profile_pic_form input[name = height]').val()
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				console.log(data);
				if(data.success){
					window.module.PromoterSetup.prototype.crop_object.cancelSelection();
					window.module.PromoterSetup.prototype.submit_complete_request();
				}
			},
			failure: function(){
				//ToDo: improve message
				alert('Server failed to respond. Please try again later.');
			}
		});
	    
	}
	
	/**
	 * Calls server after successful form and image uploads. Requests confirmation of 
	 * completed status.
	 * 
	 * @return	null
	 * */
	PromoterSetup.prototype.submit_complete_request = function(){
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				vc_method:	'confirm_complete_action',
				ci_csrf_token: cct
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				console.log(data);
				if(data.success){
					window.module.PromoterSetup.prototype.crop_object.cancelSelection();
					window.module.PromoterSetup.prototype.load_step('3');
				}
			},
			failure: function(){
				//ToDo: improve message
				alert('Server failed to respond. Please try again later.');
			}
		});
	}
	
	/* ---------------------- class helper methods ---------------------- */

	/* ---------------------- / class helper methods ---------------------- */
	
	exports.module = exports.module || {};
	exports.module.PromoterSetup = PromoterSetup;

})(window);