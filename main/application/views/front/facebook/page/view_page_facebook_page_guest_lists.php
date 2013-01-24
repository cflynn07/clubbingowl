<div id="fan_page_id" style="display:none;"><?=$signed_request_data['page']['id']?></div>
<style type="text/css">
	.unauth_content{
		display:<?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display:<?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>


	<div id="user" class="plugin_user" style="display: block; width:100%;">
		<div style="width:100%; padding-right:20px;" class="center">
			
			<div class="unauth_content" style="display:<?= (($this->session->userdata('vc_user')) ? 'none' : 'block' ) ?>;float:left; max-width:500px;">
				<p style="margin-left:10px;font-size:12px">Log in to <a target="_new" style="margin-left:0px;" href="http://www.clubbingowl.com/">ClubbingOwl</a> with Facebook</p>
			</div>
			
			<ul></ul>
		</div>
	</div>
	<script type="text/javascript">window.module.VCAuth.prototype.update_menu();</script>


	<div id="content" style="border:0" class="content">
	  
	  	<section id="guestlist" style="min-height:0px;">
			<div id="guest_list_content">
			<h1>Guest Lists & Tables</h1>
		  	<div style="border-top:1px dashed #CCC;border-bottom:0" class="guestlist-table">
			    <table>
				      <tbody>
				      	
				      	<?php for($i = 0; $i < 7; $i++): ?>
				    	<?php $time = strtotime("Today +$i days"); ?>
				    	            	
				    	<tr>
				        	<th><strong><?= strftime('%A', $time) ?></strong><br><?= strftime('%D', $time) ?></th>
				       		<td>
				       			
				       			<?php if($i === 0): ?>
					        		<p class="gl_sec_header_info">Tonight</p>
					        	<?php elseif($i === 1): ?>
					        		<p class="gl_sec_header_info">Tomorrow</p>
					        	<?php endif; ?>
				       			
					          <ul class="tables">
					                      	
					          	<?php foreach($guest_lists as $gl): ?>
					          		<?php if(strtolower($gl->tgla_day) == strtolower(date('l', $time) . 's')): ?>
					                    <li>
					
					                      <div class="info">
					                      	<div class="gl_image">
					                      		<img src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $gl->tgla_image . '_t.jpg' ?>" style="width:33px;height:44px;" alt="" />
					                      	</div>
					                      	<div class="gl_text">
					                      		<div class="name"><?= $gl->tgla_name ?></div> 
												<div class="location">
													@ <a href="<?= $central->front_link_base ?>venues/<?= $gl->c_url_identifier ?>/<?= str_replace(' ', '_', $gl->tv_name) ?>/" target="_new"><?= $gl->tv_name ?></a>
												</div>			                      	
											</div>
					                      </div>
					                      
					                      <div class="friends">
					                    <?php if(false): ?>  	
					                        3 Friends <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar">
					                    <?php endif; ?>
					                      </div>
					                      <div class="action">
					                      	
					                      	<span class="tgla_id" style="display:none;"><?= $gl->tgla_id ?></span>
					                      	<span class="tv_id" style="display:none;"><?= $gl->tv_id ?></span>
					                      	<a class="gl_join join_btn" href="#">Info/Join</a>
					                      	
					                      </div>
					                    </li>
					            	<?php endif; ?>
					            <?php endforeach; ?>
					
					      		</ul>
				         	</td>
				         </tr>	
				            		
				        <?php endfor; ?>
				      	
				      </tbody>
			    </table>
		  	</div>
		 </div><!-- guest_list_content -->
		 </section>
	
	
	
		<ul class="venue-list" style="margin-top:20px;">
			
			<?php $count = 0; ?>
			<?php foreach($team_venues as $venue): ?>
			  <li>
				
			  	<img style="max-width:254px; border:1px solid #474D6A;" src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_t.jpg" alt="Venue Banner">			  	
			  	
			  	<br>
					  	
				<a target="_new" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/"><?= $venue->tv_name ?></a>
			    
			    <p class="friends_holder">
			    	
			    	<div class="auth_content tv_friends" id="friends_<?= $venue->tv_id ?>">
			    		<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
			    	</div>
				    
				    <style type="text/css">
				    	div.auth_content.tv_friends img:not(.loading_indicator){
				    		margin-right: 	3px !important;
				    		margin-bottom: 	3px !important;
				    		width: 25px 	!important;
				    		height: 25px 	!important;
				    	}
				    </style>
				    
			    </p>
			    
			  </li>
			  <?php $count++ ?>
			  <?php if($count % 3 == 0): ?>
			  	<div style="clear:both;"></div>
			  <?php endif; ?>
			<?php endforeach; ?>
			
		</ul>
		
		
		
		
		
		
		<hr/>
		
		<h1>Promoters</h1>
		
		
		
		<ul class="venue-list" style="margin-top:20px;">
		
		
		<?php $displayed_promoters = array(); ?>
		
		<?php foreach($team_venues as $venue): ?>
			<?php foreach($venue->venue_promoters as $key => $pro): ?>
				
				<?php 
					
					if(in_array($pro->up_id, $displayed_promoters)){
					
						continue;
					
					}
					
					$displayed_promoters[] = $pro->up_id;
					
				?>
				
			
			<?php
				$pro_gl_link = $central->front_link_base . 'promoters/' . $pro->up_public_identifier . '/guest_lists/';
			?>
			
			<li>
				
				
				
				<table class="pro_overview">
					<tr>
						<td class="overview_pic_td">
							<a class="ajaxify ajaxify_t3" href="<?= $pro_gl_link ?>">
						   		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?= $central->s3_uploaded_images_base_url ?>profile-pics/<?= $pro->up_profile_image ?>_t.jpg" alt="" />
						   	</a>
						</td>
						<td>
							<div class="name_block" style="display:inline-block;clear:right;">
							    <a class="ajaxify ajaxify_t3" href="<?= $pro_gl_link ?>"><?= $pro->u_full_name ?></a>
							    <p class="sub_details"><?= $pro->t_name ?></p>
								
								
								
								<?php if(TRUE): ?>
								<div class="auth_content auth_clear_content" data-up_id="<?= $pro->up_id ?>" class="friends" style="padding:10px 0 0 0; border-top:1px dashed #CCC; border-bottom:1px dashed #CCC;">
									<img class="loading_indicator" style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />									
								</div>
								<?php endif; ?>						
			
							</div>
						</td>
					</tr>
					
				</table>
				
				
				
	
				
				
			
			
			</li>	
			
			
			
			
			
			<?php if(($key + 1) % 2 === 0): ?>
				<div class="data_clear_2" data-clear-2="<?= $key ?>" style="clear:both;"></div>
			<?php endif; ?>
							
			<?php if(($key + 1) % 3 === 0): ?>
				<div class="data_clear_3" data-clear-3="<?= $key ?>" style="clear:both;"></div>
			<?php endif; ?>
			
			
		
			<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
		
		
		
		
		
		
		
		
		
		
		
		<div style="margin-bottom:15px;margin-left:-10px;">
			<div class="fb-like-box" data-href="http://www.facebook.com/clubbing-owl" data-width="292" data-colorscheme="light" data-show-faces="false" data-stream="false" data-header="false"></div> 
		</div>
		
		<div style="padding-top:0px;border-bottom:1px dashed #CCC;padding-bottom:4px;" app_id="<?= $central->facebook_app_id ?>" class="fb-facepile" data-size="large" data-max-rows="1" data-width="1000" data-colorscheme="light"></div>		
		

		


	</div><!--content-->
	
	<?php if(false): ?>
	<a href="<?= $central->front_link_base ?>" target="_new">
		  <img style="margin-top:30px;" class="logo" src="<?= $central->front_assets ?>images/logo_large.png" />
	</a>
	<?php endif; ?>
	
	<div id="guest_list_content_temp_holder" style="display:none;"></div>

</body>
</html>