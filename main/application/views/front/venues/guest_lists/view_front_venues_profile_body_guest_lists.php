<?php 
	$events = array();

	foreach($all_guest_lists as $gl){
		if($gl->tgla_event == '1'){
			
			$time = strtotime($gl->tgla_event_date);
			
			if($time + (60 * 60 * 24) < time())
				continue;
			
			$events[] = $gl;
		}
	}
?>




 <section id="guestlist">
 	
 	
 	
 	
 	
 	
  <?php if($events): ?>
	 	
	 <h2 style="border-bottom:1px dashed #CCC;"><?= $venue->tv_name . '\'s Special Events' //$this->lang->line('p-gl_t') ?></h2>
	 
	 <?php foreach($events as $ev): ?>
	 	
	 	<?php 
	 	
    		$gl_link = $central->front_link_base . 'venues/' . $venue->c_url_identifier . '/' . str_replace(' ', '_', $venue->tv_name) . '/guest_lists/' . str_replace(' ', '_', $ev->tgla_name) . '/';
    	?>
	 	
	  <div class="tables">
	  	<table class="event_table">
	  		<tr>
	  			<td class="event_image" rowspan="2">
	  				<img src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $ev->tgla_image . '_t.jpg' ?>" />
	  			</td>
	  			<td class="event_text">
	  				<p class="event_title"><?= $ev->tgla_name ?></p>
	  				<p class="event_date"><?= date('D F j, Y', strtotime($ev->tgla_event_date)) ?></p>
	  			</td>
	  		</tr>
	  		<tr>
	  			<td>
	  				<div class="event_join action">
				  		<a class="ajaxify_t2 join_btn" href="<?= $gl_link ?>">Info/Join</a>
				  	</div>
	  			</td>
	  		</tr>
	  	</table>
	  </div>
	 	
	 <?php endforeach; ?>
	  
	  
  <?php endif; ?>

 	
 	
 	
 	
 	
 	
 	
  <h2><?= $venue->tv_name ?>'s Weekly Guest Lists</h2>
  <table class="guestlist">
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
                                     
                  	<?php foreach($all_guest_lists as $gl): ?>
                  		
                  		<?php if($gl->tgla_event == '1') continue; ?>
                  		
                  		<?php if(strtolower($gl->tgla_day) == strtolower(date('l', $time) . 's')): ?>
		                    <li>
		                      
		                      <div class="info">
		                      	<div class="gl_image">
		                      		<img src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $gl->tgla_image . '_t.jpg' ?>" style="width:33px; height:44px; border:1px solid #CCC;" alt="" />
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
		                      	<a class="ajaxify_t2 join_btn" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists/<?= str_replace(' ', '_', $gl->tgla_name) ?>/">Info/Join</a>
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