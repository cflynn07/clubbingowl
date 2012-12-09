(function(globals){
	
	
	var EVT = window.ejs_view_templates_admin_managers;
	
	
	
	var initialize = function(scope){
		
		var _this 			= scope;
		var head_user 		= _this.model.get('head_user') || _this.model.get('tglr_user_oauth_uid');
		var table_display 	= (_this.model.get('request_type') == 'promoter' || _this.model.get('tglr_table_request') == '1');
		var Views 			= {};
		
		
		
		
		
		var display_tables_helper = function(){
			
			var target = '#dialog_actions_floorplan';
			jQuery(target).hide();
			
			jQuery.background_ajax({
				data: {
					vc_method: 	'find_tables',
					tv_id:		_this.model.get('tv_id'),
					
				},
				success: function(data){
														
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
			
			jQuery('div#dialog_actions').find('textarea[name=message]').val('');
			
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
					
					console.log(data);
													
					_this.$el.animate({
						opacity: 0
					}, 500, 'linear', function(){
						//_this.$el.trigger('request-responded');
						_this.$el.remove();
					});
					
				}
			});
			
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
				'click .item.table': 'click_item_table'
			},
			click_item_table: function(e){
				
				var el = jQuery(e.currentTarget);
				el.trigger('highlighted');
				
				this.$el.find('.table').each(function(){
					jQuery(this).trigger('de-highlighted');
				})
												
			},
			close: function(){
				
				console.log('view close');
				this.$el.unbind();
				
			}
		}; Views.DialogActions = Backbone.View.extend(Views.DialogActions);
		
		
		
		
		
		
		
		
		var dialog_options = {
			title: 		'Approve or Decline Request',
			modal: 		true,
			resizable: 	true,
			draggable: 	true,
			open: 		function(){
				
				display_tables_helper();
				
			},
			close: 		function(){
				
				if(view_dialog_actions && view_dialog_actions.close)
					view_dialog_actions.close();
				
				if(tv_display_module && tv_display_module.destroy)
					tv_display_module.destroy();
				
			},
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
		};
		
		
		
		if(!table_display){
			dialog_options.height 	= 420;
			dialog_options.width 	= 320;
		}else{
			dialog_options.height 	= 690;
			dialog_options.width 	= 800;
		}
		
		
				
		jQuery('div#dialog_actions').dialog(dialog_options);
		var view_dialog_actions = new Views.DialogActions({
			el: '#dialog_actions'
		});
				
	}
	
	
	
	//public API
	var module_manager_accept_guest_list_display = {
		initialize: initialize
	};
	globals.module_manager_accept_guest_list_display = module_manager_accept_guest_list_display;
	
}(window.module.Globals.prototype));
