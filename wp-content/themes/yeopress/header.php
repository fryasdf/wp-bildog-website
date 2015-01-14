<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes() ?>><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" <?php language_attributes() ?>><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" <?php language_attributes() ?>><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" <?php language_attributes() ?>><!--<![endif]-->

  <!-- head: meta information, careful: head != header, see below -->
  <head>
    <meta charset="<?php bloginfo( 'charset' ) ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <title><?php wp_title( '|', true, 'right' ) ?></title>
    <meta name="author" content="">
    <link rel="author" href="">
    <?php wp_head() ?>
  </head>
  <!-- end head -->
  
  <body <?php body_class() ?>>

  <!-- the total header.php contains:
       1) a) link to home with bildog logo
          b) bar with drop-down menus and links to
             home/projects/blog/get involved
       2) a) image that spans throughout the total width of the page
              (called 'featured image' below)
          b) and a symbol as an overlay
             (called 'featured icon' below))
      
       1) is fixed while 2) is selected as follows:
       every site has a title (i.e. its title in wordpress)
       [except for the 'Blog' site, see below]
       Then the featured image is selected to be
       the thumbnail associated to it by wordpress.
       If the site does not have any thumbnail associated then
       a default image/icon is selected:
         image:
          icon:
       The featured icon is
         
  -->
    <header id="page-header" class="container main-column">
      <!-- reference to frontpage with image -->
      <div id="page-logo">
        <!-- but only if its not the frontpage were on -->
        <?php if (!is_front_page()): ?>
          <a 
           href="<?php bloginfo('url') ?>" 
           title="<?php bloginfo('name') 
                   ?> - <?php bloginfo('description') ?>">
          
             <img 
              src="<?php bloginfo('template_directory'); i
                    ?>/images/bldg_bldg_logo.png" 
              alt="<?php bloginfo('name') ?>"/>
          </a>
        <?php else: ?>
          <span>
            <img 
             src="<?php bloginfo('template_directory'); 
                   ?>/images/bldg_bldg_logo.png" 
             alt="<?php bloginfo('name') ?>"/>
          </span>
        <?php endif; ?>
      </div>
     
      <!-- 
        the navigation bar
        wp_nav_menu( ... ) is a wordpredd function
        that offers a ertain 'type of style'-selection bar
        --> 
      <?php
        /* we dont want the following pages to be indexed */
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
        
        /* now show the selection bar */
        wp_nav_menu(array(
          'theme_location' => 'main-nav',
          'menu_class'     => 'nav navbar-nav pull-right',
          'depth'          => 2,
          'exclude'        => $ids
          //'walker'            => new wp_bootstrap_navwalker()
        )) 
      ?>
    </header>

    <!-- show the featured image (german: Beitragsbild) 
         i.e. the image that has been associated to this page as 
         'Beitragsbild' in wordpress 
         if there is none, show a default image and icon (overlayed) 
    -->
    <div id="featured">
      <div id="featured-image">
        <?php
          if (has_post_thumbnail()):
            the_post_thumbnail();
          endif;
        ?>
        <?php if (is_home()): ?>
          <img 
           src="<?php bloginfo('template_directory'); ?>/images/head_blog.jpg" 
           alt="<?php the_title(); ?>" />
        <?php endif ?>

        <?php if (!has_post_thumbnail() && ! is_home()): ?>
          <img 
           src="<?php bloginfo('template_directory'); ?>/images/head_projekte.jpg" 
           alt="<?php the_title(); ?>" />
        <?php endif ?>
      </div>
      
      <img class="featured-icon" />
      <!-- just echo the title, the blog site is a bit weird 
           when it comes to getting its title
      -->
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

    <!-- show the featured icon, that is a second image
         being shown in the middle over the featured image 
         file selected:
         <template_directory>/images/featured-icons-bldg_<titleOfPage> 
         if there is no such file (CAREFUL: 
         or it cant be accessed due to missing permissions!) 
         then show a default icon
         -->
    <script type="text/javascript">
      /* show featured/default icon */
      function loadFeaturedIcon($) {
        var imageUrl = "<?php bloginfo('template_directory'); 
                         ?>/images/featured-icons/bldg_<?php 
                           if (is_home()) { 
                             echo "blog"; 
                           } else { 
                             echo str_replace("/", "_", strtolower(get_the_title())); } ?>.png";
        $.ajax({
          url: imageUrl,
          type: "HEAD",
          success: function () {
            $('.featured-icon').attr("src", imageUrl);
          },
          error: function() {
            var defaultImageUrl = "<?php echo bloginfo('template_directory') 
                                    ?>/images/featured-icons/bldg_projekte.png";
                 $('.featured-icon').attr("src", defaultImageUrl);
          }
        });
      }
      /* there is an empty site called 'Projekte'
         but we dont want to show it */
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
