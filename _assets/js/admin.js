(function($) {

  "use strict";

  var $doc        = $(document)
    , $body       = $('body')
    , $nav        = $('.page-nav')
    // , $navOffset  = $nav.offset().top
    // , $navItems   = $nav.find('a')

    , www = {
        host: '//' + window.location.host
      }
    ;


  // Load the SVG graphics needed
  $('#svg-includes').load(www.host + '/images/dimfist.svg');


  // Generate back-to-top button
  $body
    .append('<button data-action="back-to-top"></button>')
    ;

  var $backToTop  = $('[data-action="back-to-top"]')
    ;

  // Back-to-top functionality
  $backToTop
    .hide()
    .on('click', function(e) {
      e.preventDefault();
      $body.scrollTo(0);
    })
    ;


  // When the page scrolls, do stuff
  // $doc
  //   .scroll(function() {
  //     var top = $doc.scrollTop()
  //       ;

  //     toggleScrollFeatures(top);
  //   })
  //   ;


  // // Setup scroll action for nav buttons
  // $navItems.each(function() {
  //   var $this = $(this)
  //     ;

  //   $this.on('click', function() {
  //     $navItems.removeClass('active');
  //     $this.addClass('active');
  //     $body.scrollTo($this.attr('href'));
  //   });
  // });


  // /**
  //  * [toggleScrollFeatures description]
  //  * @param  {[type]} top [description]
  //  * @return null
  //  */
  // function toggleScrollFeatures(top) {

  //   if (top >= $navOffset) {
  //     $backToTop.fadeIn();                            // Toggle back-to-top button
  //     $nav.removeClass('sticky').addClass('sticky');  // Toggle nav link

  //   } else {
  //     $backToTop.fadeOut();                           // Toggle back-to-top button
  //     $nav.removeClass('sticky');                     // Toggle nav link

  //   }

  //   return null;
  // }




})(jQuery);

