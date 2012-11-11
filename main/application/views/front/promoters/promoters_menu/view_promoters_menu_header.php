<script type="text/javascript">window.vc_promoter_oauth=<?= $promoter->up_users_oauth_uid ?>;</script>
<style type="text/css">
	.unauth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>

<article class="promoter">

  <header>
    <h1><?= $promoter->u_full_name ?></h1>
    <div class="location"><?= $promoter->team->c_name . ', ' . $promoter->team->c_state ?><br/><p style="font-size:14px; margin:0; float:right;"><?= $promoter->team->t_name ?></p></div>
  </header>

  <div class="left">
    <div class="avatar"><img style="border: 1px solid lightgray;" src="<?= $promoter->profile_image_complete_url ?>" alt="<?= $promoter->u_full_name ?>'s Picture"></div>
    
    <div style="border-top:1px dashed #CCC;border-bottom:1px dashed #CCC;margin-top:15px;" id="add_as_friend">
    	<table style="width:100%;">
    		<tr>
    			<td><img src="<?= $central->front_assets ?>images/facebook-icon.png" /></td>
    			<td style="text-align:center;"><p><a href="javascript:void(0);">Add <?= $promoter->u_full_name ?> as a Friend.</a></p></td>
    		</tr>
    	</table>
    </div>
    
	    <div class="rating" data-stars="1">
	      <div class="stars">
	        <div class="stars-on" style="width:90%"></div>
	        <div class="stars-off" style="width:100%;"></div>
	      </div>
	    </div>
	    
	    <a href="#">What's This?</a>
	    <p><?= lang_key($this->lang->line('p-ranking_msg'), array('promoter_full_name' => $promoter->u_full_name)) ?></p>
	  
	  	<div class="unauth_content">
	  		<a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a>
	  	</div>
	  
	  	<div class="auth_content" id="pro_user_reviews">
	  		
	  		<?php for($i=0; $i < 1; $i++): ?>
	  		<table>
	  			<tr>
	  				<td class="user_pic" rowspan="2">
	  					<img src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash4/369458_504405294_1222184447_q.jpg" />
	  				</td>
	  				<td class="user_name">
	  					<a href="#">Casey Flynn</a>
	  					<div>
	  						<input name="star" type="radio" class="star" disabled="disabled"/>
							<input name="star" type="radio" class="star" disabled="disabled"/>
							<input name="star" type="radio" class="star" disabled="disabled" checked="checked"/>
							<input name="star" type="radio" class="star" disabled="disabled"/>
							<input name="star" type="radio" class="star" disabled="disabled"/>
	  					</div>
	  				</td>
	  			</tr>
	  			<tr>
	  				<td class="user_comment">This guy is great, totally helped me out.</td>
	  			</tr>
	  		</table>
	  		<?php endfor; ?>
	  		
	  	</div>
  
  	<?= '<script type="text/javascript">window.u_up_pop=' . ((isset($u_up_pop)) ? json_encode($u_up_pop) : 'false') . ';</script>' ?>
  	
  	
  </div>

  <div class="right non_fb_plugin">
    
    <div class="tab-container">
    	      
      <div class="tab-content">