<script type="text/javascript">
	window.page_obj= {}; window.page_obj.four_oh_four = true;
</script>



<section id="guestlist">
  
  <h1>Guest Lists &amp; Tables</h1>

  <div class="guestlist-form" style="display: block; opacity: 1; ">

    <p id="gl_title_header" style="font-size:18px; text-align:center;">Guest List Not Found</p>
	
	
		<div id="error_wrapper">
			<div style="width:100%;" id="message">
				<p>Sorry! Toro says,<br>The guest list you're looking for could not be found.<br/>It may have been deleted.</p>
				
				<a style="text-decoration:none;" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists/">
					<input  onclick="javascript:void(0);" class="back" type="button" value="Other Guest Lists at <?= $venue->tv_name ?>">
				</a>
				<br/>
				<br/>
				
				<input onclick="javascripot:window.location='/';" class="forward" type="button" value="Return to Home">
			</div>
			
			<img style="width:100%; margin-top:20px;" id="toro" src="<?= $central->front_assets ?>/images/ClubbingOwlBackgroundWeb.png">
			<div style="clear:both"></div>
		</div>

  </div>
  
</section>















<?php if(false): ?>

    <h2>List Desctiption</h2>
    <p><?= $guest_list->tgla_description ?></p>

    <div class="guestlist-form-left">
      	
      <div class="guestlist-form-image">
        <img id="gl_image" src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $guest_list->tgla_image . '_p.jpg' ?>" style="width:188px; height:266px;" alt="Event Image">
      </div>
      
      <p>
      	<h2>Info</h2>
      	
      	<table id="list_info">
      		<tbody>
      			<tr>
      				<td>Minimum Age</td>
      				<td><?= $guest_list->tgla_min_age ?></td>
      			</tr>
      			<tr>
      				<td>Regular Cover:</td>
      				<td>$<?= $guest_list->tgla_regular_cover ?></td>
      			</tr>
      			<tr>
      				<td>List Cover:</td>
      				<td>$<?= $guest_list->tgla_gl_cover ?></td>
      			</tr>
      			<tr>
      				<td>Doors Open</td>
      				<td><?= $guest_list->tgla_door_open ?>:00</td>
      			</tr>
      			<tr>
      				<td>List Closes</td>
      				<td><?= $guest_list->tgla_door_close ?>:00</td>
      			</tr>
      			<?php if($guest_list->tgla_additional_info_1): ?>
      			<tr>
      				<td><?= $guest_list->tgla_additional_info_1 ?></td>
      			</tr>
      			<?php endif; ?>
      			<?php if($guest_list->tgla_additional_info_2): ?>
      			<tr>
      				<td><?= $guest_list->tgla_additional_info_2 ?></td>
      			</tr>
      			<?php endif; ?>
      			<?php if($guest_list->tgla_additional_info_3): ?>
      			<tr>
      				<td><?= $guest_list->tgla_additional_info_3 ?></td>
      			</tr>
      			<?php endif; ?>
      		</tbody>	
      	</table>

      </p>
      
      
      <p>
        <h2>Venue</h2>
      	<img style="max-width:188px;" src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $guest_list->tv_image ?>_t.jpg" alt="<?= $guest_list->tv_name ?>" /><br>
        <strong><a href="<?= $central->front_link_base . 'venues/' . $guest_list->c_url_identifier . '/' . str_replace(' ', '_', $guest_list->tv_name) . '/'?>"><?= $guest_list->tv_name ?></a></strong><br>
        <?= $guest_list->tv_street_address ?><br>
        <?= $guest_list->tv_city?>, <?= $guest_list->tv_state ?> <?= $guest_list->tv_zip ?>        
      </p>
      
    </div>
    

