</div>

<footer id="footer">
		
		<div class="center">
			
			<div class="quarter">
				<script charset="utf-8" src="https://widgets.twimg.com/j/2/widget.js"></script>
				<script>
				new TWTR.Widget({
				  version: 2,
				  type: 'profile',
				  rpp: 1,
				  interval: 30000,
				  width: '100%',
				  height: 180,
				  theme: {
				    shell: {
				      background: '#727381',
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
				}).render().setUser('ClubbingOwl').start();
				</script>
				
				<br/>
				<div style="clear:both;"></div>
			</div>
			
			
			<div class="quarter">
				<div class="fb-like-box" data-href="https://www.facebook.com/clubbing-owl" data-width="292" data-colorscheme="dark" data-show-faces="false" data-stream="false" data-header="false"></div>
				<div style="clear:both;"></div>
			</div>
			
			
			<div class="half">
				<?php if(false): ?>
				<img src="<?= $central->front_assets . 'images/ClubbingOwl_origLogoEdit_purple_700x80.jpeg' ?>"/>
				<?php endif; ?>
				<p style="margin-top:0px;font-size:14px">ClubbingOwl is the fastest way to plan your evening! Find out where your friends party and join them. With ClubbingOwl getting on a guest-list or reserving a table is only one click away.</p>
				<div style="clear:both;"></div>
			</div>
			
			<br/>
			
			
			
		</div>
		
		
		
		
		
		
		<div class="footer_msg">&copy; Cobar Systems LLC 2012 - <a href="<?= $central->front_link_base ?>corp/tos/"><?= $this->lang->line('f-tos') ?></a></div>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		<?php if(false): ?>
		<div class="center">
			  <div class="left">
			  	
			  	
			  	<?php if(false): ?>
			  	<a class="branding">VibeCompass</a>
			  	<?php endif; ?>
			  	
			   	<?php if(true): ?>
			  	<div class="fb-like-box" data-href="https://www.facebook.com/clubbing-owl" data-width="292" data-colorscheme="dark" data-show-faces="false" data-stream="false" data-header="false"></div> 
			  	
			  	<br>
			  	
			  	<script charset="utf-8" src="https://widgets.twimg.com/j/2/widget.js"></script>
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
				}).render().setUser('ClubbingOwl').start();
				</script>
			  	
			  	<?php endif; ?>
			  	
			  </div>

			  <div class="content">
			  
			    <ul>
			    	
			    <?php if(false): ?>
			      <li><a href="<?= $central->front_link_base ?>corp/"><?= $this->lang->line('f-team') ?></a></li>
			    <?php endif; ?>
			      
			      <li><a href="<?= $central->front_link_base ?>corp/tos/"><?= $this->lang->line('f-tos') ?></a></li>
			    </ul>
				
				<h3><?= $this->lang->line('f-m1') ?></h3>
						
				<p><?= $this->lang->line('f-m2') ?></p>
				
				<p><?= $this->lang->line('f-m3') ?></p>	
				
			  </div>
			<br />
			
	</div>
	<?php endif; ?>
	
		
</footer>

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