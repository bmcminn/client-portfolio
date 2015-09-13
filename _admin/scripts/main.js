(function($) {
    "use strict";
    var $doc = $(document), $body = $("body"), $nav = $(".page-nav"), www = {
        host: "//" + window.location.host
    };
    // Load the SVG graphics needed
    $("#svg-includes").load(www.host + "/images/dimfist.svg");
    // Generate back-to-top button
    $body.append('<button data-action="back-to-top"></button>');
    var $backToTop = $('[data-action="back-to-top"]');
    // Back-to-top functionality
    $backToTop.hide().on("click", function(e) {
        e.preventDefault();
        $body.scrollTo(0);
    });
})(jQuery);