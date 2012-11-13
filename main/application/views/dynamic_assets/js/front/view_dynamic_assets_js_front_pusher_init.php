jQuery(function(){
	
	Pusher.channel_auth_endpoint = '/ajax/pusher_presence/';
	
	<?php // add ci_csrf_token ?>
	Pusher.authorizers.ajax=function(a,b){var c=this,d;if(Pusher.XHR){d=new Pusher.XHR}else{d=window.XMLHttpRequest?new window.XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP")}d.open("POST",Pusher.channel_auth_endpoint,true);d.setRequestHeader("Content-Type","application/x-www-form-urlencoded");d.onreadystatechange=function(){if(d.readyState==4){if(d.status==200){var a,c=false;try{a=JSON.parse(d.responseText);c=true}catch(e){b(true,"JSON returned from webapp was invalid, yet status code was 200. Data was: "+d.responseText)}if(c){b(false,a)}}else{Pusher.warn("Couldn't get auth info from your webapp",status);b(true,d.status)}}};var e=jQuery.cookies.get("ci_csrf_token")||"no_csrf";d.send("socket_id="+encodeURIComponent(a.connection.socket_id)+"&channel_name="+encodeURIComponent(c.name)+"&ci_csrf_token="+e)}
	
	var indiv_notification_callback = function(data){
		
		console.log('individual_notification_callback');
		console.log(data);

		if(!data.notification_type)
			return;
			
		switch(data.notification_type){
			case 'friend_online':
				fbEnsureInit(function(){
										
					jQuery.fbUserLookup([data.friend], 'uid, name, pic_square, third_party_id', function(rows){
						if(!(rows.length > 0))
							return;
							
						var user = rows[0];
						var user_link = window.module.Globals.prototype.front_link_base + 'friends/' + user.third_party_id + '/';
						
						jQuery("div#notification_container").notify("create", {
							icon: '<img src="' + user.pic_square + '" alt="picture" />',
					   		title: user.name,
					   		color: '#FFF',
						    text: user.name + ' has signed into ClubbingOwl.'
						},{
						    speed: 1000,
						    expires: 1000 * 30,
						    click: function(){
						    	
						    	jQuery('body').find('a#background_link').remove();
						    	jQuery('body').append('<a id="demo_link" style="display:none;" href="' + user_link + '">demo_link</a>');
						    	jQuery('a#demo_link').trigger('click');
						    	
						    }
						});
					});
					
					
					
				});
				break;
			case 'invitation':
				
				window.new_invitations(data.all_invitations);
				
				break;
			case 'friend_join_gl':
				break;
			
			case 'request_response':
				
				var pass_data = [];
				pass_data.push(data);
				window.VC_Global_Event_Callbacks.request_response(pass_data);

				break;

			case 'super_admin_user_message':
			
				jQuery("div#notification_container").notify("create", {
					icon: '<img src="' + data.pic + '" alt="picture" />',
			   		title: data.title,
			   		color: '#FFF',
				    text: data.message
				},{
				    expires: 3000
				});
				
			
				break;
			
			default:
				break;
		}
		
	}
	
	var notification_callback = function(data){
		
		console.log(data);
		alert(data);
		
	}
	
	var individual_channel_subscribe_callbacks = function(ic){
		
		ic.bind('notification', indiv_notification_callback);
		
	}
	
	var channel_subscribe_callbacks = function(c){
		
		c.bind('notification', notification_callback);
		
	}
	
	var pusher = new Pusher('<?= $this->config->item('pusher_api_key') ?>');
	var global_channel = pusher.subscribe('vc_global');
	channel_subscribe_callbacks(global_channel);
	
	var individual_channel;
	jQuery('div#notification_container').notify();

	var vc_user = jQuery.cookies.get('vc_user');
	if(vc_user){
		individual_channel = pusher.subscribe('private-vc-' + vc_user.vc_oauth_uid);
		individual_channel_subscribe_callbacks(individual_channel);
	}
	
	window.EventHandlerObject.addListener("vc_login", function(){
		
		if(typeof individual_channel !== 'undefined')
		if(typeof individual_channel.name !== 'undefined'){
			pusher.unsubscribe(individual_channel.name);
		}

		var vc_user = jQuery.cookies.get('vc_user');
		console.log('vc_login pusher event');
		console.log(vc_user);
		
		if(vc_user){
			individual_channel = pusher.subscribe('private-vc-' + vc_user.vc_oauth_uid);
			individual_channel_subscribe_callbacks(individual_channel);
		}
			
	});
	
	window.EventHandlerObject.addListener("vc_logout", function(){
	
		if(typeof individual_channel.name !== 'undefined'){
			pusher.unsubscribe(individual_channel.name);
		}
		
	});
	
});