<article class="venue">

  <header>
    <h1><?= $venue->tv_name ?></h1>
    <div class="location"><?= $venue->c_name . ', ' . $venue->c_state?></div>
  </header>

  <div class="banner">
  	<?php if($venue->tv_image !== ''): ?>
  	<img style="height:300px;" src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->tv_image ?>_p.jpg" alt="Venue Banner">
  	<?php else: ?>
  	<img src="http://placehold.it/1000x300?text=Coming+Soon" alt="Venue Banner">
  	<?php endif; ?>
  </div>
	
  <div class="left">
  	
  	<h2 style="text-align:left;font-size:1.3em;margin-top:0px;" ><?= $this->lang->line('v-description') ?></h2>
  	<p style="text-align:left;padding-bottom:20px;border-bottom:1px dashed #CCC;" class="description"><?= $venue->tv_description ?></p>
  	
  	<h2 style="text-align:left;font-size:1.3em;margin-bottom:20px;" ><?= $this->lang->line('v-map') ?></h2>
    <div class="logo" style="border:1px solid #CCC;margin-bottom:20px;">
    	<img src="http://maps.googleapis.com/maps/api/staticmap?size=290x290&maptype=roadmap&markers=color:red|<?= urlencode($this->library_venues->venue->tv_street_address . ' ' . $this->library_venues->venue->tv_city . ' ' . $this->library_venues->venue->tv_state . ' ' . $this->library_venues->venue->tv_zip) ?> &sensor=false" alt="Venue Map">
    </div>
    <div class="gallery"></div>
  </div>

  <div class="right non_fb_plugin">

    <div class="tab-container">