<style type="text/css">
	.unauth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>


<?php if($city): // ----------------- specific city ---------------------- ?>
	<script type="text/javascript">window.ptype_promoter_city='city';</script>
	<h1><?= $this->lang->line('m-promoters') ?> - <a href="<?= $central->front_link_base ?>promoters/cities/<?= $city->url_identifier ?>/"><?= $city->name . ', ' . $city->state ?></a></h1>
	<hr style="height: 0; border-top: 1px dashed #CCC;">
	
	


	<?php if(false): ?>
	<div style="width:1000px;height:365px;background:#CCC;">
		<h2 >My Friends' Top Promoters in <?= $city->name ?></h2>
		<div style=""></div>
		<div></div>
	</div>
	<?php endif; ?>
	
	<?php if(!$promoters): ?>
		
		<p>Sorry! No Promoters in <?= $city->name ?> yet!</p>
		
	<?php else: ?>
	
	<ul class="venue-list" style="margin-top:20px;">
		<?php foreach($promoters as $pro): ?>
		
		<?php
			$pro_gl_link = $central->front_link_base . 'promoters/' . $pro->up_public_identifier . '/guest_lists/';
		?>
		
		<li>
			
			
			
			
			
			
			<table class="pro_overview">
				<tr>
					<td class="overview_pic_td">
						<a class="ajaxify_t3" href="<?= $pro_gl_link ?>">
					   		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?= $central->s3_uploaded_images_base_url ?>profile-pics/<?= $pro->up_profile_image ?>_t.jpg" alt="" />
					   	</a>
					</td>
					<td>
						<div class="name_block" style="display:inline-block;clear:right;">
						    <a class="ajaxify_t3" href="<?= $pro_gl_link ?>"><?= $pro->u_full_name ?></a>
						    <p class="sub_details"><?= $pro->t_name ?></p>
							
							<div class="auth_content auth_clear_content" data-up_id="<?= $pro->up_id ?>" class="friends" style="padding:10px 0 0 0; border-top:1px dashed #CCC; border-bottom:1px dashed #CCC;">
								<img class="loading_indicator" style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
								
								<?php if(false): ?>
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<img class="friend_img" src="http://placehold.it/20" />
								<?php endif; ?>
								
							</div>
														
		
						</div>
					</td>
				</tr>
				<tr>
					<td style="padding-right:10px;">
						<p style="margin:0; float:right;">Promotes @</p>
					</td>
					<td>
						
						<?php foreach($pro->venues as $venue): ?>
							
														
							<?php if($venue->c_id != $city->id) 
								continue; 
								
								$tv_url = $central->front_link_base . 'venues/' . $venue->c_url_identifier . '/' . str_replace(' ', '_', $venue->tv_name) . '/';
							?>
																
							<div class="promoter_venue_box">
								<a href="<?= $tv_url ?>">
									<img src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_t.jpg" />
								</a>
								<a class="promoter_venue_list_a" href="<?= $tv_url ?>"><?= $venue->tv_name ?></a>
							</div>
						<?php endforeach; ?>
						
											
					</td>
				</tr>
			</table>
			
			
			

			
			<?php if(false): ?>
			<a class="ajaxify_t3" href="<?= $pro_gl_link ?>">
		   		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?= $central->s3_uploaded_images_base_url ?>profile-pics/<?= $pro->up_profile_image ?>_t.jpg" alt="" />
		   	</a>
		    <div class="name_block" style="display:inline-block;clear:right;">
			    <a class="ajaxify_t3" href="<?= $pro_gl_link ?>"><?= $pro->u_full_name ?></a>
			    <p class="sub_details"><?= $pro->t_name ?></p>
				
				
				<?php if(false): ?>   
			    <p>3 friends have been here</p>
				<?php endif; ?>
			
			</div>
			<?php endif; ?>
		
		
		
		
		
		
		</li>	
			
		<?php endforeach; ?>
	</ul>
	
	<?php endif; ?>
		


