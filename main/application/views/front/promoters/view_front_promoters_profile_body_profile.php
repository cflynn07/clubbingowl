<script type="text/javascript">window.ptype_promoter_city='promoter';</script>
<style type="text/css">
	.unauth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>

<div class="tab-content">
	
    
    <section id="profile">
    	
      <h1><?= $this->lang->line('p-profile') ?></h1>
      
      <h2><?= $this->lang->line('p-venues') ?></h2>
      
      <ul class="venues">
      	<?php foreach($promoter->promoter_team_venues as $key => $ptv): ?>
			<li>
				
				<a href="<?= $central->front_link_base ?>venues/<?= $promoter->team->c_url_identifier ?>/<?= str_replace(' ', '_', $ptv->tv_name) ?>/">
					<?php if($ptv->tv_image): ?>
				  	<img class="logo" src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $ptv->tv_image ?>_t.jpg" alt="<?= $this->lang->line('p-venue_banner') ?>">
				  	<?php else: ?>
				  	<img class="logo" src="http://placehold.it/286x86?text=Coming+Soon" alt="<?= $this->lang->line('p-venue_banner') ?>">
				  	<?php endif; ?>
			  	</a>
			  	
              <?php if(false): ?>
              <img class="logo" src="http://placehold.it/106x42" alt="">
              <?php endif; ?>
              
              <div class="name">
              	<a href="<?= $central->front_link_base ?>venues/<?= $promoter->team->c_url_identifier ?>/<?= str_replace(' ', '_', $ptv->tv_name) ?>/"><?= $ptv->tv_name ?></a></div>
                <p class="auth_content tv_friends" id="user_friends_<?= $ptv->tv_id ?>">
              	  <img style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
           	    </p>
            </li>
		<?php endforeach; ?>
      </ul>
     
      <h2><?= $this->lang->line('p-biography') ?></h2>
      <div class="bio">
        <p><?= $promoter->up_biography ?></p>
      </div>
      <h2><?= $this->lang->line('p-friend_activity') ?></h2>
      <ul class="updates">
      	
      	<div style="width:100%;text-align:center;">
      		
      		<div class="unauth_content">
      			<p><?= lang_key($this->lang->line('p-login_msg1'), array('promoter_first_name' => $promoter->u_first_name)) ?></p>
      			<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
      		</div>
      		
      		<div id="promoter_user_news_feed" class="auth_content">
      			<img style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
      		</div>

      	</div>

      	
      </ul>
      
    </section>
  
</div>