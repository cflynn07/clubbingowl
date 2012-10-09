jQuery(function(){
	
	var vc_user = jQuery.cookies.get('vc_user');
		
	if(!vc_user)
		return;
		
	if(window.vc_user_invitations.length == 0)
		return;
		
	var new_invitations = window.vc_user_invitations;
	var unseen_invitations = new_invitations.length;
	
	if(!vc_user.invitations){
		
		//the user has new invitations
		vc_user.invitations = new_invitations;
		jQuery.cookies.set('vc_user', vc_user);
		window.module.VCAuth.prototype.display_invitations();
		
	}else{
		
		//user has invitations, are there any new invitations? 
		for(var i = 0; i < vc_user.invitations.length; i++){
			
			for(var j = 0; j < new_invitations.length; j++){
				
				if(vc_user.invitations[i].ui_id == new_invitations[j].ui_id){
					unseen_invitations--;
				}
				
			}
			
		}		
		if(unseen_invitations > 0){
			
			//There are new invitations this user hasn't seen yet
			vc_user.invitations = window.vc_user_invitations;
			jQuery.cookies.set('vc_user', vc_user);
			window.module.VCAuth.prototype.display_invitations();
			
		}
		
	}
	
});