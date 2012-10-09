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