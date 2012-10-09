
<article class="promoter">

  <header>
    <h1><?= $promoter->u_full_name ?></h1>
    <div class="location"><?= $promoter->team->c_name . ', ' . $promoter->team->c_state ?></div>
  </header>

  <div class="left">
    <div class="avatar"><img style="border: 1px solid lightgray;" src="<?= $promoter->profile_image_complete_url ?>" alt="<?= $promoter->u_full_name ?>'s Picture"></div>
    <div class="rating" data-stars="1">
      <div class="stars">
        <div class="stars-on" style="width:0px;"></div>
        <div class="stars-off" style="width:100%;"></div>
      </div>
    </div>
    <p><?= lang_key($this->lang->line('p-ranking_msg'), array('promoter_full_name' => $promoter->u_full_name)) ?></p>
  </div>

  <div class="right non_fb_plugin">
    
    <div class="tab-container">
    	      
      <div class="tab-content">