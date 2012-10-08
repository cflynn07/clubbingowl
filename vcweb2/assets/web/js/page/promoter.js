$(function() {

	return;

  /**
   * Ratings
   */

  (function($) {
    
    function initRating() {
      var $container = $(".rating");
      var $starsOn = $(".stars-on");
      var count = $container.data('stars');
      if(typeof count !== 'undefined') {
   //     $starsOn.width(count * 20 + "%");
      }
    }

    initRating();

  })(jQuery);

  /**
   * Guestlist Info/Join
   */
  (function($) {

    var $guestlist = $("#guestlist");
    var $buttons = $guestlist.find('.action a').not('.inactive');
    var $table = $guestlist.find('.guestlist-table');
    var $form = $guestlist.find('.guestlist-form');
    var $confirmationText = $("#guestlist-confirmation-text");
    var $cancel = $("#guestlist-form-cancel");

    function showDetails() {
      $table.animate({ 'opacity' : 0 }, 500, function() {
        $(this).hide();
        $form.css({ 'opacity' : 0, 'display': 'block' }).animate({ 'opacity' : 1 }, 500);
      });
      
    }

    function showTable() {
      $form.animate({ 'opacity' : 0 }, 500, function() {
        $(this).hide();
        $table.css({ 'opacity' : 0, 'display': 'block' }).animate({ 'opacity' : 1 }, 500);
      });
    }

    $buttons.on('click', function(e) {
      showDetails();
    });

    $cancel.on('click', function(e) {
      showTable();
      e.preventDefault();
    });

    $confirmationText.on('click', function(e) {
      log('test');
      $(this).parent().find('.confirmation-details').slideToggle();
    });

  })(jQuery);

});