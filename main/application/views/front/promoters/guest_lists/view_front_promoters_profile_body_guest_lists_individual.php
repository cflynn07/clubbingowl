<style type="text/css">
	div#unavailable_overlay{
		display: <?= (isset($vc_user)) ? 'none' : 'block' ?>;
	}
</style>

<section id="guestlist">
	
  <h1><?= $this->lang->line('p-gl_t') ?></h1>
 
  <div class="guestlist-form" style="display: block; opacity: 1; ">
  	
	<?php //Kint::dump($guest_list); ?>
		
    <p id="gl_title_header" style="font-size:18px;">
    	    	
    	<?= lang_key($this->lang->line('p-gl_join_msg'), array(
			'promoter_u_full_name' 				=> $promoter->u_full_name,
			'guest_list_pgla_name' 				=> $guest_list->pgla_name,
			'central_front_link_base'			=> $central->front_link_base,
			'promoter_team_c_url_identifier' 	=> $guest_list->c_url_identifier,
			'guest_list_tv_name_l'				=> str_replace(' ', '_', $guest_list->tv_name),
			'guest_list_tv_name' 				=> $guest_list->tv_name
		)) ?>
		
	</p>
	
	
	
	
	<?php if($guest_list->pgla_event == '1'):
		$time = strtotime($guest_list->pgla_event_date);
	?>
	
		<h2 style="text-align:center; width:100%; margin-bottom:30px;"><?= date('l F j, Y', $time) ?></h2>
		
	<?php endif; ?>
	
	
	
	
<?php if(false): ?>
    <?= Kint::dump($venue_floorplan) ?>
    <?= Kint::dump($guest_list) ?>
    <?= Kint::dump($promoter) ?>
    <?= Kint::dump($central) ?>
<?php endif; ?>
    
    
    
    <?php if(false): ?>
    <h2>List Description</h2>
    <p style="border:1px dashed #CCC; padding:5px;"><?= $guest_list->pgla_description ?></p>
	<?php endif; ?>


    <div class="guestlist-form-left">
      	
      <div style="text-align:center;" class="guestlist-form-image">
        <img id="gl_image" src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $guest_list->pgla_image . '_p.jpg' ?>" style="width:188px; height:266px; border-radius:10px;" alt="Event Image">
      </div>
      
      
      <p>
        <h2>Venue</h2>
        <div style="margin-left:auto; margin-right:auto; max-width:188px;">
	        	
	      	<img style="max-width:188px; border:1px solid #CCC;" src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $guest_list->tv_image ?>_t.jpg" alt="<?= $guest_list->tv_name ?>" /><br>
	        <strong><a href="<?= $central->front_link_base . 'venues/' . $guest_list->c_url_identifier . '/' . str_replace(' ', '_', $guest_list->tv_name) . '/'?>"><?= $guest_list->tv_name ?></a></strong><br>
	        <?= $guest_list->tv_street_address ?><br>
	        <?= $guest_list->tv_city?>, <?= $guest_list->tv_state ?> <?= $guest_list->tv_zip ?>      
	        
        </div>  
      </p>
      
      
      
      <p>
      	<h2>Info</h2>
      	
      	
      	
      	<?php
      		$time_mod = function($hour){
      			
				$hour = intval($hour);
      						
				if($hour > 12){
					return ($hour - 12) . "pm";
				}else{
					
					if($hour === 12)
						return "12pm";
						
					if($hour === 0)
						return "12am";
					else
						return $hour . "am";
					
				}
				
      		};
      	?>
      	
      	
      	
      	<table id="list_info">
      		<tbody>
      			<tr>
      				<td style="font-weight:500; text-align:left;">Minimum Age</td>
      				<td style="text-align:right;"><?= $guest_list->pgla_min_age ?></td>
      			</tr>
      			<tr>
      				<td style="font-weight:500; text-align:left;">Regular Cover</td>
      				<td style="text-align:right;">$<?= $guest_list->pgla_regular_cover ?></td>
      			</tr>
      			<tr>
      				<td style="font-weight:500; text-align:left;">List Cover</td>
      				<td style="text-align:right;">$<?= $guest_list->pgla_gl_cover ?></td>
      			</tr>
      			<tr>
      				<td style="font-weight:500; text-align:left;">Doors Open</td>
      				<td style="text-align:right;"><?= $time_mod($guest_list->pgla_door_open) ?></td>
      			</tr>
      			<tr>
      				<td style="font-weight:500; text-align:left;">List Closes</td>
      				<td style="text-align:right;"><?= $time_mod($guest_list->pgla_door_close) ?></td>
      			</tr>
      			<?php if($guest_list->pgla_additional_info_1): ?>
      			<tr>
      				<td colspan="2" class="gl_additional_list_info"><?= $guest_list->pgla_additional_info_1 ?></td>
      			</tr>
      			<?php endif; ?>
      			<?php if($guest_list->pgla_additional_info_2): ?>
      			<tr>
      				<td colspan="2" class="gl_additional_list_info"><?= $guest_list->pgla_additional_info_2 ?></td>
      			</tr>
      			<?php endif; ?>
      			<?php if($guest_list->pgla_additional_info_3): ?>
      			<tr>
      				<td colspan="2" class="gl_additional_list_info"><?= $guest_list->pgla_additional_info_3 ?></td>
      			</tr>
      			<?php endif; ?>
      		</tbody>	
      	</table>

      </p>
      
      
      
      
      
      
      
      <p>
      	<h2>Description</h2>
      	<p><?= $guest_list->pgla_description ?></p>
      </p>
      
      
      
    </div>
    



	


