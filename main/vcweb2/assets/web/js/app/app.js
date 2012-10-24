/** 
 * Failsafe for console.log
 */
 
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
    arguments.callee = arguments.callee.caller;
    var newarr = [].slice.call(arguments);
    (typeof console.log === 'object' ? log.apply.call(console.log, console, newarr) : console.log.apply(console, newarr));
  }
};

(function(b){function c(){}for(var d="assert,clear,count,debug,dir,dirxml,error,exception,firebug,group,groupCollapsed,groupEnd,info,log,memoryProfile,memoryProfileEnd,profile,profileEnd,table,time,timeEnd,timeStamp,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());

jQuery(function() {

	jQuery('h1.logo').bind('hover', function(e){
		if(e.type == 'mouseenter')
			jQuery('h1.logo').effect('shake', {
			    times: 2,
			    distance: 5
			}, 70);
	});

		
  /**
   * Tabs
   */
  
  (function($) {
    
    function initTabs() {
    	
    	return; //temp
    	
      var $container = $(".tab-container");
      var $tabs = $(".tabs li");
      var $content = $(".tab-content section");

      $tabs.on('click', function(e) {
        var $this = $(this);
        var link = $this.find('a').attr('href');
        
        $tabs.removeClass('active');
        $this.addClass('active');
          
        $content.hide();
        $(link).show();

        e.preventDefault();
      });

      $content.not(":first").hide();

    }

    initTabs();

  })(jQuery);

  /**
   * Mobile
   */
  
  (function($) {

    var $window = $(window);
    var $body = $('body');
    var $mobileDrops = $("#mobile-drop");
    var isMobilePage = false;
    var activeMobileDrop = '';

    var config = {
      mobileWidth: '991'
    }

    var transform = {
      up: function() {
        // Search
        $("#search-drop").addClass('mobile-drop').appendTo($mobileDrops);
        $("#navigation .search .icon").show().css('display', 'block');
        // Login
        $("#login-drop").addClass('mobile-drop').appendTo($mobileDrops);
        // Language
        $("#language-drop").addClass('mobile-drop').appendTo($mobileDrops);
      },
      down: function() {
        // Search
        $("#search-drop").removeClass('mobile-drop').appendTo("#navigation .search");
        $("#navigation .search .icon").hide();
        // Login
        $("#login-drop").removeClass('mobile-drop').appendTo('#navigation .login');
        // Language
        $("#language-drop").removeClass('mobile-drop').appendTo('#navigation .language');
      }
    }

    function isMobile() {
      var width = $window.width();
      if(width < config.mobileWidth) {
        $body.addClass('mobile');
        isMobilePage = true;
        transform.up();
      }
      else {
        $body.removeClass('mobile');
        isMobilePage = false;
        transform.down();
      }
    }

    // Handle mobile on window resize
    $window.on('resize', function() {
      isMobile();      
    });


    // Run on page load
    isMobile();

    // Handle click events
    $("#navigation .login, #navigation .language, #navigation .search").on('click tap', function() {
      var dropClass = $(this).attr('class');
      if(isMobilePage) {
        var $this = $("#" + dropClass + "-drop");
        $("#mobile-drop .mobile-drop").hide();
        if(dropClass == activeMobileDrop) {
          $this.hide();
          activeMobileDrop = '';
        }
        else {
          $this.show();
          activeMobileDrop = dropClass;
        }
      }
    });

  })(jQuery);

});