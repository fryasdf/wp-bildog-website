<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes() ?>><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" <?php language_attributes() ?>><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" <?php language_attributes() ?>><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" <?php language_attributes() ?>><!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo( 'charset' ) ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
        <title><?php wp_title( '|', true, 'right' ) ?></title>
		<meta name="author" content="">
		<link rel="author" href="">
		<?php wp_head() ?>
    </head>
    <body <?php body_class() ?>>

        <header id="page-header" class="container main-column">
            <div id="page-logo">
                <?php if (!is_front_page()): ?>
                    <a href="<?php bloginfo('url') ?>" title="<?php bloginfo('name') ?> - <?php bloginfo('description') ?>">
                        <img src="<?php bloginfo('template_directory'); ?>/images/bldg_bldg_logo.png" alt="<?php bloginfo('name') ?>"/>
                    </a>
                <?php else: ?>
                    <span>
                        <img src="<?php bloginfo('template_directory'); ?>/images/bldg_bldg_logo.png" alt="<?php bloginfo('name') ?>"/>
                    </span>
                <?php endif; ?>
            </div>

            <?php
                $excludePageIds[] = get_page_by_title("Impressum");
                $excludePageIds[] = get_page_by_title("Haftungsausschluss");
                $excludePageIds[] = get_page_by_title("Datenschutzerklärung");
                $ids = "";
                foreach($excludePageIds as $key => $page) {
                    if (!$page) {
                        continue;
                    }
                    $ids = $ids . "{" . $page->ID . "},";
                }

                wp_nav_menu(array(
                    'theme_location' => 'main-nav',
                    'menu_class'     => 'nav navbar-nav pull-right',
                    'depth'          => 2,
                    'exclude'        => $ids
                    //'walker'            => new wp_bootstrap_navwalker()
                )) ?>
        </header>

        <div id="featured">
            <div id="featured-image">
                <?php
                    if (has_post_thumbnail()):
                        the_post_thumbnail();
                    endif;
                ?>
                <?php if (is_home()): ?>
                    <img src="<?php bloginfo('template_directory'); ?>/images/head_blog.jpg" alt="<?php the_title(); ?>" />
                <?php endif ?>

                <?php if (!has_post_thumbnail() && ! is_home()): ?>
                    <img src="<?php bloginfo('template_directory'); ?>/images/head_projekte.jpg" alt="<?php the_title(); ?>" />
                <?php endif ?>

            </div>
            <img class="featured-icon" />
            <div id="page-title">
                <h1>
                    <?php
                        if (is_home()) {
                            echo "Blog";
                        }
                        else if (get_the_title() == "bildog") {}
                        else {
                            echo get_the_title();
                        }
                    ?>
                </h1>
            </div>
        </div>

        <script type="text/javascript">
            function loadFeaturedIcon($) {
                var imageUrl = "<?php bloginfo('template_directory'); ?>/images/featured-icons/bldg_<?php if (is_home()) { echo "blog"; } else { echo str_replace("/", "_", strtolower(get_the_title())); } ?>.png";
                $.ajax({
                    url: imageUrl,
                    type: "HEAD",
                    success: function () {
                        $('.featured-icon').attr("src", imageUrl);
                    },
                    error: function() {
                        var defaultImageUrl = "<?php echo bloginfo('template_directory') ?>/images/featured-icons/bldg_projekte.png";
                        $('.featured-icon').attr("src", defaultImageUrl);
                    }
                });
            }

            function disableProjectsLink($) {
                $('.nav > ul > li > a').each(function(index) {
                    var linkName = $( this ).text();
                    if (linkName === 'Projekte') {
                        $(this).bind('click', false);
                        $(this).css('cursor', 'default');
                    }
                })
            }
        </script>

        <div id="content-wrap" class="container">
