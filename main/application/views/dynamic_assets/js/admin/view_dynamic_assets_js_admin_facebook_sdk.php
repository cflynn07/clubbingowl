window.fbAsyncInit = function() {
    FB.init({appId: '<?=$central->facebook_app_id?>',
    		status: true,
    		cookie: true,
             xfbml: true});

	//indicates facebook has completed loading
  	fbApiInit = true;
};

//used to load code within body after facebook init complete
function fbEnsureInit(callback) {
    if(!window.fbApiInit) {
        setTimeout(function() {fbEnsureInit(callback);}, 50);
    } else {
        if(callback) {
            callback();
        }
    }
}
  
(function(){
    var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol +
  			'//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
}());
