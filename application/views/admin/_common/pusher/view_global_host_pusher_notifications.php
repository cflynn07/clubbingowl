<?php
	/**
	 * Host Pusher Notifications
	 * 
	 * 		Notifications from pusher that are present on all host admin panel pages
	 * 		EX: A host has checked in a group
	 */
?>
<script type="text/javascript">
jQuery(function(){
	
	
	//TODO... expand on this
	var pusher_init = function(){
				
		
		team_chat_channel.bind('promoter_guest_list_reservation', function(data){
			
		});
		
		
		team_chat_channel.bind('team_guest_list_reservation', function(data){
			
		});
		
		
	};
	
	window.EventHandlerObject.addListener('pusher_init', pusher_init);
	
})
</script>