<?php else: //--------------------- all cities ---------------------- ?>

	<h1><?= $this->lang->line('m-promoters') ?></h1>
	<hr style="height: 0; border-top: 1px dashed #CCC;">
	
	
	
	<?php if(false): ?>
	<div style="width:1000px;height:365px;">
		<h3>My Friends' Top Promoters</h3>
	</div>
	<?php endif; ?>
	
	<?php Kint::dump($all_cities); ?>
	<?php foreach($all_cities as $vc_city): ?>
		
		<?php if($vc_city->promoters): ?>
			
			<h1>
				<a href="<?= $central->front_link_base ?>promoters/cities/<?= $vc_city->url_identifier ?>/"><?= $vc_city->name . ', ' . $vc_city->state ?></a>
			</h1>
			
			<ul class="venue-list" style="margin-top:20px;">
			
			<?php foreach($vc_city->promoters as $pro): ?>
				
				<?php
					$pro_gl_link = $central->front_link_base . 'promoters/' . $pro->up_public_identifier . '/guest_lists/';
				?>
				
				<li>
					
					
					
					
					<table class="pro_overview">
						<tr>
							<td class="overview_pic_td">
								<a class="ajaxify_t3" href="<?= $pro_gl_link ?>">
							   		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?= $central->s3_uploaded_images_base_url ?>profile-pics/<?= $pro->up_profile_image ?>_t.jpg" alt="" />
							   	</a>
							</td>
							<td>
								<div class="name_block" style="display:inline-block;clear:right;">
								    <a class="ajaxify_t3" href="<?= $pro_gl_link ?>"><?= $pro->u_full_name ?></a>
								    <p class="sub_details"><?= $pro->t_name ?></p>
									
									<div class="auth_clear_content auth_content" data-up_id="<?= $pro->up_id ?>" class="friends" style="padding:10px 0 0 0; border-top:1px dashed #CCC; border-bottom:1px dashed #CCC;">
										<img class="loading_indicator" style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
										
										
										<?php if(false): ?>
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<img class="friend_img" src="http://placehold.it/20" />
										<?php endif; ?>									
										
									</div>
																
				
								</div>
							</td>
						</tr>
						<tr>
							<td style="padding-right:10px;">
								<p style="margin:0; float:right;">Promotes @</p>
							</td>
							<td>
								
								<?php foreach($pro->venues as $venue): ?>
									
																
									<?php if($venue->c_id != $vc_city->id) 
										continue; 
										
										$tv_url = $central->front_link_base . 'venues/' . $venue->c_url_identifier . '/' . str_replace(' ', '_', $venue->tv_name) . '/';
									?>
																		
									<div class="promoter_venue_box">
										<a href="<?= $tv_url ?>">
											<img src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_t.jpg" />
										</a>
										<a class="promoter_venue_list_a" href="<?= $tv_url ?>"><?= $venue->tv_name ?></a>
									</div>
								<?php endforeach; ?>
								
													
							</td>
						</tr>
					</table>
					
					
					
					
					
					
					
					
					<?php if(false): ?>
					<a class="ajaxify_t3" href="<?= $pro_gl_link ?>">
			    		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?=$central->s3_uploaded_images_base_url?>profile-pics/<?=$pro->up_profile_image?>_t.jpg" alt="" />
				   	</a>
				    <div class="name_block" style="display:inline-block;clear:right;">
					    <a class="ajaxify_t3" href="<?= $pro_gl_link ?>"><?= $pro->u_full_name ?></a>
						<p class="sub_details"><?= $pro->t_name ?></p>
						
						<?php if(false): ?>
					    <p>3 friends have been here</p>
						<?php endif; ?>
						
					</div>
					<div class="friends" style="padding-right:10px;">

					</div>
					
					<?php endif; ?>
					
					
					
				</li>
				
			<?php endforeach; ?>
			
			</ul>
			
		<?php endif; ?>
		
	<?php endforeach; ?>	
	
	
	<hr style="height: 0px; border-top: 1px dashed #CCC;">
	<h1>Coming Soon!</h1>
	<ul class="venue-list" style="">
	<?php foreach($all_cities as $vc_city): ?>
	
		<?php if(!$vc_city->promoters): ?>
	
		<li><?= $vc_city->name . ', ' . $vc_city->state ?></li>
	
		<?php endif; ?>
	
	<?php endforeach; ?>
	</ul>

<?php endif; //-------------------------------------------- ?>