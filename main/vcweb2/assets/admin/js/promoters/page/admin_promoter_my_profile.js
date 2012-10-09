if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
		
	window.vc_page_scripts.admin_promoter_my_profile = function(){
		
		var unbind_callbacks = [];
		
		
		
		
		
		
		
		jQuery('input#sms_text_number').mask('(999)-999-9999');
		
		jQuery('form#my_profile_form input.submit').bind('click', function(){
			
			
			jQuery('img#ajax_loading').css('display', 'inline-block');
			jQuery('img#ajax_complete').css('display', 'none');
			jQuery(this).css('disabled', 'disabled');
			
			//initialize 1-click-image upload with iframe
		    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
		    jQuery.ajax({
		    	url: window.location,
				type: 'post',
				data: jQuery('form#my_profile_form').serialize() + '&ci_csrf_token=' + cct,
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					console.log(data);
					jQuery(this).css('disabled', '');
					jQuery('img#ajax_loading').css('display', 'none');
					jQuery('img#ajax_complete').css('display', 'inline-block');
					
				},
				failure: function(){
					
					//ToDo: improve message
					alert('AJAX Failure, server failed to respond');
					
				}
		    });
			
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