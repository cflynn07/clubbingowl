(function(globals){
	
	var EVT = window.ejs_view_templates_admin_promoters || window.ejs_view_templates_admin_managers;


	var respond = function(obj){
	
		var el 			= obj.el;
		var head_user 	= obj.head_user;
		var _this 		= obj._this;
		var called_from	= obj.called_from;




	
		var respond_callback = function(resp){
			
			jQuery('div#dialog_actions').find('textarea[name=message]').val('');
			
			
			
			if(called_from == 'dashboard')
				if(resp.action == 'approve'){
					_this.$el.css({
						background: 'green'
					});
				}else{
					_this.$el.css({
						background: 'red'
					});
				}
			
			
			
			
			jQuery.background_ajax({
				data: {
					vc_method: 	'update_pending_requests',
					pglr_id: 	_this.model.get('id'),
					action: 	resp.action,
					message: 	resp.message
				},
				success: function(data){
				
					//this differs on dash-v-gl pages
					if(called_from == 'dashboard'){
													
						_this.$el.animate({
							opacity: 0
						}, 500, 'linear', function(){
							//_this.$el.trigger('request-responded');
							_this.$el.remove();
						});
						
					
					}else if(called_from == 'guest_lists'){
						
						if(data.success)
							_this.model.set({
								pglr_response_msg: 	resp.message,
								pglr_approved:		(resp.action == 'approve') ? '1' : '-1'
							});
						
					}
					
					
				}
			});
			
		};
		
		
		
		
		
		
		if(_this.model.get('pglr_table_request') == '1'){
			jQuery('div#dialog_actions').find('p#table_message').show();
		}else{
			jQuery('div#dialog_actions').find('p#table_message').hide();
		}
		
		jQuery('div#dialog_actions').dialog({
			title: 		'Approve or Decline Request',
			height: 	420,
			width: 		320,
			modal: 		true,
			resizable: 	false,
			draggable: 	false,
			buttons: [{
				text: 'Decline',
				click: function(){
					respond_callback({
						action: 'decline',
						message: jQuery(this).find('textarea[name=message]').val()
					});
					jQuery(this).dialog('close');
				}
			},{
				text: 'Approve',
				id: 'ui-approve-button',
				'class': 'btn-confirm',
				click: function(){
					respond_callback({
						action: 'approve',
						message: jQuery(this).find('textarea[name=message]').val()
					});
					jQuery(this).dialog('close');
				}
			}]
		});
		
		
		
		
		jQuery('div#dialog_actions').find('*[data-name]').attr('data-name', head_user);				
		jQuery('div#dialog_actions').find('*[data-pic]').attr('data-pic', 	head_user);				
		
		
		
		
		jQuery.fbUserLookup([head_user], 'name, uid, third_party_id', function(rows){							
			for(var i in rows){
				var user = rows[i];
				if(user.uid != head_user)
					continue;
				
				jQuery('div#dialog_actions').find('*[data-name=' + head_user + ']').html(user.name);				
				jQuery('div#dialog_actions').find('*[data-pic=' + head_user + ']').attr('src', 	'https://graph.facebook.com/' + head_user + '/picture?width=50&height=50');
			
			}
		});
		
			
		
	};
	
	
	
	
	var module_request_respond = {
		respond: respond
	};

 	globals.promoter_module_request_respond = module_request_respond;
 	
 	
 	

}(window.module.Globals.prototype));
