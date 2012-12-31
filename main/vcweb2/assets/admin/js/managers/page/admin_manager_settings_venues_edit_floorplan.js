if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_venues_edit_floorplan = function(){
						
		var unbind_callbacks = [];		
				
					
					
					
		jQuery('div#table_add_dialog input.day_price').priceFormat({
			prefix: 'US$ ',
			limit: 5,
			thousandsSeparator: ',',
			centsSeparator: '',
			centsLimit: 0
		});
		
		jQuery('div#table_add_dialog input.max_capacity').priceFormat({
			prefix: '',
			limit: 3,
			thousandsSeparator: '',
			centsSeparator: '',
			centsLimit: 0
		});			
					
					
				
				
				
				
		jQuery('div#tabs').tabs();
		jQuery('div#tabs > div.ui-widget-header ul').css('display', 'none');
		jQuery('div#tabs > div.ui-widget-header select.venue_floor_select').bind('change', function(){
			console.log(this);
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		
		jQuery('div.items > div.item').draggable({
			revert: 'invalid',
			tolerance: 'fit',
			opacity: .5,
			helper: "clone"
		});
		
		jQuery('div.venue_floor div.item').draggable({
			revert: 'invalid',
			tolerance: 'fit',
			snap: 'div.venue_floor',
			snapMode: 'inner',
			snapTolerance: 0,
			grid: [20, 20]
		}).resizable({
			grid: 20,
			constrain: 'div.venue_floor',
			minWidth: 40,
			minHeight: 40
		});
		
		var table_dialog_config = {
			width: 300,
			height: 'auto', 
			title: 'Add/Edit Table',
			modal: true,
			resizable: false,
		}
		var table_settings_adjust = function(table_div){
			//DRY viloation sure, don't care.
					
			var me = jQuery(table_div).parents('div.table');
					
			jQuery('div#table_add_dialog p.error').css('display', 'none').html('');
			
			var temp_title		= jQuery.trim(jQuery(me).find('span.title').html());
			
			var temp_monday 	= jQuery(me).find('div.monday').html();
			var temp_tuesday 	= jQuery(me).find('div.tuesday').html();
			var temp_wednesday 	= jQuery(me).find('div.wednesday').html();
			var temp_thursday 	= jQuery(me).find('div.thursday').html();
			var temp_friday 	= jQuery(me).find('div.friday').html();
			var temp_saturday 	= jQuery(me).find('div.saturday').html();
			var temp_sunday 		= jQuery(me).find('div.sunday').html();
			var temp_max_capacity 	= jQuery(me).find('div.max_capacity').html();
			
			var tad = jQuery('div#table_add_dialog');
			
			tad.find('input[name = title]').attr('value', temp_title);
			
			tad.find('input[name = monday]').attr('value', temp_monday);
			tad.find('input[name = tuesday]').attr('value', temp_tuesday);
			tad.find('input[name = wednesday]').attr('value', temp_wednesday);
			tad.find('input[name = thursday]').attr('value', temp_thursday);
			tad.find('input[name = friday]').attr('value', temp_friday);
			tad.find('input[name = saturday]').attr('value', temp_saturday);
			tad.find('input[name = sunday]').attr('value', temp_sunday);
			tad.find('input.max_capacity').attr('value', temp_max_capacity);
			
			
			
			
			table_dialog_config.buttons = [{
				text: 'Cancel',
				click: function(){
					jQuery(this).dialog('close');
				}
			},{
				text: 'OK',
				'class': 'btn-confirm',
				click: function(){
					
					var title = jQuery(this).find('input[name=title]').val();
					if(title.length === 0){
						jQuery('div#table_add_dialog p.error').html('You must specify a name for this table').show();
						return;
					}
					
					var max_capacity = jQuery(this).find('input.max_capacity').attr('value');
					if(max_capacity.length == 0){
						jQuery('div#table_add_dialog p.error').html('You must specify a maximum seating capacity for this table').show();
						return;
					}
					
					var inputs = [];
					
					jQuery(this).find('input.day_price').each(function(){
						var name = jQuery(this).attr('name');
						var value = jQuery(this).attr('value');
												
						if(value.length == 0){
							jQuery('div#table_add_dialog p.error').html('You must specify a default minimum price for this table on every weekday').show();
							return;
						}
						
						inputs[name] = value;
						
					});
										
					//find out how many tables are already on the floor
					
					
					for(key in inputs){
						me.find('.' + key).html(inputs[key]);
					}
					
					me.find('div.max_capacity').html(max_capacity);
					me.find('span.title').html(title);
					
					jQuery(this).dialog('close');
				}
			}];
			
			
			
			
			
			jQuery('div#table_add_dialog').dialog(table_dialog_config);
			
		}
		
		var table_add_dialog = function(ui, pos_x, pos_y){
			
			jQuery('div#table_add_dialog p.error').css('display', 'none').html('');
			jQuery('div#table_add_dialog input[name=title]').attr('value', '');
			
			
			
			table_dialog_config.buttons = [{
				text: 'Cancel',
				click: function(){
					jQuery(this).dialog('close');
				}
			},{
				text: 'OK',
				'class': 'btn-confirm',
				click: function(){
					
					
					var table_title = jQuery.trim(jQuery('div#table_add_dialog input[name=title]:first').attr('value'));
					
					
					//find out if another table already has this name
					var title_found = false;
					jQuery('div.item.table span.title').each(function(){
						
						console.log(this_title == jQuery.trim(table_title).toLowerCase());
												
						var this_title = jQuery.trim(jQuery(this).html()).toLowerCase();
						if(this_title == jQuery.trim(table_title).toLowerCase())
							title_found = true;
						
					});
					if(title_found){
						jQuery('div#table_add_dialog p.error').css('display', 'block').html('Another table already has the name"' + table_title + '"<br/>Please choose another title');
						return;
					}
					
					
					
					var max_capacity = jQuery(this).find('input.max_capacity').attr('value');
					if(max_capacity.length == 0){
						jQuery('div#table_add_dialog p.error').css('display', 'block').html('You must specify a maximum seating capacity for this table');
						return;
					}
					
					var inputs = [];
					jQuery(this).find('input.day_price').each(function(){
						var name = jQuery(this).attr('name');
						var value = jQuery(this).attr('value');
												
						if(value.length == 0){
							jQuery('div#table_add_dialog p.error').css('display', 'block').html('You must specify a default minimum price for this table on every weekday');
							return;
						}
						
						inputs[name] = value;
						
					});
										
					//find out how many tables are already on the floor
					var num_tables = jQuery('div#tabs-' + jQuery('div#tabs').tabs('option', 'selected') + ' div.venue_floor').find('div.table').length;
					
					var item = ui.draggable.clone();
					item.find('span.full_title').remove();
					item.find('br').remove();
				//	item.find('span.title').html('T-' + num_tables);
					
					item.find('span.title').html(table_title).css({
						color: 'lightblue',
						'text-decoration': 'underline'
					});
					
					for(key in inputs){
						item.append('<div class="day_price ' + key + '">' + inputs[key] + '</div>');
					}
					
					item.append('<div class="max_capacity">' + max_capacity + '</div>');
					
					item.removeClass('pre_drop').css({
						'position': 'absolute',
						'top': pos_y + 'px',
						'left': pos_x + 'px'
					}).appendTo( 'div#tabs-' + jQuery('div#tabs').tabs('option', 'selected') + ' div.venue_floor').draggable({
						revert: 'invalid',
						tolerance: 'fit',
						snap: 'div.venue_floor',
						snapMode: 'inner',
						snapTolerance: 0,
						grid: [20, 20]
					}).resizable({
						grid: 20,
						constrain: 'div.venue_floor',
						minWidth: 40,
						minHeight: 40
					});
					
					jQuery(this).dialog('close');
					
				}
			}];
			
						
			jQuery('div#table_add_dialog').dialog(table_dialog_config);
					
		}
		
		var droppable_drop_function = function(event, ui){
								
			var me = jQuery(this);
			
			var drop_offset = me.offset();
			var item_offset = ui.offset;
			
			drop_offset.top 	= Math.floor(drop_offset.top);
			drop_offset.left 	= Math.floor(drop_offset.left);
			item_offset.top 	= Math.floor(item_offset.top);
			item_offset.left 	= Math.floor(item_offset.left);
			
			var pos_x = item_offset.left - drop_offset.left;
			var pos_y = item_offset.top - drop_offset.top;
			
			//align to grid
			if(pos_x > 0){
				
				pos_x = Math.ceil(pos_x / 20) * 20;
				
			}else if(pos_x < 0){
				
				pos_x = Math.floor(pos_x / 20) * 20;
				
			}else{
				
				pos_x = 20;
				
			}
			
			//align to grid
			if(pos_y > 0){
				
				pos_y = Math.ceil(pos_y / 20) * 20;
				
			}else if(pos_y < 0){
				
				pos_y = Math.floor(pos_y / 20) * 20;
				
			}else{
				
				pos_y = 20;
				
			}
			
			if(ui.draggable.hasClass('pre_drop')){
							
				if(ui.draggable.hasClass('table')){
					//table questions
									
					table_add_dialog(ui, pos_x, pos_y);
					
				}else{
					//fresh drop
					var item = ui.draggable.clone();
					item.find('span.full_title').remove();
					item.find('br').remove();
					item.removeClass('pre_drop').css({
						'position': 'absolute',
						'top': pos_y + 'px',
						'left': pos_x + 'px'
					}).appendTo( 'div#tabs-' + jQuery('div#tabs').tabs('option', 'selected') + ' div.venue_floor').draggable({
						revert: 'invalid',
						tolerance: 'fit',
						snap: 'div.venue_floor',
						snapMode: 'inner',
						snapTolerance: 0,
						grid: [20, 20]
					}).resizable({
						grid: 20,
						constrain: 'div.venue_floor',
						minWidth: 40,
						minHeight: 40
					});
				}
				
			}
			
			//fix bug in FF
			setTimeout(function(){
				jQuery('div.items div.ui-draggable-dragging').remove();
			}, 100);
				
		}
		
		var no_delete_floor_dialog = function(){
			jQuery('div#no_delete_floor_dialog').dialog({
				width: 300,
				height: 'auto', 
				title: 'Error',
				modal: true,
				buttons: [{
					text: 'Okay',
					click: function(){
						jQuery(this).dialog('close');
					}
				}]
			});
		}
		
		var delete_floor_dialog = function(scope){
			
			var me = scope;
			
			jQuery('div#delete_floor_dialog').dialog({
				width: 300,
				height: 'auto', 
				title: 'Are you sure?',
				modal: true,
				buttons: [{
					text: 'Cancel',
					click: function(){
						jQuery(this).dialog('close');
					}
				},{
					text: 'OK',
					'class': 'btn-confirm',
					click: function(){
						
						//delete floor
						var index = jQuery('div#tabs').tabs('option', 'selected');
						console.log(index);
						
						//delete div
						jQuery('div#tabs div#tabs-' + index).remove();
						
						//delete select option
						jQuery('div#tabs div.ui-widget-header select.venue_floor_select option[value = ' + index + ']').remove();
						jQuery('div#tabs div.ui-widget-header a[href = #tabs-' + index + ']').parent().remove();
						
						//reorder divs
						var count = 0;
						jQuery('div#tabs div.layout_tabs').each(function(){
							jQuery(this).attr('id', 'tabs-' + count);
							count++;
						});
						
						//reorder selects
						count = 0;
						jQuery('div#tabs div.ui-widget-header select.venue_floor_select option').each(function(){
							jQuery(this).attr('value', count);
							if(jQuery(this).html().indexOf('Floor ') == 0){
								jQuery(this).html('Floor ' + count);
							}
							count++;
						});
						count = 0;
						jQuery('div#tabs div.ui-widget-header ul a').each(function(){
							jQuery(this).attr('href', '#tabs-' + count);
							if(jQuery(this).html().indexOf('Floor ') == 0){
								jQuery(this).html('Floor ' + count);
							}
							count++;
						});
						
						//select 0
						jQuery('div#tabs').tabs('destroy').tabs().tabs('select', 0);
						
						jQuery(this).dialog('close');
					}
				}]
			});
		}
		
		
		
		
		var edit_floor_dialog = function(){
			
			var floor_title = jQuery(this).html();
			jQuery('div#add_floor_dialog').find('input').val(floor_title);
			var _this = this;
			
			jQuery('div#add_floor_dialog').dialog({
				width: 		300,
				height: 	'auto', 
				title: 		'Add/Edit Floor',
				modal: 		true,
				resizable: 	false,
				buttons: [{
					text: 'Cancel',
					click: function(){
						jQuery(this).dialog('close');
					}
				},{
					text: 'OK',
					'class': 'btn-confirm',
					click: function(){
						
						
						
						var title = jQuery.trim(jQuery(this).find('input').val());
						if(title.length === 0){
							alert('Please choose a title for this floor.');
							return;
						}
						
						jQuery(_this).html(title);
						
						
						
						jQuery(this).dialog('close');
					}
				}]
			});
			
		};
		jQuery('.vlf_title').live('click', edit_floor_dialog);
		
		
		
		
		
		
		
		
		
		var add_floor_dialog = function(){
			
			jQuery('div#add_floor_dialog').dialog({
				width: 		300,
				height: 	'auto', 
				title: 		'Add/Edit Floor',
				modal: 		true,
				resizable: 	false,
				buttons: [{
					text: 'Cancel',
					click: function(){
						jQuery(this).dialog('close');
					}
				},{
					text: 'OK',
					'class': 'btn-confirm',
					click: function(){
						
						
						var title = jQuery.trim(jQuery(this).find('input').val());
						if(title.length === 0){
							alert('Please choose a title for this floor.');
							return;
						}
						
						
						var count_tabs = jQuery('div#tabs div.layout_tabs').length;
						
						//add div
						jQuery('div#tabs div.layout_tabs:last').clone().attr('id', 'tabs-' + count_tabs).appendTo('div#tabs').find('div.venue_floor').empty().append('<div class="vlf_title">' + title + '</div>');
						
						
						
						
						//add select
						jQuery('div#tabs div.ui-widget-header select.venue_floor_select option:last').clone().attr('value', count_tabs).html(title).appendTo('div#tabs div.ui-widget-header select.venue_floor_select');
						jQuery('div#tabs div.ui-widget-header ul li:last').clone().find('a').attr('href', '#tabs-' + count_tabs).html(title).parent().appendTo('div#tabs div.ui-widget-header ul');
										
										
										
										
						jQuery('div#tabs').tabs('destroy').tabs().tabs('select', count_tabs);
						jQuery('div#tabs div.ui-widget-header select.venue_floor_select').val(count_tabs);					
						
						//reinit droppable
						jQuery('div#tabs div#tabs-' + count_tabs + ' div.venue_floor').droppable({
							drop: droppable_drop_function
						});
						
						jQuery(this).dialog('close');
					}
				}]
			});
			
		}
	
		jQuery('div.venue_floor').droppable({
			drop: droppable_drop_function
		});
		
		//tooltip hover
		jQuery('div.venue_floor div.item span.title').live({
			mouseenter: function(){
				//jQuery(this).parent().find('div.tooltip').css('display', 'block');
			},
			mouseleave: function(){
				//jQuery(this).parent().find('div.tooltip').css('display', 'none');
			},
			click: function(){
				if(jQuery(this).parents('div.item').hasClass('table'))
					table_settings_adjust(this);
			}
		});
		
		//delete button
		jQuery('div.venue_floor > div.item > div.del').live('click', function(){
			
			
			var item = jQuery(this).parent();
			var is_table = item.hasClass('table');
			item.remove();
			
		//	if(is_table){
		//		jQuery('div#tabs-' + jQuery('div#tabs').tabs('option', 'selected') + ' div.venue_floor').find('div.table').each(function(index, element){
		//			jQuery(this).find('span.title').html('T-' + index);
		//		});
		//	}
			
		});
		
		jQuery('span.del_floor').live('click', function(){
			
			//check to make sure there is at least 1 floor
			var layout_tabs = jQuery('div#tabs > div.layout_tabs');
			if(layout_tabs.length == 1){
				no_delete_floor_dialog();
				return;
			}
			
			delete_floor_dialog(this);
			
		});
		jQuery('span.add_floor').live('click', function(){
			
			add_floor_dialog();
			
		});
		
		
		
		
		
		window.json_encode_floorplan = function(){
			
			var floors = [];
			
			jQuery('div#tabs div.venue_floor').each(function(index, el){
				
				var vlf_id = false;
				
				if(jQuery(this).find('div.vlf_id').length === 1)
					vlf_id = parseInt(jQuery(this).find('div.vlf_id').html(), 10);
				
				vlf_title = jQuery.trim(jQuery(this).find('.vlf_title').html());
				
				var floor = {
					vlf_id: vlf_id,
					title:	vlf_title,
					items: []
				};
				
				jQuery(this).find('div.item').each(function(index2, el2){
					
					var top 		= parseInt(jQuery(this).css('top'), 10);
					var left 		= parseInt(jQuery(this).css('left'), 10);
					var width		= parseInt(jQuery(this).css('width'), 10);
					var height		= parseInt(jQuery(this).css('height'), 10);
					var item_class 	= jQuery.trim(jQuery(this).attr('class').replace('item', '').replace('ui-draggable', '').replace('ui-resizable', ''));
					var vlfi_id 	= false;
					if(jQuery(this).find('div.vlfi_id').length === 1)
						vlfi_id = parseInt(jQuery(this).find('div.vlfi_id').html(), 10);
					
					var item = {
						top: top,
						left: left,
						width: width,
						height: height,
						item_class: item_class,
						vlfi_id: vlfi_id
					};
					
					if(item_class == 'table'){
						//pull table specific props
						
						item.title			= jQuery.trim(jQuery(this).find('span.title').html());
						item.monday_min 	= jQuery(this).find('div.monday').html();
						item.tuesday_min 	= jQuery(this).find('div.tuesday').html();
						item.wednesday_min 	= jQuery(this).find('div.wednesday').html();
						item.thursday_min 	= jQuery(this).find('div.thursday').html();
						item.friday_min 	= jQuery(this).find('div.friday').html();
						item.saturday_min 	= jQuery(this).find('div.saturday').html();
						item.sunday_min 	= jQuery(this).find('div.sunday').html();
						item.max_capacity 	= jQuery(this).find('div.max_capacity').html();
						
					}
									
					floor.items[index2] = item;
					
				});
				
				floors[index] = floor;
				
			});
			
			console.log(floors);
			console.log(JSON.stringify(floors));
			return JSON.stringify(floors);
		}
		
		
		
		
		
		//submit floorplan
		jQuery('input#submit_floorplan').bind('click', function(){
			
			jQuery('img#ajax_loading').css('display', 'block');
			jQuery('img#ajax_complete').css('display', 'none');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					vc_method: 'venue_save_layout',
					ci_csrf_token: cct,
					venue_layout: window.json_encode_floorplan()
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					jQuery('img#ajax_loading').css('display', 'none');
					jQuery('img#ajax_complete').css('display', 'block');
					
				}
			});
			
			return false;
			
		});		
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			jQuery('.vlf_title').die('click', edit_floor_dialog);
			
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});