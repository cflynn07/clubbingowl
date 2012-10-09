 <?php //Kint::dump($all_guest_lists); ?>
 <section id="guestlist">
  <h1>Guest Lists &amp; Tables</h1>
  <table class="guestlist">
    <tbody>
    	
    	<?php for($i = 0; $i < 7; $i++): ?>
    	<?php $time = strtotime("Today +$i days"); ?>
    		<tr>
            <th><strong><?= strftime('%A', $time) ?></strong><br><?= strftime('%D', $time) ?></th>
            <td>
            	<ul class="tables">
                   
                   
                  	<?php foreach($all_guest_lists as $gl): ?>
                  		<?php if(strtolower($gl->tgla_day) == strtolower(date('l', $time) . 's')): ?>
		                    <li>
		                      
		                      <div class="info">
		                      	<div class="gl_image">
		                      		<img src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $gl->tgla_image . '_t.jpg' ?>" style="width:33px;height:44px;" alt="" />
		                      	</div>
		                      	<div class="gl_text">
		                      		<div class="name"><?= $gl->tgla_name ?></div> 
		                      	</div>
		                      </div>
		                      
		                      <div class="friends">
		                      	<?php if(false): ?>
		                        3 Friends <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar"> <img src="http://placehold.it/20x20" alt="Avatar">
		                      	<?php endif; ?>
		                      </div>
		                      <div class="action">
		                      	<a class="ajaxify_t2" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists/<?= str_replace(' ', '_', $gl->tgla_name) ?>/">Info/Join</a>
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