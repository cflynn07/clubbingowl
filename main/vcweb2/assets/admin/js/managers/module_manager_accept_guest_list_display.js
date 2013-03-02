(function(globals){
	
	
	var EVT = window.ejs_view_templates_admin_managers;
	
	
	
	var initialize = function(scope){
		
		var _this 			= scope;
		var head_user 		= _this.model.get('head_user') || _this.model.get('tglr_user_oauth_uid');
		var table_display 	= (_this.model.get('request_type') == 'promoter' || _this.model.get('tglr_table_request') == '1');
		var Views 			= {};
		var vlfit_id		= false;
		
		var guest_lists_page		= (window.location.href.indexOf('/admin/managers/guest_lists') != -1);
		
		jQuery('div#dialog_actions span#assigned_table div').empty().hide();
		
		if(table_display)
			jQuery('div#dialog_actions #table_assignment_message').show();
		else 
			jQuery('div#dialog_actions #table_assignment_message').hide();
		
		
		
		var display_tables_helper = function(){
			
			console.log(_this.model.toJSON());
			
			var target = '#dialog_actions_floorplan';
			jQuery(target).hide();
			
			jQuery.background_ajax({
				data: {
					vc_method: 	'find_tables',
					tv_id:		_this.model.get('tv_id'),
					
					
					
				//	pglr_id:	_this.model.get('id'),
				//	tglr_id:	_this.model.get('tglr_id'),
					
					
					
					iso_date: 	_this.model.get('iso_date')
				},
				success: function(data){
					
					
					console.log(data);
					
									
					var venue = false;
					for(var i in data.message.team_venues){
						
						console.log(_this.model.get('tv_id'));
						console.log(data.message.team_venues[i]);
						
						if(data.message.team_venues[i].tv_id == _this.model.get('tv_id')){
							venue = data.message.team_venues[i];
							break;
						}
					}
					if(!venue)
						return false;
				
																	
					
					// Find the prices of tables for the day
					// --------------------------------------------------------------------
			/*		//array of unique day prices
					var day_prices = [];
					
					var pgla_day = _this.model.get('pgla_day');
					pgla_day = pgla_day.slice(0, -1);
					
					for(var i in venue.venue_floorplan){
						var floor = venue.venue_floorplan[i]
						
						for(var k in floor.items){
							var item = floor.items[k];
							
							if(item.vlfi_item_type == 'table'){
	
								//what day do we care about?
								var table_day_price = item['vlfit_' + pgla_day + '_min'];
								console.log(table_day_price);
								
								if(jQuery.inArray(table_day_price, day_prices) === -1){
									day_prices.push(table_day_price);
								}
								
							}	
						}
					}
					
					day_prices = day_prices.sort();
					console.log(day_prices);
					// --------------------------------------------------------------------
			*/		
			
					var tv_display_module 	= jQuery.extend(globals.module_tables_display, {});
					var target = '#dialog_actions_floorplan';
					tv_display_module
						.initialize({
							display_target: 	target, //'#' + _this.$el.attr('id'),
							team_venue: 		venue,
							factor: 			0.45,
							options: {
								display_slider: true
							}
						});
					jQuery(target).show();	
					jQuery('div#dialog_actions').dialog('option', 'position', 'center center');
					
					
					
					
					
					
					//_this.modal_view.dialog('option', {
					//	width: 	900						
					//});
					//_this.modal_view.dialog('option', {
					//	position: 'center center'
					//});
					
					//_this.$el.find('select#table_min_price').trigger('change');
										
				}
			});
		}
	
	
	
	
	
	
	
		var respond_callback = function(resp){
			
			
			if(resp.action == 'approve' && table_display && !vlfit_id){
				jQuery('div#dialog_actions p#dialog_actions_message').html('Please select a table.');
				jQuery('#dialog_actions').scrollTop(9999999);
				return;
			}
			
			
			jQuery('div#dialog_actions').find('textarea[name=message]').val('');
			
			if(!guest_lists_page)
				if(resp.action == 'approve'){				
					_this.$el.css({
					//	background: 'green'
					
						opacity: 0.5
					
					});
				}else{
					_this.$el.css({
					//	background: 'red'
					
						opacity: 0.5
					
					});
				}
			
			
			jQuery.background_ajax({
				data: {
					vc_method: 		'update_pending_requests',
					request_type:	_this.model.get('request_type'),
					glr_id:			(_this.model.get('request_type') == 'promoter') ? _this.model.get('id') : _this.model.get('tglr_id'),
					vlfit_id:		vlfit_id,
					action: 		resp.action,
					message: 		resp.message
				},
				success: function(data){
					
					if(!guest_lists_page)													
						_this.$el.animate({
							opacity: 0
						}, 500, 'linear', function(){
							_this.$el.remove();
						});
					
				}
			});
			
			
			vlfit_id = false;
			jQuery('div#dialog_actions p#dialog_actions_message').empty();
			
			jQuery(resp.scope).dialog('close');
			
		};
	
		
		
		
		
		
											
					
								
		Views.DialogActions = {
			initialize: function(){
				//insert user name into dialog				
				if(head_user){
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
				}else{
					jQuery('div#dialog_actions').find('*[data-name]').html(_this.model.get('pglr_supplied_name'));				
					jQuery('div#dialog_actions').find('*[data-pic]').attr('src', window.module.Globals.prototype.admin_assets + 'images/unknown_user.jpeg');	
				}		
			},
			events: {
				'click .item.table': 'click_item_table',
				'event-reorganize-tables': 'reorganize_tables'
			},
			reorganize_tables: function(){
				vlfit_id = false;
				jQuery('div#dialog_actions span#assigned_table div').empty().hide();
			},
			click_item_table: function(e){
				
				var el = jQuery(e.currentTarget);
				
				if(el.data('reserved')){
					return false;
				}
								
				vlfit_id = el.data('vlfit_id');
				
				
				jQuery('div#dialog_actions span#assigned_table div').html(el.html()).show();
				
				
			//	el.trigger('highlighted');
				
			//	this.$el.find('.table').each(function(){
			//		jQuery(this).trigger('de-highlighted');
			//	})
												
			},
			close: function(){
				
				console.log('view close');
				this.$el.unbind();
				
			}
		}; Views.DialogActions = Backbone.View.extend(Views.DialogActions);
		
		
		
		
		var dialog_options = {
			title: 		'Approve or Decline Request',
			modal: 		true,
			height: 	'auto',
			resizable: 	true,
			draggable: 	true,
			open: 		function(){
				
				
				
				
				if(table_display)
					display_tables_helper();
				else 
					jQuery('#dialog_actions_floorplan').hide();
				
				
				if(_this.model.get('request_type') == 'promoter'){
					jQuery('#dialog_actions_message_wrapper').hide();
				}else{
					jQuery('#dialog_actions_message_wrapper').show();
				}
				
			},
			close: 		function(){
				
				globals.module_reservation_display.remove();
				
			},
			buttons: [{
				text: 'Decline',
				click: function(){
					
					respond_callback({
						action: 'decline',
						message: jQuery(this).find('textarea[name=message]').val(),
						scope: this
					});
					
				
				}
			},{
				text: 'Approve',
				id: 'ui-approve-button',
				'class': 'btn-confirm',
				click: function(){
					
					respond_callback({
						action: 'approve',
						message: jQuery(this).find('textarea[name=message]').val(),
						scope: this
					});
					

				}
			}]
		};
		
		
		
		if(!table_display){
		//	dialog_options.height 	= 330;
			dialog_options.width 	= 320;
		}else{
		//	dialog_options.height 	= 690;
			dialog_options.width 	= 800;		
		}
		
		
				
		jQuery('div#dialog_actions').dialog(dialog_options);
		var view_dialog_actions = new Views.DialogActions({
			el: '#dialog_actions'
		});
				
	}
	
	
	
	//public API
	var module_manager_accept_guest_list_display = {
		initialize: initialize,
		remove: function(){
			
			jQuery('div#dialog_actions').dialog('close');
			
		}
	};
	globals.module_manager_accept_guest_list_display = module_manager_accept_guest_list_display;
	
}(window.module.Globals.prototype));
