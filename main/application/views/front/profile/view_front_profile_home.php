<article class="history">

  <header>
    <h1><?= $user->users_full_name ?></h1>
  </header>
  
  <h2>Your Reservations</h2>
  
  <div class="history-table">
    <table id="history_table">

      <thead style="text-align:center !important;">
      	<tr>
	        <th>Event Date</th>
	        <th>Promoter</th>
	        <th>Guest List Name</th>
	        <th>Request Message</th>
	        <th>Response Message</th>
	        <th>Approval Status</th>
      	</tr>
      </thead>

      <tbody style="text-align:left;">
      	<?php foreach($reservation_requests as $res): ?>
      		
      		<?php if(isset($res->pgl_date)): ?>
		    
		      	 <tr>
		          <td><?= strftime('%x', strtotime($res->pgl_date)) ?></td>
		          <td><a href="<?= $central->front_link_base ?>promoters/<?= $res->up_public_identifier ?>/"><?= $res->u_full_name ?></a></td>
		          <td><a href="<?= $central->front_link_base ?>promoters/<?= $res->up_public_identifier ?>/guest_lists/<?= str_replace(' ', '_', $res->pgla_name) ?>/"><?= $res->pgla_name ?></a></td>
		          <td class="request_msg">"<?= $res->pglr_request_msg ?>"</td>
		          <td class="response_msg">"<?= $res->pglr_response_msg ?>"</td>
		          
		          
		          <?php if($res->pglr_approved === '1'): ?>
		          	<td style="color:green;">Approved</td>
		          <?php elseif($res->pglr_approved === '-1'): ?>
		          	<td style="color:red;">Declined</td>
		          <?php elseif($res->pglr_approved === '0'): ?>
		          	<td>Requested</td>
		          <?php endif; ?>
		          
		    	 </tr>
      		
      		<?php else: ?>
      
      			<tr>
		          <td><?= strftime('%x', strtotime($res->tgl_date)) ?></td>
		          <td> - </td>
		          <td><a href="<?= $central->front_link_base ?>venues/<?= $res->c_url_identifier ?>/<?= str_replace(' ', '_', $res->tv_name) ?>/<?= str_replace(' ', '_', $res->tgla_name) ?>/"><?= $res->tgla_name ?></a></td>
		          <td class="request_msg">"<?= $res->tglr_request_msg ?>"</td>
		          <td class="response_msg">"<?= $res->tglr_response_msg ?>"</td>
 				  
 				  <?php if($res->tglr_approved === '1'): ?>
		          	<td style="color:green;">Approved</td>
		          <?php elseif($res->tglr_approved === '-1'): ?>
		          	<td style="color:red;">Declined</td>
		          <?php elseif($res->tglr_approved === '0'): ?>
		          	<td>Requested</td>
		          <?php endif; ?>
		          
		        </tr>
      			
      		<?php endif; ?>
      
      	<?php endforeach; ?>
      </tbody>

    </table>
  </div>

  <section style="width:50%;" id="promoters" class="column">
    
    <h1>Your promoters</h1>

    <ul class="people">
    	<?php foreach($favorite_promoters as $fav_pro): ?>
    		
    		 <li>
		        <img style="margin-bottom: 8px; border:1px solid #CCC;" src="<?= $central->s3_uploaded_images_base_url ?>profile-pics/<?= $fav_pro->up_profile_image ?>_t.jpg" alt="<?= $fav_pro->u_full_name ?>'s Avatar">
		        <a style="margin-top:0;width:100%;" class="name" href="<?= $central->front_link_base ?>promoters/<?= $fav_pro->up_public_identifier ?>/"><?= $fav_pro->u_full_name ?></a>
		     </li>
    		
		<?php endforeach; ?>
    </ul>

  </section>

  <section style="width:49%;" id="settings" class="column">
    <h1>Your settings</h1>
    <form>
    	
      <p><strong>Email Address:</strong> <?= $user->users_email ?></p>
      <p>
        <input type="checkbox" id="email-optout" name="email-optout" <?= ($user->users_opt_out_email == 1) ? 'checked="checked"' : ''?>>
        <label style="position:relative; top:-4px;" for="email-optout">Don't send me email notices from ClubbingOwl</label>
      </p>
      
      
      <?php if(false): ?>
      <p>
        <input type="checkbox" id="search-optout" name="search-optout" <?= ($user->users_opt_out_search == 1) ? 'checked="checked"' : ''?>>
        <label for="search-optout">Don't show me in search-results.</label>
      </p>
      <?php endif; ?>
    </form>
    
    
  </section>

</article>