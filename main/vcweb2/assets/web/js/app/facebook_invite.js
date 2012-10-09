jQuery(function(){
	jQuery('aside#invite a.invite').live('click', function(){
		
		fbEnsureInit(function(){
			
			var vc_user = jQuery.cookies.get('vc_user');
			if(!vc_user){
				var message = 'Check out VibeCompass! A great new way to join guest lists and book tables at your favorite venues!';
			}else{
				var message = vc_user.first_name + ' thinks you should check out VibeCompass, a great new way to join guest lists and book tables at your favorite venues!';
			}
			
			var data = {
				type: 1
			};
			
			FB.ui({
				method: 'apprequests',
				title: 'Invite your friends to VibeCompass',
				message: message,
				data: JSON.stringify(data)
			  }, function(){});
			  
		});
		
		return false;
	});
});