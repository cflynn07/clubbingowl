/* 
 * Casey Flynn
 * August 10, 2011
 * Script for admin/guest_lists/
 */

jQuery(document).ready(function(){

	jQuery('#new_guest_list').bind('click', function(){
		window.location = '/admin/promoters/guest_lists_new/';
	});
	
	
	//bind click event to modal confirmation window open
	jQuery('.guest_list_delete').bind('click', function(){
		
		//get parent row
		var tr = jQuery(this).parent().parent();
		
		//get id of this guest list
		var gl_id = tr.attr('name');
		
		jQuery('#dialog-confirm').dialog({
			resizable: false,
			height:180,
			modal: true,
			autoOpen: false,
			buttons: {
				'Delete Guest List': function() {
					//tell server to delete this guest list
					window.module.AdminGuestLists.prototype.update_server('delete_guest_list', gl_id);
					jQuery(this).dialog('close');
				},
				Cancel: function() {					
					jQuery(this).dialog('close');
				}
			}
		});
		
		jQuery('#dialog-confirm').dialog('open');
		
	});
	
});

(function(exports) {
	

	//class constructor
	var AdminGuestLists = function() {
	
	};
		
	/*
	 * Sends a guest list delete request to the server via ajax
	 * 
	 * @param	string (method name)
	 * @param	int (guest-list id to delete)
	 * @return	null
	 * */
	AdminGuestLists.prototype.update_server = function(method, gl_id){
		
		//get csrf token
	    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: '',
			type: 'post',
			data: {'method': method,
					'gl_id': gl_id,
	        		'ci_csrf_token': cct},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				console.log(data);
				if(data.success)
					window.location.reload(true);
				else
					alert(data.message);
			}
		});
	};
	
	
	/* ---------------------- class helper methods ---------------------- */

	/* ---------------------- / class helper methods ---------------------- */
	
	exports.module = exports.module || {};
	exports.module.AdminGuestLists = AdminGuestLists;

})(window);