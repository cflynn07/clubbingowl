if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.global_promoter_pusher_notifications = function(oauth_uid){
		
		var oauth_uid = window.module.Globals.prototype.user_oauth_uid;
		
		var vc_team_chat_users = team_chat_object.vc_team_chat_users;
		var team_chat_channel = team_chat_object.team_chat_channel;
				
		console.log('team_chat_channel');
		console.log(team_chat_channel);
				
		team_chat_channel.bind('promoter_guest_list_reservation', function(data){
			
			console.log('promoter_guest_list_reservation');
			console.log(data);
			
			if(data.promoter_oauth_uid != oauth_uid)
				return;		//Event is not for THIS promoter on the team
			
			if(data.manual_add == 1 || data.manual_add == '1')
				return;		//Event fired for manually initiated event by promoter
				
			// ------------- Assemble all UIDS of all users in request ------------
			if(Object.prototype.toString.call(data.entourage) === '[object Array]')
				var users = data.entourage;
			else
				users = [];
			
			users.push(data.head_oauth_uid);
			// ------------- Assemble all UIDS of all users in request ------------
			
			
			
			
			
			jQuery.fbUserLookup(users, '', function(rows){
				
				//find head user
				var fb_head_user;
				var entourage;
				for(var i=0; i<rows.length; i++){
					if(rows[i].uid == data.head_oauth_uid){
						fb_head_user = rows[i];
						entourage = rows.slice();
						entourage.splice(i, 1);
						break;
					}
				}
				
				//find the promoter
				var promoter;
				for(var i=0; i < vc_team_chat_users.length; i++){
					if(vc_team_chat_users[i].uid == data.promoter_oauth_uid){
						promoter = vc_team_chat_users[i];
						break;
					}
				}
				
				
				
				
				//TODO ----- look into improving this mess
		//		if(!window.vc_fql_users)
		//			window.vc_fql_users = [];
				
				//add users to window.vc_fql_users
		//		for(var i=0; i < rows.length; i++){
		//			window.vc_fql_users.push(rows[i]);
		//		}
				//TODO ----- look into improving this mess
				
				
				
				
				//Growl notification of new guest list request
				var text = '<span style="font-weight:bold;">' + fb_head_user.name + '</span> has requested to join ';
				text += 'your guest list "<span style="text-decoration:underline;">' + data.guest_list_name + '</span>" at '; 
				text += data.venue_name + ' on ' + data.guest_list_date;
				
				if((data.entourage.length - 1) > 0)
					text += ' with ' + (data.entourage.length - 1) + ' friend' + (((data.entourage.length - 1) > 1) ? 's.' : '.');	
				
				if((data.request_msg.length - 1) > 0)
					text += '<br><span style="text-decoration:underline; font-weight:bold;">Request Message</span>: ' + data.request_msg;
							
				var image_gen = function(src){
					return '<img src="' + src + '" alt="" />';
				}
							
				jQuery("div#notification_container").notify('create', 'new-promoter-request', {
					icon: '<img src="' + fb_head_user.pic_square + '" alt="" />',
			   		title: '<span style="color:red;">Guest List Request!</span>',
			   		color: '#FFF',
				    text: text,
				    ent0: (0 < entourage.length) ? image_gen(entourage[0].pic_square) : '',
				    ent1: (1 < entourage.length) ? image_gen(entourage[1].pic_square) : '',
				    ent2: (2 < entourage.length) ? image_gen(entourage[2].pic_square) : '',
				    ent3: (3 < entourage.length) ? image_gen(entourage[3].pic_square) : '',
				    ent4: (4 < entourage.length) ? image_gen(entourage[4].pic_square) : '',
				    ent5: (5 < entourage.length) ? image_gen(entourage[5].pic_square) : '',
				    ent6: (6 < entourage.length) ? image_gen(entourage[6].pic_square) : '',
				    ent8: (7 < entourage.length) ? '...' : ''
				},{
					expires: true,
				    speed: 1000
				});
				
				adminAlertSound.play();
				
			});

		});
				
		
		team_chat_channel.bind('team_guest_list_reservation', function(data){
			
			
		});
		
		
		
		var team_user_presence = team_chat_object.pusher.subscribe('presence-promotervisitors-' + window.module.Globals.prototype.user_oauth_uid);

		team_user_presence.bind('pusher:member_added', function(member){
		  	
		  	console.log('puser:member_added');
		  	console.log(member);
		  	var vc_user = jQuery.cookies.get('vc_user');
		  	
	  		jQuery.fbUserLookup([member.id], '', function(rows){
	  			
	  			for(var i in rows){
					
					var user = rows[i];				
					
					if(user.uid == vc_user.vc_oauth_uid)
						continue;
					
					jQuery("div#notification_container").notify("create", {
						icon: '<img src="' + user.pic_square + '" alt="" />',
				   		title: '<span style="color:blue;">' + user.name + '</span>',
				   		color: '#FFF',
				   		text: user.name + ' is viewing your promoter profile.'
					},{
						expires: true,
					    speed: 1000
					});
					
				}
				
	  			
	  		});
			  	
		});	
		
	};
	
	window.vc_page_scripts.global_promoter_pusher_notifications();
	
});