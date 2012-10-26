<style type="text/css">
	.unauth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>

<div id="venues_overview">

<?php if($city): // ----------------- specific city ---------------------- ?>

	<h1><?= $this->lang->line('m-venues') ?> - <a href="<?= $central->front_link_base ?>venues/<?= $city->url_identifier ?>/"><?= $city->name . ', ' . $city->state ?></a></h1>
	<hr style="height: 0; border-top: 1px dashed #CCC;">
	
	
	<?php if(false): ?>
	<img src="http://placehold.it/1000x365" alt="Landing Page Placeholder">
	<?php endif; ?>

	<?php if(!$venues): ?>
		
		<p>
			<?= lang_key($this->lang->line('v-no_venues_city'), array('location' => $city->name)) ?>	
		</p>
		
	<?php else: ?>
	
	<div id="graphs" style="max-width:100%;height:auto;"></div>
	
	<ul class="venue-list" style="margin-top:20px;">
		
		<?php foreach($venues as $venue): ?>
		  <li>
			
			<a class="ajaxify_t3" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/">

			<?php if($venue->tv_image): ?>
		  	<img src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_t.jpg" alt="Venue Banner">
		  	<?php else: ?>
		  	<img src="http://placehold.it/286x86?text=Coming+Soon" alt="Venue Banner">
		  	<?php endif; ?>
		  	</a><br/>
				  	
			<a class="ajaxify_t3" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/"><?= $venue->tv_name ?></a>
		    
		    <p class="friends_holder">
		    	
		    	<div class="auth_content tv_friends" id="friends_<?= $venue->tv_id ?>">
		    		<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		    	</div>
			    
		    </p>
		    
		  </li>
		<?php endforeach; ?>
		
	</ul>
	
	<?php endif; ?>

<?php else: //--------------------- all cities ---------------------- ?>

	<h1><?= $this->lang->line('m-venues') ?></h1>
	<hr style="height: 0; border-top: 1px dashed #CCC;">
	
	<?php if(false): ?>
	<img src="http://placehold.it/1000x365" alt="Landing Page Placeholder">
	<?php endif; ?>

	<?php foreach($all_cities as $vc_city): ?>
		
		<?php if($vc_city->venues): ?>
			
			<h1>
				<a href="<?= $central->front_link_base ?>venues/<?= $vc_city->url_identifier ?>/guest_lists/"><?= $vc_city->name . ', ' . $vc_city->state ?></a>
			</h1>
			
			<ul class="venue-list" style="margin-top:20px;">
			
			<?php foreach($vc_city->venues as $venue): ?>
				
				 <li>
				 	
				 	<a class="ajaxify_t3" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/">

				 	<?php if($venue->tv_image): ?>
				  	<img src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_t.jpg" alt="Venue Banner">
				  	<?php else: ?>
				  	<img src="http://placehold.it/286x86?text=Coming+Soon" alt="Venue Banner">
				  	<?php endif; ?>
				  	</a><br/>
				  	
				    <a class="ajaxify_t3" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/"><?= $venue->tv_name ?></a>
				    
				   	<p class="friends_holder">
				   		
				   		<div class="auth_content tv_friends" id="friends_<?= $venue->tv_id ?>">
		    				<img class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
				   		</div>			   	 
				    	
				    </p>
				    
				 </li>
				
			<?php endforeach; ?>
			
			</ul>
			
		<?php endif; ?>
		
	<?php endforeach; ?>	
	
	
	<hr style="height: 0px; border-top: 1px dashed #CCC;">
	<h1><?= $this->lang->line('v-coming_soon') ?></h1>
	<ul class="venue-list" style="">
	<?php foreach($all_cities as $vc_city): ?>
	
		<?php if(!$vc_city->venues): ?>
	
		<li><?= $vc_city->name . ', ' . $vc_city->state ?></li>
	
		<?php endif; ?>
	
	<?php endforeach; ?>
	</ul>
		
<?php endif; //-------------------------------------------- ?>

</div>