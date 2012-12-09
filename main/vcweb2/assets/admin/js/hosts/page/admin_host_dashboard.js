if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_host_dashboard = function(){
		
		console.log('admin_host_dashboard()');
		if(!window.page_obj || !window.page_obj.backbone)
			return false;
			
			
			
		
		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		
		
		console.log(window.page_obj);	
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			
		}
		
	}; 

});