<div style="position:relative;" class="guestlist-form-right">
	
    
    <div id="unavailable_overlay" style="position:absolute; top:0; left:0; width:100%; height:101%; background:rgba(0,0,0,.85); z-index:999999; vertical-align:middle; text-align:center; color:#FFF;">
    	<p style="position:relative; top:30%; width:85%; font-size:14px; text-align:center; margin-left:auto; margin-right:auto;">
    		<?= $this->lang->line('p-login_msg2_overlay') ?>
    		<br><br>
    		<a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a>
    	</p>
    </div>    
    
    	
	<div id="accordion">
		<h3><a class="no-ajaxy" href="javascript: void(0);">1: Add Your Friends</a></h3>
		<div style="overflow:hidden;">
			
			 <p>
			 	<a class="facebook-invite no-ajaxy white" href="javascript: void(0);">
			 		<img src="<?= $central->front_assets ?>images/facebook-icon.png" alt="Facebook Icon"><span>Add your friends to your reservation.</span>
			 	</a>
			 </p>
				 
			<p>Friends: <span id="facebook-gl-friends-count"></span></p>
			<div id="facebook-guest-list-friends">No friends added yet.</div>
			
			<input class="guestlist-button step_submit" type="button" value="Next">
			
		</div>
		<h3><a class="no-ajaxy" href="javascript: void(0);">2: Add a Message</a></h3>
		<div>
			
			<p>Send a message with your request. (optional)</p>
			
			<textarea id="guestlist-message" placeholder="Enter your message." style="resize:none;"></textarea>
	        <p class="characters">You have <span class="count">160</span> characters remaining.</p>
			
			<input class="guestlist-button step_submit" type="button" value="Next">
			
			<div style="height:60px;"></div><?php //spacer ?>
			
		</div>
		<h3><a class="no-ajaxy" href="javascript: void(0);">3: Table Reservations</a></h3>
		<div>
			
			
			
			<input type="checkbox" id="guestlist-table-request" name="guestlist-table-request">
			<label for="guestlist-table-request">Request a Table</label>
			
			
			<div id="price_opt_hide" style="display:none;">
					
				<?php $factor = 0.39; ?>
				<?php $small_factor = 0.20; ?>
				<?php 
					$table_prices = array();
				?>
				
				<p style="text-align:center; font-weight:bold;"><?= $guest_list->tv_name ?> Tables and Floorplan</p>
						
						
						
						
						
						
						
						
						
						
						
						
								
				<div id="vl_big" class="vl" style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">
				<?php foreach($venue_floorplan as $key => $vf): ?>
					<div class="vlf" style="width:<?= ceil(800 * $factor) ?>px; height:<?= ceil(600 * $factor) ?>px;">
						
						<div class="vlf_title">Floor <?= $key ?></div>
						
						<div class="vlf_id" style="display:none;"><?= $key ?></div>
						
						
						<?php foreach($vf->items as $item): ?>
						
							<div class="item <?= $item-> vlfi_item_type ?>" style="top:<?= ceil($item->vlfi_pos_y * $factor) ?>px; left:<?= ceil($item->vlfi_pos_x * $factor) ?>px; width:<?= ceil($item->vlfi_width * $factor) ?>px; height:<?= ceil($item->vlfi_height * $factor) ?>px;">
	
								<?php if($item->vlfi_item_type == 'table'): ?>
									
									<span class="title price_<?= $item->{'vlfit_' . rtrim($guest_list->tgla_day, 's') . '_min' } ?>">T</span>						
									<?php $table_prices[] = $item->{'vlfit_' . rtrim($guest_list->tgla_day, 's') . '_min' } ?>
								
																		
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
				
				
				
				
				
				
				
				
				
				
				
				
				
				

				<div id="vl_small" class="vl" style="margin-left:auto; margin-right:auto; width:100%; text-align:center;">
				<?php foreach($venue_floorplan as $key => $vf): ?>
					<div class="vlf" style="width:<?= ceil(800 * $small_factor) ?>px; height:<?= ceil(600 * $small_factor) ?>px;">
						
						<div class="vlf_title">Floor <?= $key ?></div>
						
						<div class="vlf_id" style="display:none;"><?= $key ?></div>
						
	
						<?php foreach($vf->items as $item): ?>
							
						
							<div class="item <?= $item-> vlfi_item_type ?>" style="top:<?= ceil($item->vlfi_pos_y * $small_factor) ?>px; left:<?= ceil($item->vlfi_pos_x * $small_factor) ?>px; width:<?= ceil($item->vlfi_width * $small_factor) ?>px; height:<?= ceil($item->vlfi_height * $small_factor) ?>px;">
	
								<?php if($item->vlfi_item_type == 'table'): ?>
									
									<span class="title price_<?= $item->{'vlfit_' . rtrim($guest_list->tgla_day, 's') . '_min' } ?>">T</span>						
									<?php $table_prices[] = $item->{'vlfit_' . rtrim($guest_list->tgla_day, 's') . '_min' } ?>
																		
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
				
				
				<?php 
					// gather all unique table prices for TODAY	
					echo '<div style="display:none">';
					var_dump($table_prices);
					echo '</div>';	
					$table_prices = array_unique($table_prices);
					array_multisort($table_prices, SORT_ASC);
				?>
				
				
				<br>
			
				<label for="guest-list-table-price-selection">*Select a Minimum Spend:</label><br>
				<select id="guest-list-table-price-selection" name="guest-list-table-price-selection">
					<?php foreach($table_prices as $val): ?>
						<option value="<?= $val ?>">$<?= $val ?></option>
					<?php endforeach; ?>
				</select>
				
				<p style="color:red; text-align:center;">Note: For a table request, you must provide your phone number on step #4 (Next)</p>
				<span style="font-size:12px;">*required</span>
			</div>

			<br><br><br>
			<div style="clear:both;"></div>
			
			
			
			
			
			
			
			
			
			<input class="guestlist-button step_submit" type="button" value="Next">
		</div>
		<h3><a class="no-ajaxy" href="javascript: void(0);">4: Options & Submit</a></h3>
		<div>
			
			
					
				<div class="confirmation">
		          
		          <p style="font-size:12px;">Check here if you would like ClubbingOwl to text you when <?= $guest_list->tv_name ?> accepts or declines your reservation request.</p>
		          <p style="font-size:12px;text-align:center;color:red;">*Phone number required for table reservation requests</p>
		          <input type="checkbox" id="guestlist-confirmation-text" name="guestlist-confirmation-text">
		          <label for="guestlist-confirmation-text">Confirmation Text</label>
		          
		          <div class="confirmation-details">
		            
		            <input style="width:140px;" type="text" placeholder="Phone Number" id="guestlist-confirmation-phone" name="guestlist-confirmation-phone">
		            
		            <br><br><label for="guestlist-confirmation-carrier">Carrier:</label>
		            <select id="guestlist-confirmation-carrier" name="guestlist-confirmation-carrier">
		              	<option value="invalid">Select...</option>
		              	<option value="0">AT&amp;T</option>
						<option value="1">Verizon</option>
						<option value="2">T-Mobile</option>
						<option value="3">Sprint</option>
		            </select>
		          </div>
		        </div>
		        
		        <br>
			
				<input type="checkbox" id="guestlist-share" name="guestlist-share" checked="checked">
	          	<label for="guestlist-share">Share on Facebook</label>
	          	
	          	
	          	
	          	
	          	<p id="messages" style="color:red;background:#000;"></p>
	          	
	          	
	          	
	          	
	          	<div style="height:60px;"></div><?php //spacer ?>
	          	
	          	<p style="position:absolute;bottom:10px;right:10px;">
	          		
	          		<div id="dialog-confirm" style="display:none;" title="Cancel Reservation?">
						<p>Are you sure you want to cancel your reservation?</p>
					</div>
	          		
	          		<div style="display:none;">
	          			<a id="back_trigger_link" href="<?= $central->front_link_base . 'venues/' . $guest_list->c_url_identifier . '/' . str_replace(' ', '_', $guest_list->tv_name) . '/guest_lists/' ?>"></a>
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
	
	<div id="accordion_replace_msg" style="display:none;text-align:center;padding-bottom:20px;">
		
		<br><br><br>
		
		<h2>Your reservation request has been successfully submitted, ClubbingOwl will notify you via email/sms when <?= $guest_list->tv_name ?> responds.</h2>
		<div class="fb-like-box" data-href="http://www.facebook.com/clubbing-owl" data-width="292" data-colorscheme="light" data-show-faces="false" data-stream="false" data-header="false"></div>
		
		<div style="margin-left:auto;margin-right:auto;margin-bottom:20px;"></div>
		
		<div style="clear:both;"></div>
	</div>
    	
</div>

















  </div>
</section>



<?php endif; ?>