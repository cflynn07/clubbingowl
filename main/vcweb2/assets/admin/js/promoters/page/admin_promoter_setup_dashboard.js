if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_setup_dashboard = function(){
		
		
		console.log('hp1');
		
		jQuery('#continue_completed_setup').live('click', function(){
			window.location = '/admin/promoters/';
		});
		
		
		
		
		//alert user to invalid public_identifier
		jQuery('input#public_identifier').keyup(function(){
			
			
			
			if( /[^a-zA-Z0-9] /.test(jQuery(this).val()) ){
				jQuery('p#public_identifier_error').css('display', 'block');
			}else{
				jQuery('p#public_identifier_error').css('display', 'none');
			}
			
		});
		
		
		jQuery('input#sms_text_number').mask('(999)-999-9999');
		
		
		(function(){
			var characters = 100;
			jQuery("form#promoter_setup_form span#biography_char_remaining").html("<strong>" + characters + "</strong> characters to go...");
			
			jQuery("textarea#text_biography").keyup(function(){								
			    if(jQuery(this).val().length > characters){
			
					jQuery("form#promoter_setup_form span#biography_char_remaining").html('');
			
			    }else{
			    	var remaining = characters - jQuery(this).val().length;
					jQuery("form#promoter_setup_form span#biography_char_remaining").html("<strong>" + remaining + "</strong> characters to go...");
			    }
			        
			});
		})();
		
		
		
		
		jQuery('div#visible_content form#promoter_setup_form input[type = submit]').bind('click', function(){
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: jQuery('form#promoter_setup_form').serialize(),
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					console.log(data);
					
					if(data.success){
						
						jQuery('div#visible_content').fadeOut(750, function(){
							
							jQuery(window).scrollTop(0);
							
							jQuery(this).empty();
							jQuery(this).html(jQuery('div#step_2_content').html());
							
						}).fadeIn(750, function(){
							
							//step two -- init ocupload
											
							//initialize 1-click-image upload with iframe
						    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						    					    
					        //initialize one-click upload for profile picture
					        myUpload = jQuery('#ocupload_button').upload({
						        name: 'file',
						        action: window.location,
						        enctype: 'multipart/form-data',
						        params: {
						        	ocupload: true,
					        		vc_method: 'image_upload',
					        		ci_csrf_token: cct
					        	},
						        autoSubmit: true,
						        onSubmit: function(){
						        	
						        	jQuery('#ajax_loading').css('visibility', 'visible');
						        //	jQuery('#ajax_complete').css('visibility', 'hidden');
						        	
						        },
						        onComplete: function(response){
						        	
						        	
						        	
						        	response = jQuery.parseJSON(response);
						        	
						        	
						        	if(!response.success){
						        		
						        		jQuery('#ajax_loading').hide();
						        		alert(response.message);
						        		return;
						        		
						        	}
						        	
						        	jQuery("input[name=file]").val('');
						         	if(typeof crop_object !== 'undefined' && crop_object.remove)
										crop_object.remove();
						        	
						        	
						        	//load new profile image
						        	var src = window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/originals/' + response.image_data.profile_img + '.jpg';
						        	var img = jQuery('<img></img>').attr('id', 'profile_pic').attr('src', src).bind('load', function(){
						        		
						        		
						        		jQuery('#ajax_loading').hide();
						        	//	jQuery('#ajax_complete').css('visibility', 'visible');
						        		
						        		//update crop form
							        	jQuery('div#visible_content #my_profile_pic_form input[name = x0]').val(response.image_data.x0);
										jQuery('div#visible_content #my_profile_pic_form input[name = y0]').val(response.image_data.y0);
										jQuery('div#visible_content #my_profile_pic_form input[name = x1]').val(response.image_data.x1);
										jQuery('div#visible_content #my_profile_pic_form input[name = y1]').val(response.image_data.y1);
										jQuery('div#visible_content #my_profile_pic_form input[name = width]').val(response.image_data.original_width);
										jQuery('div#visible_content #my_profile_pic_form input[name = height]').val(response.image_data.original_height);	
				
						        		crop_object = jQuery('#profile_pic').imgAreaSelect({
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
											minWidth: 240,
											onSelectChange: function(img, selection){
												jQuery('#my_profile_pic_form input[name = x0]').val(selection.x1);
												jQuery('#my_profile_pic_form input[name = y0]').val(selection.y1);
												jQuery('#my_profile_pic_form input[name = x1]').val(selection.x2);
												jQuery('#my_profile_pic_form input[name = y1]').val(selection.y2);
												jQuery('#my_profile_pic_form input[name = width]').val(selection.width);
												jQuery('#my_profile_pic_form input[name = height]').val(selection.height);
											}
										});
						        		
						        		
						        	});
						        	
						        	jQuery('div#profile_pic_holder').html(img);
						        	
						        	//display crop button & bind click event to it
						        	jQuery('#crop_button').css('display', 'inline-block');
						        	jQuery('#crop_button').unbind('click');
						        	jQuery('#crop_button').bind('click', function(){
						        		
						        		jQuery('#ajax_loading').show();
						        	//	jQuery('#ajax_complete').css('visibility', 'hidden');
						        		
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
	
												if(data.success){
													
													crop_object.cancelSelection();
													
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
																										
																jQuery('#ajax_loading').css('visibility', 'hidden');
						        								jQuery('#ajax_complete').css('visibility', 'visible');
																
																jQuery('div#visible_content').fadeOut(750, function(){
																	
																	jQuery(window).scrollTop(0);
																	
																	jQuery(this).empty();
																	jQuery(this).html(jQuery('div#step_3_content').html());
																	jQuery(window).scrollTop(0);
																	
																	Cufon.replace(jQuery('h2,h1'));
																	
																	
																}).fadeIn(750, function(){
																	
																});
																
																
															}
														},
														failure: function(){
															//ToDo: improve message
															alert('Server failed to respond. Please try again later.');
														}
													});
												}
												
											},
											failure: function(){
												//ToDo: improve message
												alert('Server failed to respond. Please try again later.');
											}
										});
	
						        		return false;
						        	});
						        	
										        		        	
						        },
						        onSelect: function(){
						        	
						        }
						    });
							
						});
						
					}else{
						
						jQuery('p#step_1_response_message').html(data.message);
						
					}
				}
			});
			
			return false;
		});
	}
	
});