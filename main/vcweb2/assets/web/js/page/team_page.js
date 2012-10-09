if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	window.vc_page_scripts.team_page = function(){
				
				
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];		
				
				
		var team_users = [
			'504405294',	//Casey
			'1634269784', 	//Federico
			'588503245',	//Miguel
			'691334780',	//Robert
			'500418954',	//Anabella
			'648325230',	//Christophe
			'1324316721', 	//Andrew H
			'626553085',	//Cameron
			'1165086512',	//Edwin
			'501519084',	//Mete
			'502601135',	//Chris
			'512158003',	//Johann
			'1467390458'	//Evelyn
		];
		
		var fb_callback = function(rows){
			
			for(var i in rows){
				jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="' + rows[i].name + '" />');
			}
			
		};
		jQuery.fbUserLookup(team_users, 'uid, pic_square, name', fb_callback);
		facebook_callbacks.push(fb_callback);
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			
			for(var i in unload_items){
				unload_items[i][1].unbind(unload_items[i][0], unload_items[i][2]);
			}
			
			for(var i in timeout_cancels){
				clearTimeout(timeout_cancels[i]);
			}
			
			for(var i in custom_events_unbind){
				custom_events_unbind[i][1].removeListener(custom_events_unbind[i][0], custom_events_unbind[i][2]);
			}
			
			for(var i in facebook_callbacks){
				facebook_callbacks[i] = function(){};
			}
			
		}
			
			
			
	}
});