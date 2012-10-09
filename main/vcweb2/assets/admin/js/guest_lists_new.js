/* 
 * Casey Flynn
 * August 12, 2011
 * Script for admin/guest_lists_new/
 */

jQuery(document).ready(function(){

	jQuery('#guest_list_new_form input[type = submit]').bind('click', function(){
		//disable form input button after submission
		jQuery('#guest_list_new_form input[type = submit]', this).attr('disabled', 'disabled');
	
		//update server
		window.module.AdminGuestListsNew.prototype.update_server();
		return false;
	});

});

(function(exports) {
	

	//class constructor
	var AdminGuestListsNew = function() {
	
	};
	
	/*
	 * 
	 * 
	 * @return	null
	 */
	AdminGuestListsNew.prototype.update_server = function(){
		
		jQuery('#guest_list_new_form input[type = submit]').ajaxStart(function(){
			jQuery('#ajax_loading').css('visibility', 'visible');
		    jQuery('#ajax_complete').css('visibility', 'hidden');
		});
		
		jQuery('#guest_list_new_form input[type = submit]').ajaxStop(function(){
			jQuery('#ajax_loading').css('visibility', 'hidden');
		  //  jQuery('#ajax_complete').css('visibility', 'visible'); <-- ToDo: fix
		});
		
		jQuery.ajax({
			url: '',
			type: 'post',
			data: jQuery('#guest_list_new_form').serialize(),
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				if(data.success){
					window.location = '/admin/promoters/guest_lists/';
					return;
				}else
					jQuery('#ajax_loading').css('visibility', 'hidden');
		   			jQuery('#ajax_complete').css('visibility', 'hidden');
					alert(data.message);
					//re-enable form
					jQuery('#guest_list_new_form input[type = submit]', this).attr('disabled', '');
			}
		});
		
	};
	
	
	/* ---------------------- class helper methods ---------------------- */

	/* ---------------------- / class helper methods ---------------------- */
	
	exports.module = exports.module || {};
	exports.module.AdminGuestListsNew = AdminGuestListsNew;

})(window);