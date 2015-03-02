require.config({
  "baseUrl": "wp-content/themes/yeopress/js",
  "shim" : {
    "bootstrap" : { "deps" :['jquery'] }
  },
  "paths": {
    "jquery": "vendor/jquery/jquery",
    "bootstrap": "../node_modules/bootstrap-sass/assets/javascripts/bootstrap",
    "skrollr": "skrollr"
  }
});

require(['jquery', 'bootstrap'], function($) {
    // Hack to enable dropdown.
    // IF you use this remove :hover menu from css
//    $('.page_item_has_children').on('click', function(event) {
//        if (!$(this).hasClass('open')) {
//            event.preventDefault();
//            $(this).addClass('open');
//        }
//    });

        $('#jQueryEnabled').each(function(index) {
          $(this).html("1");
        });
    // disable projects link
      /* EDIT: now the design has changed, now the site 'Projekte'
         MUST be accesible!*/
        /*
        $('.nav > ul > li > a').each(function(index) {
          var linkName = $( this ).text();
          if (linkName === 'Projekte') {
            $(this).bind('click', false);
            $(this).css('cursor', 'default');
          }
        });
        */

 
});

require(['skrollr/skrollr.min'], function(skrollr){
    // initialize parallax scrolling script for parallaxing background images
    var s = skrollr.init({
      smoothScrolling: false,
      forceHeight: false
    });
});
