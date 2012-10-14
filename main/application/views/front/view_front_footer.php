</div>
	<footer id="footer"><div class="center">

  <div class="left">
  	
  	
  	<?php if(false): ?>
  	<a class="branding">VibeCompass</a>
  	<?php endif; ?>
  	
   	<?php if(true): ?>
  	<div class="fb-like-box" data-href="http://www.facebook.com/clubbingowl" data-width="292" data-colorscheme="dark" data-show-faces="false" data-stream="false" data-header="false"></div> 
  	
  	<br>
  	
  	<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
	<script>
	new TWTR.Widget({
	  version: 2,
	  type: 'profile',
	  rpp: 1,
	  interval: 30000,
	  width: 290,
	  height: 180,
	  theme: {
	    shell: {
	      background: '#333333',
	      color: '#ffffff'
	    },
	    tweets: {
	      background: '#000000',
	      color: '#ffffff',
	      links: '#3CA6E2'
	    }
	  },
	  features: {
	    scrollbar: false,
	    loop: true,
	    live: false,
	    behavior: 'default'
	  }
	}).render().setUser('BarackObama').start();
	</script>
  	
  	<?php endif; ?>
  	
  </div>

  <div class="content">
  
    <ul>
      <li><a href="<?= $central->front_link_base ?>corp/"><?= $this->lang->line('f-team') ?></a></li>
      <li><a href="<?= $central->front_link_base ?>corp/tos/"><?= $this->lang->line('f-tos') ?></a></li>
    </ul>
	
	<h3><?= $this->lang->line('f-m1') ?></h3>
			
	<p><?= $this->lang->line('f-m2') ?></p>
	
	<p><?= $this->lang->line('f-m3') ?></p>	
	
  </div>

</div></footer>

<!-- Piwik -->
<?php //some pages (promoters pages in particular) are defined as unique sites that are a subset of the overall site
//in piwik. Echo appropriate tracking code here. 
if(isset($additional_sites_ids)): ?>
	<?= piwik_tag($additional_sites_ids); ?>
<?php else: ?>
	<?= piwik_tag(); ?>	
<?php endif; ?>
<!-- End Piwik Tracking Code -->

</body>
</html>
<?php if(extension_loaded('newrelic')): ?>
	<?= newrelic_get_browser_timing_footer(); ?>
<?php endif; ?>