<div id="super_status_wrapper" class="guestlist-form-right">
	
	
	<h2><?= $promoter->u_first_name ?>'s List Status</h2>
	
	
	<div id="guestlist_status_wrapper">
		
		
		<?php if($guest_list->status && $guest_list->status->glas_status): ?>
			<p class="status"><?= $guest_list->status->glas_status ?></p>
			<span class="status-update-time">Last Updated: <?= $guest_list->status->glas_human_date ?></span>
		<?php else: ?>
			
			<p class="no-status"><?= $promoter->u_first_name ?> hasn't updated the status of "<?= $guest_list->pgla_name ?>" yet.</p>
			<span>&nbsp;</span>	
			
		<?php endif; ?>
		
		
		<div style="clear:both;"></div>
	</div>	
</div>




<?php

$event_past = false;

if($guest_list->pgla_event == '1' && $time + (60 * 60 * 24) < time()){
	$event_past = true;
}

?>
<?php if(true): ?>
	

		<div style="position:relative; <?= ($event_past) ? 'display:none;' : '' ?> " class="guestlist-form-right">
			
			
			
			
		    <div id="unavailable_overlay">
		    	<p>
		    		
		    		<?= '' //$this->lang->line('p-login_msg2_overlay') ?>
		    		Connect with Facebook to join <?= $promoter->u_first_name ?>'s guest list.<br/><strong>It's that easy!</strong>
		    		<br><br>
		    		<a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a>
		    		
		    	</p>
		    </div>    
		    	
		    	
		    <h2 id="join_here">Join Here</h2>	
		    
			<div id="accordion">
				<h3><a class="no-ajaxy" href="javascript: void(0);">1: Add Your Friends</a></h3>
				<div style="overflow:hidden;">
					
					 <p>Bringing anyone with you? Let <?= $promoter->u_first_name ?> know by adding them here. (optional)</p>
					
					 <p>
					 	<a class="facebook-invite no-ajaxy white" href="javascript: void(0);">
					 		<img src="<?= $central->front_assets ?>images/facebook-icon.png" alt="Facebook Icon"><span>Add your friends to your reservation.</span>
					 	</a>
					 </p>
						 
					<p style="font-weight:600;">Selected Friends: <span id="facebook-gl-friends-count"></span></p>
					<div id="facebook-guest-list-friends">
						<span style="color:#CCC;">No friends added yet.</span>
					</div>
					
					<input class="guestlist-button step_submit" type="button" value="Next">
					
				</div>
				<h3><a class="no-ajaxy" href="javascript: void(0);">2: Add a Message</a></h3>
				<div>
					
					<p>Send <?= $promoter->u_first_name ?> a message with your request. (optional)</p>
					
					<textarea id="guestlist-message" placeholder="Enter your message." style="resize:none;"></textarea>
			        <p class="characters">You have <span class="count">160</span> characters remaining.</p>
					
					<input class="guestlist-button step_submit" type="button" value="Next">
					
					<div style="height:60px;"></div><?php //spacer ?>
					
				</div>
				<h3><a class="no-ajaxy" href="javascript: void(0);">3: Table Reservations</a></h3>
				<div>
					
					
					
					<input type="checkbox" id="guestlist-table-request" name="guestlist-table-request">
					<label for="guestlist-table-request"> Request a Table</label>
					
					
					<div id="price_opt_hide" style="display:none;">
							
							
							
							
						<?php $factor = 0.39; ?>
						<?php $small_factor = 0.20; ?>
						<?php 
							$table_prices = array();
						?>
							
							
							
						
						
		
						<?php foreach($venue_floorplan as $key => $vf): ?>
							
								<?php foreach($vf->items as $item): ?>
								
										<?php if($item->vlfi_item_type == 'table'): ?>
											
											<?php $table_prices[] = $item->{'vlfit_' . rtrim($guest_list->pgla_day, 's') . '_min' } ?>
													
										<?php endif; ?>
										
								<?php endforeach; ?>
								
						<?php endforeach; ?>
											
							
							
						<?php 
							// gather all unique table prices for TODAY	
						//	echo '<div style="display:none">';
						//	var_dump($table_prices);
						//	echo '</div>';	
							$table_prices = array_unique($table_prices);
							array_multisort($table_prices, SORT_ASC);
						?>
						
							
							
							
							
							
						<label for="guest-list-table-price-selection">*Select a Minimum Spend:</label><br>
						<select id="guest-list-table-price-selection" name="guest-list-table-price-selection">
							<?php foreach($table_prices as $val): ?>
								<option value="<?= $val ?>">$<?= $val ?></option>
							<?php endforeach; ?>
						</select>	
							
							
							
						<span class="tables_res_description">These are the tables and minimum spends required to reserve them @ <?= $guest_list->tv_name ?> on <?= ucfirst(rtrim($guest_list->pgla_day, 's')) ?></span>	
		
						
						
						<p style="text-align:center; font-weight:bold;"><?= $guest_list->tv_name ?> Tables and Floorplan</p>
								
								
								
								
								
								
								
								
								
								
								
								
										
						<div id="vl_big" class="vl" style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">
						<?php foreach($venue_floorplan as $key => $vf): ?>
							<div class="vlf" style="width:<?= ceil(800 * $factor) ?>px; height:<?= ceil(600 * $factor) ?>px;">
								
								<div class="vlf_title">Floor <?= $key ?></div>
								
								<div class="vlf_id" style="display:none;"><?= $key ?></div>
								
							
								<?php foreach($vf->items as $item): ?>
									
									<?php
										
										$reserved = '';
																
									?>
									
									<div class="item <?= $item-> vlfi_item_type ?>" style="top:<?= ceil($item->vlfi_pos_y * $factor) ?>px; left:<?= ceil($item->vlfi_pos_x * $factor) ?>px; width:<?= ceil($item->vlfi_width * $factor) ?>px; height:<?= ceil($item->vlfi_height * $factor) ?>px;">
			
										<?php if($item->vlfi_item_type == 'table'): ?>
											
											<span class="title price_<?= $item->{'vlfit_' . rtrim($guest_list->pgla_day, 's') . '_min' } ?>">T</span>						
											<?php $table_prices[] = $item->{'vlfit_' . rtrim($guest_list->pgla_day, 's') . '_min' } ?>
																		
										<?php elseif($item->vlfi_item_type == 'bar'): ?>
											<span class="title">(B)</span>
										<?php elseif($item->vlfi_item_type == 'stage'): ?>
											<span class="title">(S)</span>
										<?php elseif($item->vlfi_item_type == 'dancefloor'): ?>
											<span class="title">(D)</span>
										<?php elseif($item->vlfi_item_type == 'djbooth'): ?>
											<span class="title">(DJ)</span>
										<?php elseif($item->vlfi_item_type == 'bathroom'): ?>
											<span class="title">(Br)</span>
										<?php elseif($item->vlfi_item_type == 'entrance'): ?>
											<span class="title">(E)</span>
										<?php elseif($item->vlfi_item_type == 'stairs'): ?>
											<span class="title">(St)</span>
										<?php endif; ?>
			
										<div class="vlfi_id" style="display:none;"><?= $item->vlfi_id ?></div>
										<div class="vlfi_id_<?= $item->vlfi_id ?>" style="display:none;"><?= $item->vlfi_id ?></div>
										<div class="pos_x" style="display:none;"><?= $item->vlfi_pos_x ?></div>
										<div class="pos_y" style="display:none;"><?= $item->vlfi_pos_y ?></div>
										<div class="width" style="display:none;"><?= $item->vlfi_width ?></div>
										<div class="height" style="display:none;"><?= $item->vlfi_height ?></div>
										<div class="itmCls" style="display:none;"><?= $item->vlfi_item_type ?></div>
									
									</div>
								<?php endforeach; ?>
								<?php unset($table_count); ?>
								
							</div>
						<?php endforeach; ?>
						</div>
						
						
						<table id="vlf_key">
							<tr>
								<td>(Br) - Bathroom</td>
								<td>(DJ) - DJ Booth</td>
							</tr>
							<tr>
								<td>(T) - Table</td>
								<td>(D) - Dance Floor</td>
							</tr>
							<tr>
								<td>(St) - Stairs</td>
								<td>(E) - Entrance</td>
							</tr>
						</table>
						
						
					
						
						<br>
					
						
						
						<p style="color:darkred; text-align:center;">Note: For a table request, you must provide your phone number on step #4 (Next)</p>
						<span style="font-size:12px;">*required</span>
					</div>
		
					<br><br><br>
					<div style="clear:both;"></div>
					
					
					
					
					
					
					
					
					
					<input class="guestlist-button step_submit" type="button" value="Next">
				</div>
				<h3><a style="font-weight:600;" class="no-ajaxy" href="javascript: void(0);"><span>Last Step</span>: <span style="color:#37CE73;">Submit</span></a></h3>
				<div style="padding-left:15px;padding-right:15px;">
					
					
							
						<div class="confirmation">
				          
				          <p style="font-size:12px;">Check here if you would like ClubbingOwl to text you when <?= $promoter->u_first_name ?> accepts or declines your reservation request.</p>
				          <p id="gl_phone_required_msg" style="font-size:12px;text-align:center;color:red;">*Phone number required for table reservation requests</p>
				          <input type="checkbox" id="guestlist-confirmation-text" name="guestlist-confirmation-text">
				          <label for="guestlist-confirmation-text">Confirmation Text & List Updates</label>
				          
				          <div class="confirmation-details">
				            
				            <label for="guestlist-confirmation-phone">Phone Number:</label>
				            <input style="width:140px;" type="text" placeholder="Phone Number" id="guestlist-confirmation-phone" name="guestlist-confirmation-phone">
				            
				            
				            
				            <?php if(false): ?>
				            <br><br><label for="guestlist-confirmation-carrier">Carrier:</label>
				            <?php endif; ?>
				            
				            <select style="display:none;" id="guestlist-confirmation-carrier" name="guestlist-confirmation-carrier">
				              	<option value="invalid">Select...</option>
				              	<option selected="selected" value="0">AT&amp;T</option>
								<option value="1">Verizon</option>
								<option value="2">T-Mobile</option>
								<option value="3">Sprint</option>
				            </select>
				            
				            	            
				            
				          </div>
				        </div>
				        
				        <br>
					
						<input type="checkbox" id="guestlist-share" name="guestlist-share" checked="checked">
			          	<label style="font-size:11px;" for="guestlist-share">Share with my friends on Facebook</label>
			          	
			          	
			          	
			          	
			          	<p id="messages" style=""></p>
			          	
			          	
			          	
			          	
			          	<div style="height:60px;"></div><?php //spacer ?>
			          	
			          	<p style="position:absolute;bottom:10px;right:10px;">
			          		
			          		<div id="dialog-confirm" style="display:none;" title="Cancel Reservation?">
								<p>Are you sure you want to cancel your reservation?</p>
							</div>
			          		
			          		<div style="display:none;">
			          			<a id="back_trigger_link" href="<?= $central->front_link_base . 'promoters/' . $promoter->up_public_identifier . '/guest_lists/' ?>"></a>
			          		</div>
			          		          		
			          		
			          		<table style="float:right;">
			          			<tr>
			          				<td>
			          					<input class="guestlist-button cancel" type="button" id="guestlist-form-cancel" value="Cancel">
			          				</td>
			          				<td>
			          					<input class="guestlist-button submit" type="submit" id="guestlist-form-submit" name="guestlist-form-submit" value="Submit">
			          					<img style="display:none;" id="submit_loading" src="<?= $central->global_assets ?>images/ajax.gif" alt="loading..."/>
			          				</td>
			          			</tr>
			          		</table>
			          					       
			          	</p>
				</div>
			</div>
			
			<div id="accordion_replace_msg" style="display:none;text-align:center;padding:0 15px 20px 15px;">
				
				<br><br>
				
				
				
				<div id="accordion_replace_sub_msg">
					
					<h2 style="text-align:left;">Your reservation request has been successfully submitted</h2>
					<h2 style="text-align:left;">ClubbingOwl will notify you via email/sms when <?= $promoter->u_first_name ?> responds.</h2>
					
					<a class="ajaxify" href="<?= $central->front_link_base ?>profile/">View Your Reservation Status</a>
					
				</div>
				
				
				
				
				<div class="fb-like-box" data-href="http://www.facebook.com/clubbing-owl" data-width="292" data-colorscheme="light" data-show-faces="false" data-stream="false" data-header="false"></div>
				
				<div style="margin-left:auto;margin-right:auto;margin-bottom:20px;"></div>
				
				<div style="clear:both;"></div>
			</div>
		    	
		</div>
		
		
		
		
		
		
		
		
		
		
		<div style="position:relative; text-align:center; <?= (!$event_past) ? 'display:none;' : '' ?>" class="guestlist-form-right">
			
			<h3>Sorry, this event has already occured.</h3>
			
			<h3>Check out <?= $promoter->u_first_name ?>'s other guest lists and events.</h3>
			
		</div>
				
				
		
	<?php endif; ?>
		













  </div>
</section>





<script type="text/javascript">
(function(){
	<?php
		$gl_obj = new stdClass;
		$gl_obj->pgla_id 				= $guest_list->pgla_id;
		$gl_obj->up_id					= $promoter->up_id;
		$gl_obj->promoter_first_name	= $promoter->u_first_name;
	?>
	window.gl_obj = <?= json_encode($gl_obj) ?>;
})();
</script>