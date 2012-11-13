 <section id="guestlist">
  <h1><?= $this->lang->line('p-gl_t') ?></h1>
  <table class="guestlist">
    <tbody>
    	
    	<?php for($i = 0; $i < 7; $i++): ?>
    	<?php $time = strtotime("Today +$i days"); ?>
    	            	
    	<tr>
        <th><strong><?= strftime('%A', $time) ?></strong>
        	<br>
        	<?= strftime('%D', $time) ?>
        </th>
        <td>
        	
        	<?php if($i === 0): ?>
        		<p class="gl_sec_header_info">Tonight</p>
        		<br/><br/>
        	<?php elseif($i === 1): ?>
        		<p class="gl_sec_header_info">Tomorrow</p>
        		<br/><br/>
        	<?php endif; ?>
        	
        	
          <ul class="tables">
                      	
          	<?php foreach($all_guest_lists as $gl): ?>
          		<?php if(strtolower($gl->pgla_day) == strtolower(date('l', $time) . 's')): ?>
                    <li>
                    	
                    	<?php 
                    		$gl_link = $central->front_link_base . 'promoters/' . str_replace(' ', '_', $promoter->up_public_identifier) . '/guest_lists/' . str_replace(' ', '_', $gl->pgla_name) . '/';
                    	?>
                    	
                      <div class="info">
                      	<div class="gl_image">
                      		<img src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $gl->pgla_image . '_t.jpg' ?>" style="width:33px;height:44px;" alt="" />
                      	</div>
                      	<div class="gl_text">
                      		<div class="name"><?= $gl->pgla_name ?></div> 
                       	 	<div class="location">@ <a href="<?= $central->front_link_base ?>venues/<?= $gl->c_url_identifier ?>/<?= str_replace(' ', '_', $gl->tv_name) ?>/"><?= $gl->tv_name ?></a></div>
                      	</div>
                      </div>
                      
                      <div class="friends">
                    <?php if(false): ?>  	
                        3 Friends <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar">
                    <?php endif; ?>
                      </div>
                      <div class="action">
                      	<a class="ajaxify_t2 join_btn" href="<?= $gl_link ?>"><?= $this->lang->line('p-info_join') ?></a>
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
</section>

<?php if(false): ?>
<script type="text/javascript">
jQuery(function(){
	window.vc_page_scripts.promoter_pusher_presence_channels('<?= $promoter->up_users_oauth_uid ?>', '<?= $promoter->team->t_fan_page_id ?>', '<?= $promoter->up_id ?>', '<?= $this->config->item('pusher_api_key') ?>');
})
</script>
<?php endif; ?>