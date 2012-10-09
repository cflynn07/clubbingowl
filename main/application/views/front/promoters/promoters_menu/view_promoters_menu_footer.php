	   </div>

    </div>

  </div>

</article>
<script type="text/javascript">
(function(){
	
	<?php
		$obj = new stdClass;
		$obj->up_users_oauth_uid 	= $promoter->up_users_oauth_uid;
		$obj->t_fan_page_id			= $promoter->team->t_fan_page_id;
		$obj->up_id					= $promoter->up_id;
		$obj->pusher_api_key		= $this->config->item('pusher_api_key');
	?>
	
	window.promoter_pusher_presence_vars = <?= json_encode($obj) ?>;
	
})();
</script>
