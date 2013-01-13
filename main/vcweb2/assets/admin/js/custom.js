jQuery.noConflict();

function initMenu() {
    jQuery('#menu ul ul').hide();
	jQuery('#menu ul li').click(function() {
		jQuery(this).parent().find("ul").slideUp('fast');
		jQuery(this).parent().find("li").removeClass("current");
		jQuery(this).find("ul").slideToggle('fast');
		jQuery(this).toggleClass("current");
 	});
}
 
 
jQuery(document).ready(function() {
	

//	Cufon.replace('h1, h2, h5, .notification strong', { hover: 'true' }); // Cufon font replacement
//	initMenu(); // Initialize the menu!
	
//	jQuery(".tablesorter").tablesorter(); // Tablesorter plugin
			
			
			
	/*		
	jQuery('#dialog').dialog({
		autoOpen: false,
		width: 650,
		buttons: {
			"Done": function() { 
				jQuery(this).dialog("close"); 
			}, 
			"Cancel": function() { 
				jQuery(this).dialog("close"); 
			} 
		}
	}); // Default dialog. Each should have it's own instance.
	*/	
			
	/*	
	jQuery('.dialog_link').click(function(){
		jQuery('#dialog').dialog('open');
		return false;
	}); // Toggle dialog
	*/
	
	
	
	/*
	jQuery('.notification').hover(function() {
 		jQuery(this).css('cursor','pointer');
 	}, function() {
		jQuery(this).css('cursor','auto');
	}); // Close notifications
	*/	
			
			
			
	/*
	jQuery('.checkall').click(
		function(){
			jQuery(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', jQuery(this).is(':checked'));   
		}
	); // Top checkbox in a table will select all other checkboxes in a specified column
	*/
	
		
//	jQuery('.iphone').iphoneStyle(); //iPhone like checkboxes

//	jQuery('.notification span').click(function() {
//		jQuery(this).parents('.notification').fadeOut(800);
//	}); // Close notifications on clicking the X button
			
			
			
//	jQuery(".tooltip").easyTooltip({
//		xOffset: -60,
//		yOffset: 70
//	}); // Tooltips! 
			
			
			
	
	
/*			
	jQuery('#menu li:not(".current"), #menu ul ul li a').hover(function() {
		jQuery(this).find('span').animate({ marginLeft: '5px' }, 100);
	}, function() {
		jQuery(this).find('span').animate({ marginLeft: '0px' }, 100);           
	}); // Menu simple animation
			
	jQuery('.fade_hover').hover(
		function() {
			jQuery(this).stop().animate({opacity:0.6},200);
		},
		function() {
			jQuery(this).stop().animate({opacity:1},200);
		}
	); // The fade function
			
	//sortable, portlets
	jQuery(".column").sortable({
		connectWith: '.column',
		placeholder: 'ui-sortable-placeholder',
		forcePlaceholderSize: true,
		scroll: false,
		helper: 'clone'
	});
				
	jQuery(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all").find(".portlet-header").addClass("ui-widget-header ui-corner-all").prepend('<span class="ui-icon ui-icon-circle-arrow-s"></span>').end().find(".portlet-content");

	jQuery(".portlet-header .ui-icon").click(function() {
		jQuery(this).toggleClass("ui-icon-minusthick");
		jQuery(this).parents(".portlet:first").find(".portlet-content").toggle();
	});

	jQuery(".column").disableSelection();
	
	jQuery("table.stats").each(function() {
		if(jQuery(this).attr('class')) { var statsType = jQuery(this).attr('class').replace('stats ',''); }
		else { var statsType = 'area'; }
		
		var chart_width = (jQuery(this).parent().parent(".ui-widget").width()) - 60;
		jQuery(this).hide().visualize({		
			type: statsType,	// 'bar', 'area', 'pie', 'line'
			width: '800px',
			height: '240px',
			colors: ['#6fb9e8', '#ec8526', '#9dc453', '#ddd74c']
		}); // used with the visualize plugin. Statistics.
	});
*/	
	
	
	
	
			
//	jQuery(".tabs").tabs(); // Enable tabs on all '.tabs' classes
	
//	jQuery( ".datepicker" ).datepicker();
	
//	jQuery(".editor").cleditor({
//		width: '800px'
//	}); // The WYSIWYG editor for '.editor' classes
	
	// Slider
/*	jQuery(".slider").slider({
		range: true,
		values: [20, 70]
	});
				
	// Progressbar
	jQuery(".progressbar").progressbar({
		value: 40 
	});
*/
	
	
	
	
	
	
	
	
	
	
	
	
//----------------------------------------------------------------------
	//special fix for browsers with viewports that cannot fit entire menu
	var test_set_relative_menu = function(){
	
		var pl_height = jQuery('div#primary_left').height();
		var win_height = jQuery(window).height();
		
		if(pl_height > (win_height - 60)){
			jQuery('div#primary_left').css('position', 'relative');
		}else{
			jQuery('div#primary_left').css('position', 'fixed');
		}
		
	}
	jQuery(function(){
		jQuery(window).resize(function(){
			test_set_relative_menu();
		});
		
		test_set_relative_menu();
	});
//----------------------------------------------------------------------
	

	
	fbEnsureInit(function(){
		
		var vc_user = jQuery.cookies.get('vc_user');
		var user;
		
		var oauth_uid_to_query = admin_users_oauth_uid || vc_user.vc_oauth_uid;
		
		var fql = "SELECT uid, name, pic_square FROM user WHERE uid = " + oauth_uid_to_query;
		
		
		FB.api({
			method: 'fql.query', 
			query: fql
		}, function(rows){
			
			console.log('--------');
			console.log(rows);
			
			if(rows.length == 0)
				return;
			
			user = rows[0];
			
			var vc_user = jQuery.cookies.get('vc_user');
			if(vc_user.vc_admin_user){
				
				var vc_admin_user = vc_user.vc_admin_user;
				
				if(!vc_admin_user.name || (vc_admin_user.name != user.name) || (vc_admin_user.pic_square != user.pic_square) || (vc_admin_user.uid != user.uid)){
				
					vc_admin_user = {};
					vc_admin_user.name = user.name;
					vc_admin_user.pic_square = user.pic_square;
					vc_admin_user.uid = user.uid;
					
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);
					admin_display_user();
				}
				
			}else{
				
				user.chat_open = false;
				user.unread = 0;
				vc_user.vc_admin_user = user;
				
				jQuery.cookies.set('vc_user', vc_user);
				admin_display_user();
				
			}
		});
				

	});
	
	
	
	
	
	
	
	
	
	
	
///	jQuery('img.tooltip').tooltip();
	
	
	jQuery.extend({
		
	    isIpad: function(){
	        return navigator.userAgent.match(/ipad/i) != null;
	    },
	    isMobile: function(){
	    	
	    	return /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
	    	
	    },
	    isIphone: function(){
	    	
	    	return /iPhone|iPod/i.test(navigator.userAgent);
	    	
	    },
	    populateFacebook: function(el, callback){
	    	
	    	console.log(el.find('*[data-oauth_uid]'));
	    	
	    	
	    	var oauth_uids = [];
	    	el.find('*[data-oauth_uid]').each(function(){
	    		
	    		oauth_uids.push(jQuery(this).attr('data-oauth_uid'));
	    		    		
	    	});
	    	
	    	
	    	
	    	
	    	oauth_uids = _.uniq(oauth_uids);
	    	
	    	console.log('unique_oauth_uids');
	    	console.log(oauth_uids);
	    	
	    	
	    	jQuery.fbUserLookup(oauth_uids, '', function(rows){
	    		
	    		for(var i in rows){
	    			
	    			var user = rows[i];
	    			
	    			el.find('*[data-name=' + user.uid + ']').html(user.name);
	    			
	    		}
	    		
	    		callback();
	    		
	    	});
	    	
	    },
	    /**
	     * FQL query to retrieve user info for given uids
	     */
	    fbUserLookup: function(users, fields, callback){
	    	
	    	fbEnsureInit(function(){
	    			    		
	    		if(users && users.length && users.length > 0){
	    			
	    			if(fields.length == 0)
	    				fields = "uid, name, first_name, last_name, pic_square, pic_big";
	    			
	    			var fql = "SELECT " + fields + " FROM user WHERE ";
	    			for(var i = 0; i < users.length; i++){
						if(i == (users.length - 1)){
							fql += "uid = " + users[i];
						}else{
							fql += "uid = " + users[i] + " OR ";
						}
					}
					
	    			
	    			FB.api({
						method: 'fql.query', 
						query: fql
					}, function(rows){
						callback(rows);
					});
					
	    			
	    		}else{
    			
	    			callback([]);
	    			
	    		}
	    	});
	    },
	    /**
	     * Outputs the HTML for a clickable client in 
	     */
	    vcClientGen: function(user){
	    	
	    }
	});
	
});




window.zebraRows = function(){
	jQuery('table.normal > tbody > tr').removeClass('odd');
	jQuery('table.normal > tbody > tr:odd').addClass('odd');
};

window.admin_display_user = function(){
	var vc_user = jQuery.cookies.get('vc_user');
	var vc_admin_user = vc_user.vc_admin_user;
	
	if(vc_admin_user && vc_admin_user.pic_square && vc_admin_user.name){
		var display_user = jQuery('div#primary_left div#logo div#display_user');
		display_user.find('img.pic_square').attr('src', vc_admin_user.pic_square);
		display_user.find('span.name').html(vc_admin_user.name);
		display_user.css('display', 'block');
	}
};