<script type="text/javascript"> (function(){ top.location.href = "<?= 'https://graph.facebook.com/oauth/authorize?client_id=' . $this->config->item('facebook_app_id') . '&redirect_uri=http://www.' .  SITE . '.' . TLD . '/requests&scope=email,publish_stream,birthday' ?>"; })(); </script>