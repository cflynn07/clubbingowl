if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.suite_re_init = function(){
		
		Cufon.replace('h1, h2, h5, .notification strong', { hover: 'true' }); // Cufon font replacement
	
		
		jQuery("#primary_right .tablesorter").tablesorter(); // Tablesorter plugin
				
		jQuery('#primary_right #dialog').dialog({
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
				
		jQuery('#primary_right .dialog_link').click(function(){
			jQuery('#dialog').dialog('open');
			return false;
		}); // Toggle dialog
		
		jQuery('#primary_right .notification').hover(function() {
	 		jQuery(this).css('cursor','pointer');
	 	}, function() {
			jQuery(this).css('cursor','auto');
		}); // Close notifications
				
		jQuery('#primary_right .checkall').click(
			function(){
				jQuery(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', jQuery(this).is(':checked'));   
			}
		); // Top checkbox in a table will select all other checkboxes in a specified column
				
		jQuery('#primary_right .iphone').iphoneStyle(); //iPhone like checkboxes
	
		jQuery('#primary_right .notification span').click(function() {
			jQuery(this).parents('.notification').fadeOut(800);
		}); // Close notifications on clicking the X button
				
		jQuery("#primary_right .tooltip").easyTooltip({
			xOffset: -60,
			yOffset: 70
		}); // Tooltips! 
				
								
		jQuery('#primary_right .fade_hover').hover(
			function() {
				jQuery(this).stop().animate({opacity:0.6},200);
			},
			function() {
				jQuery(this).stop().animate({opacity:1},200);
			}
		); // The fade function
				
		//sortable, portlets
		jQuery("#primary_right .column").sortable({
			connectWith: '.column',
			placeholder: 'ui-sortable-placeholder',
			forcePlaceholderSize: true,
			scroll: false,
			helper: 'clone'
		});
					
		jQuery("#primary_right .portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all").find(".portlet-header").addClass("ui-widget-header ui-corner-all").prepend('<span class="ui-icon ui-icon-circle-arrow-s"></span>').end().find(".portlet-content");
	
		jQuery("#primary_right .portlet-header .ui-icon").click(function() {
			jQuery(this).toggleClass("ui-icon-minusthick");
			jQuery(this).parents(".portlet:first").find(".portlet-content").toggle();
		});
	
		jQuery("#primary_right .column").disableSelection();
		
		jQuery("#primary_right table.stats").each(function() {
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
				
		jQuery("#primary_right .tabs").tabs(); // Enable tabs on all '.tabs' classes
		
		jQuery( "#primary_right .datepicker" ).datepicker();
		
		jQuery("#primary_right .editor").cleditor({
			width: '800px'
		}); // The WYSIWYG editor for '.editor' classes
		
		// Slider
		jQuery("#primary_right .slider").slider({
			range: true,
			values: [20, 70]
		});
					
		// Progressbar
		jQuery("#primary_right .progressbar").progressbar({
			value: 40 
		});
	}
});