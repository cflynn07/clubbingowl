if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.promoter_all = function(){

		var friend_add = jQuery('#add_as_friend');
		
		var unbind_method = function(){
			friend_add.unbind();
		};
		unbind_method();
		
		
		
		
		
		
		jQuery('#add_as_friend').css({
			cursor: 'pointer'
		}).bind('click', function(){
			
			fbEnsureInit(function(){
				
				FB.ui({
				    method: 'friends.add',
				    id: 	window.vc_promoter_oauth
				}, function(param) {
					
				}); 
				
			});
			
		});
		
		
		
	}
	
});