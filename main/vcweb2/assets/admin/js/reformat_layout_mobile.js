jQuery(function(){
	
	var initialized = false;
	
	window.vc_page_scripts.reformat_layout_mobile = function(){
	
		
		
		//format actions to happen on all mobile & desktop pages
		jQuery('input.hasDatepicker').attr('readonly', 'readonly');
		
	
	
		if(jQuery.isMobile()){
		
			jQuery('body').css('background', '#F0F0F0');
			jQuery('#primary_left').hide();
			jQuery('#primary_right').css({
				margin: 	0,
				width: 		'980px',
				position: 	'absolute',
				top: 		0,
				left: 		0
			});  
			jQuery('#primary_right > .inner').css({
				padding: '10px',
				margin: 0,
				width: '980px',
				overflow: 'hidden',
				'min-width': 0
			});
			jQuery('#primary_right > .inner > div').css({
				width: 			'980px',
				'max-width': 	'980px'
			});
			
			jQuery('#primary_right > .inner #admin_managers_tables_wrapper > div#tabs').css({
				width: '980px'
			});
			
			jQuery('div.ui-widget ul.ui-tabs-nav li').each(function(){
			
				jQuery(this).css({
					'padding': '5px 0 5px 0',
				}).find('a').addClass('button-action btn-link').css({
					'font-size': '18px'
				});
			
			});
			
			
			jQuery('*[data-mobile_font]').each(function(){
				jQuery(this).css({
					'font-size': jQuery(this).attr('data-mobile_font')
				});
			});
			
			
			var EVT = window.ejs_view_templates_admin_hosts || window.ejs_view_templates_admin_promoters || window.ejs_view_templates_admin_managers;
					
			var mobile_menu = jQuery('#primary_right > #mobile_menu');
			if(!mobile_menu.length){
	
	
				var links = [];
				var prefix = '';
				jQuery('#primary_left div#menu > ul > li').each(function(){
					
					var first_link = jQuery(this).find('> a:first');				
					
					if(jQuery(this).find('> ul').length){
						
						var prefix = jQuery(this).find('a:first > span').html();
						jQuery(this).find('> ul li a').each(function(){
							
							links.push({
								title: 	prefix + ' - ' + jQuery(this).html(),
								href: 	jQuery(this).attr('href')
							});
							
						});
						
					}else{
						
						links.push({
							title: 	first_link.find('> span').html(),
							href: 	first_link.attr('href')
						});
						
					}
									
				});
				
				var html = new EJS({
					text: EVT['admin_mobile_menu']
				}).render({
					links: links
				});
		
				jQuery('#primary_right').prepend(html);
				jQuery('#primary_right #mobile_menu select#mobile_menu_nav').bind('change', function(){
					
					var value = jQuery(this).val();
					jQuery('#primary_left div#menu a[href="' + value + '"]').trigger('click');
					
				});
				
				if(!initialized)
					jQuery.fbUserLookup([window.admin_users_oauth_uid], '', function(rows){
						initialized = true;
						if(rows.length){
							jQuery('#primary_right #mobile_menu span[data-user_name]').html(rows[0].name);
						}
					});
				
				
				jQuery('#primary_right > .inner').css({
					top: '40px'
				});
				
			}
			
		}
		
		/*		
		if( jQuery.isIphone()){
			
			jQuery('#primary_right .inner').css({
				padding: 	'0px',
				width: 		'1000px'
			});
			
			jQuery('#primary_right div.top_lvl').css({
				height: '1000px'
			});
			
			jQuery('#primary_right .inner div#tabs:first').css({
				width: '998px'
			});
			
		}
		*/
		
	}
	
});
