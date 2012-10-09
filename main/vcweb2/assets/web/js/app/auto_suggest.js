jQuery(function(){
	var base_path = window.module.Globals.prototype.karma_assets;

	//var image_base_path = 'http://vcweb2.s3.amazonaws.com/vc-images/profile-pics/';
	var image_base_path = window.location.protocol + '//cdn{-#-}.vibecompass.' + 'com/vc-images/';
	
	window.app_friends = false;
	
  	jQuery('#search-input')
  		.focus(function(){
    		jQuery(this).val('');
  		})
  		.bind("keypress", function(e){
		    if(e.keyCode == 13) {
		        return false;	//no submit on enter
		    }
		});
	 
	jQuery('body').click(function(e) {
		
	    if(e.target.id != "search-drop" && jQuery(e.target).parents('#search-drop').length === 0) {
	    	
	    	jQuery('#search-input').val('');
		 	jQuery('#search-drop-results').css('display', 'none');
			jQuery('#search-drop-results > ul li:not(li.search_header)').remove();
			jQuery('#search-drop-results > ul li.search_header').css('display', 'none');
	    	
	    }
	});
	 	
	jQuery('#search-input').submit(function(e){
		e.preventDefault();
	 	return false;
	});
	
	jQuery('#search-input').autocomplete({
		source: function(request, response) {
			
			jQuery('#search-drop-results').css('display', 'block');
			jQuery('form#search > input').addClass('searching');
			
				
			if(!app_friends)	
				fbEnsureInit(function(){
					var query = FB.Data.query('SELECT name, uid, pic_square, third_party_id FROM user WHERE uid IN (SELECT uid1 FROM friend WHERE uid2 = me()) AND is_app_user = 1');
					query.wait(function(rows){
					    app_friends = rows;
					});
				});
			
			
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';	
					
			jQuery.ajax({
				url: '/ajax/auto_suggest/',
				type: 'post',
				dataType: 'json',
				data: {
					ci_csrf_token: cct,
					vc_method: 'find_completions',
					search_pattern: request.term
				},
				success: function(data){
				
					var concat_fb_api = function(){
											
						//wait on FB api Query
						if(app_friends == false && jQuery.cookies.get('vc_user')){
							
							window.setTimeout(concat_fb_api, 40);
							
						}else{
							
							if(app_friends != false){
								
								var matches = new Array();
								
								for(var i=0; i < app_friends.length; i++){
									
									if(app_friends[i].name.toLowerCase().indexOf(request.term.toLowerCase()) != -1){
										
										matches.push(app_friends[i]);
										
									}
									
								}
								
								//filter duplicates of promoters + friends
								for(var i = 0; i < data.length; i++){
									for(var k = 0; k < matches.length; k++){
										
										if(data[i].oauth_uid == matches[k].uid){
											//this app-friend is also a promoter
											
											matches.splice(k, 1);
											
										}
										
									}
								}
														
								data = jQuery.merge(data, matches);
								
							}
							
							// great now we know that the response needs to be an array of objects
							//data = new Array({'id': 'test id', 'label': 'test label', 'value': 'supervalue!'});
				
							jQuery('form#search > input').removeClass('searching');
							jQuery('#search-drop-results > ul li.search_header').css('display', 'none');
							jQuery('#search-drop-results > ul li:not(li.search_header)').remove();
							
							if(data.length === 0)
								jQuery('#search-drop-results > ul').append('<li class="no_select">' + jQuery('div#no_search_results_msg').html().replace('<%=term%>', request.term) + '</li>');
							
							response(data);
							
						}
						
					}
					
					concat_fb_api();

				}
			});
			
		},
		minLength: 2,
		select: function( event, ui ) {
			
		/*	if(ui.item.uid){
				window.location = window.module.Globals.prototype.front_link_base + 'friends/' + ui.item.third_party_id + '/';
			}else{
				window.location = window.module.Globals.prototype.front_link_base + 'promoters/' + (ui.item.id).toLowerCase() + '/';
			}		
		*/
			
		},
		open: function() {
			jQuery('#div .ui-menu').width(200);
			console.log('open');
		},
		close: function() {
			console.log('close');
		}
	})
	.data( "autocomplete" )
	._renderItem = function( ul, item ) {
		
		//override ul
		ul = jQuery('#search-drop-results > ul');
		
		if(item.uid){
			//item is facebook user
			return jQuery( '<li></li>' )
	            .data( "item.autocomplete", item )
	            .append( "<a href='" + window.module.Globals.prototype.front_link_base + "friends/" + item.third_party_id + "' >" + "<img src='" + item.pic_square + "' style='vertical-align:text-top; float:left; margin-right:4px;'/><div><span>" + item.name + "</span><br /></div><div style='clear:both;'></div></a>" )
	            .insertAfter(ul.find('li.search_friends').css('display', 'list-item'));
	       //     .appendTo( ul.find('li') );
		}else{
			//item is vibecompass
			
			
			
			
			if(item.value == 'Promoter'){
				
				var cdn_num = (item.up_id % 10);
				var mod_base_path = image_base_path.replace('{-#-}', cdn_num);
								
				var promoter_html = "<a href='" + window.module.Globals.prototype.front_link_base +  "promoters/" + item.c_url_identifier + "/" + item.id + "' >" + "<img src='" + mod_base_path + 'profile-pics/' + item.thumb + "_t.jpg' style='vertical-align:text-top; float:left; margin-right:4px;'/><div><span>" + item.label + "</span><br />";
				
				for(var i in item.p_venues){
					promoter_html += "<span class='subtext'>" + item.p_venues[i].tv_name + "</span><br>";
				}
				
				promoter_html += "</div><div style='clear:both;'></div></a>";
				
				return jQuery( '<li></li>' )
		            .data( "item.autocomplete", item )
		            .append( promoter_html )
		            .insertAfter(ul.find('li.search_promoters').css('display', 'list-item'));
		            
	       }else if(item.value == 'Venue'){
	       	
	       		
	       		
	       		
	       		
	       		var cdn_num = (item.tv_id % 10);
				var mod_base_path = image_base_path.replace('{-#-}', cdn_num);
								
				var venue_html = "<a href='" + window.module.Globals.prototype.front_link_base +  "venues/" + item.c_url_identifier + "/" + item.tv_name.replace(/ /g,'_') + "' >" + "<img src='" + mod_base_path + 'venues/banners/' + item.tv_image + "_t.jpg' style='vertical-align:text-top; float:left; margin-right:4px; width:130px;'/><div><span>" + item.tv_name + "</span><br />";
				
				venue_html += "<span class='subtext'>" + item.c_name + ', ' + item.c_state + "</span><br>";
				
				
				venue_html += "</div><div style='clear:both;'></div></a>";
				
				return jQuery( '<li></li>' )
		            .data( "item.autocomplete", item )
		            .append( venue_html )
		            .insertAfter(ul.find('li.search_venues').css('display', 'list-item'));
	       		
	       		
	       		
	       		
	       		
	       		
	       	
	       }else if(item.value == 'Guestlist'){
	       	
	       	
	       		if(item.gl_type == 'promoter'){
	       			
	       			
	       			var cdn_num = (item.gl_id % 10);
					var mod_base_path = image_base_path.replace('{-#-}', cdn_num);
									
					var list_html = "<a href='" + window.module.Globals.prototype.front_link_base +  "promoters/" + item.c_url_identifier + "/" + item.up_public_identifier + "/guest_lists/" + item.gl_name.replace(/ /g, '_') + "' >" + "<img src='" + mod_base_path + 'guest_lists/' + item.gl_image + "_t.jpg' style='vertical-align:text-top; float:left; margin-right:4px;'/><div><span>" + item.gl_name + "</span><br />";
				
					list_html += "<span class='subtext'>@ " + item.tv_name + "</span><br>";
					list_html += "<span class='subtext'>" + item.c_name + ', ' + item.c_state + "</span><br>";
					list_html += "<span class='subtext'>" + item.occurance_date + "</span><br>";
					
					list_html += "</div><div style='clear:both;'></div></a>";
										
					return jQuery( '<li></li>' )
			            .data( "item.autocomplete", item )
			            .append( list_html )
			            .insertAfter(ul.find('li.search_lists').css('display', 'list-item'));
	       			
	       			
	       		}else if(item.gl_type == 'venue'){
	       			
	       			
	       			var cdn_num = (item.gl_id % 10);
					var mod_base_path = image_base_path.replace('{-#-}', cdn_num);
									
					var list_html = "<a href='" + window.module.Globals.prototype.front_link_base +  "venues/" + item.c_url_identifier + "/" + item.tv_name.replace(/ /g, '_') + "/guest_lists/" + item.gl_name.replace(/ /g, '_') + "' >" + "<img src='" + mod_base_path + 'guest_lists/' + item.gl_image + "_t.jpg' style='vertical-align:text-top; float:left; margin-right:4px;'/><div><span>" + item.gl_name + "</span><br />";
				
					list_html += "<span class='subtext'>@ " + item.tv_name + "</span><br>";
					list_html += "<span class='subtext'>" + item.c_name + ', ' + item.c_state + "</span><br>";
					list_html += "<span class='subtext'>" + item.occurance_date + "</span><br>";
					
					list_html += "</div><div style='clear:both;'></div></a>";
										
					return jQuery( '<li></li>' )
			            .data( "item.autocomplete", item )
			            .append( list_html )
			            .insertAfter(ul.find('li.search_lists').css('display', 'list-item'));
	       			
	       			
	       		}

	       	
	       	
	       }
    
		}
				
    };

});