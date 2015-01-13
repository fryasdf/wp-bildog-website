require.config({
	"baseUrl": "wp-content/themes/yeopress/js",
    "shim" : {
        "bootstrap" : { "deps" :['jquery'] }
    },
	"paths": {
		"jquery": "vendor/jquery/jquery",
        "bootstrap": "../node_modules/bootstrap-sass/assets/javascripts/bootstrap"
	}
});

require(['jquery', 'bootstrap'], function($) {
	console.log('Working!!');

    // Hack to enable dropdown.
    // IF you use this remove :hover menu from css
//    $('.page_item_has_children').on('click', function(event) {
//        if (!$(this).hasClass('open')) {
//            event.preventDefault();
//            $(this).addClass('open');
//        }
//    });

    loadFeaturedIcon($);
    disableProjectsLink($);
    
});

