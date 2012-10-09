<?php if($city): // ----------------- specific city ---------------------- ?>

	<h1><?= $this->lang->line('m-promoters') ?> - <a href="<?= $central->front_link_base ?>promoters/<?= $city->url_identifier ?>/"><?= $city->name . ', ' . $city->state ?></a></h1>
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
		
		<li>
			<a href="<?=$central->front_link_base?>promoters/<?= $pro->c_url_identifier ?>/<?= $pro->up_public_identifier ?>/">
		   		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?=$central->s3_uploaded_images_base_url?>profile-pics/<?=$pro->up_profile_image?>_t.jpg" alt="" />
		   	</a>
		    <div class="name_block" style="display:inline-block;clear:right;">
			    <a href="<?=$central->front_link_base?>promoters/<?= $pro->c_url_identifier ?>/<?= $pro->up_public_identifier ?>/"><?= $pro->u_full_name ?></a>
				
				<?php if(false): ?>   
			    <p>3 friends have been here</p>
				<?php endif; ?>
			
			</div>
			<div class="friends" style="padding-right:10px;">
			</div>
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
	
	
	<?php foreach($all_cities as $vc_city): ?>
		
		<?php if($vc_city->promoters): ?>
			
			<h1>
				<a href="<?= $central->front_link_base ?>promoters/<?= $vc_city->url_identifier ?>/"><?= $vc_city->name . ', ' . $vc_city->state ?></a>
			</h1>
			
			<ul class="venue-list" style="margin-top:20px;">
			
			<?php foreach($vc_city->promoters as $pro): ?>
				
				<li>
					<a href="<?=$central->front_link_base?>promoters/<?= $pro->c_url_identifier ?>/<?= $pro->up_public_identifier ?>/">
			    		<img style="border: 1px solid lightgray; display:inline-block; vertical-align:top;" class="venue-image" src="<?=$central->s3_uploaded_images_base_url?>profile-pics/<?=$pro->up_profile_image?>_t.jpg" alt="" />
				   	</a>
				    <div class="name_block" style="display:inline-block;clear:right;">
					    <a href="<?=$central->front_link_base?>promoters/<?= $pro->c_url_identifier ?>/<?= $pro->up_public_identifier ?>/"><?= $pro->u_full_name ?></a>
						
						<?php if(false): ?>
					    <p>3 friends have been here</p>
						<?php endif; ?>
						
					</div>
					<div class="friends" style="padding-right:10px;">

					</div>
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