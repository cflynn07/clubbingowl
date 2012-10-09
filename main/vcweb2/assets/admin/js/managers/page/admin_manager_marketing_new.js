if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_marketing_new = function(){
						
		var unbind_callbacks = [];		


				
		jQuery('img.tooltip').tooltip();		
	
	
	
	
	
	
	
		
		//http://jsfiddle.net/qm4G6/84/
		var $ = jQuery;
		var cledit = $("#inputcledit").cleditor({
			width: '600px',
			height: '400px'
		})[0];
	    $(cledit.$frame[0]).attr("id","cleditCool");
	    
	    var cleditFrame;
	    if(!document.frames)
	    {
	       cleditFrame = $("#cleditCool")[0].contentWindow.document;
	    }
	    else
	    {
	        cleditFrame = document.frames["cleditCool"].document;
	    }
	    
	    var change_callback = function(value){
	    	
	    	jQuery('td#content_preview_td').html(value);
	    	
	    }
	    
	    $(cleditFrame ).bind('keyup', function(){
	    	var v = $(this).find("body").html();
	        change_callback(v);   
	    });
	    
	    $("div.cleditorToolbar").bind("click",function(){
	         var v = $( cleditFrame ).find("body").html();
	         change_callback(v);
	    });
						
				
				
		
		
		
		
		jQuery('a#send_campaign').bind('click', function(){
			
			var campaign_title 		= jQuery.trim(jQuery('input#campaign_title').val());
			var campaign_content 	= jQuery.trim(jQuery('#content_preview_td').html());
			
			
			if(campaign_title.length === 0){
				alert('Campaign title must not be blank.');
				return;
			}
			
			if(campaign_content.length === 0){
				alert('Campaign content must not be blank.');
				return;
			}
			
			
			jQuery('#confirm_dialog').dialog({
				resizable: false,
				height: 200,
				modal: true,
				buttons: {
					Cancel: function(){
						jQuery(this).dialog('close');
					},
					Send: function(){
						
						
						jQuery('#confirm_dialog img.loading_indicator').css('display', 'block');
						var _this = this;
						
						
						//cross-site request forgery token, accessed from session cookie
						//requires jQuery cookie plugin
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
							 	ci_csrf_token: 		cct,
								vc_method: 			'marketing_campaign_create',
								campaign_title: 	campaign_title,
								campaign_content: 	campaign_content
					 		},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
								console.log(data);
								
								jQuery('#confirm_dialog img.loading_indicator').css('display', 'none');
								jQuery(_this).dialog('close').dialog('destroy');						
								jQuery('a#back_btn').trigger('click');
								
								
							}
						});
						
						
					}
				}
			});
			
		
			//content_preview_td
			//campaign_title
			
			return false;
		});
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});