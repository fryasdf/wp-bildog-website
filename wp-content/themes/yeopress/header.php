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
    <title><?php wp_title( '') ?></title>
    <meta name="author" content="">
    <link rel="author" href="">
    <?php wp_head() ?>
    <!-- for parallaxing background images -->
    <script type="text/javascript" src="<?php echo get_bloginfo('url'); ?>/js/skrollr.min.js"></script>
    <script type="text/javascript">
      /* DEBUG
      window.onresize = displayWindowSize;
      window.onload = displayWindowSize;
      function displayWindowSize() {
        document.getElementById("dimensions").innerHTML = window.innerWidth + "x" + window.innerHeight;
      };
      */
    //  var x = document.getElementById("page-logo");
    //  var x = document.getElementById("page-logo");
      //var x = document.getElementsByClassName("page_item_has_children");
      // SOME MORE DEBUG
      // CSS3 asks for this property when applying media query
      // @media only screen and (min-width: ...px)
      //alert(window.innerWidth);
    </script>

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
        $excludePageIds[] = get_page_by_title("Kontakt");
        $excludePageIds[] = get_page_by_title("Test");
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
          'depth'          => 3,
          'exclude'        => $ids,
          'walker'            => new MyWalker()
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
          // wordpress treats the 'blog' page a little 
          // differently than the others
          if (is_home()) {
            $url = get_bloginfo('template_directory') . '/images/head_blog.jpg';
          } else {
            if (has_post_thumbnail()) {
              $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
            } else {
              $url = get_bloginfo('template_directory') . 
                       '/images/head_projekte.jpg';
            }
          }
        ?>
 
        <div id="parallax-layer" style="
           background:url(
           '<?php echo $url; ?>'
           );
           background-size: 100%;
           height:2000px; /* to be honest: i dont know what this setting actually does...*/
           " 
           data-0="margin-top:0px;" 
           data-600="margin-top:-130px;">
        </div>
      <!-- end featured-image -->
      </div>

    <!-- show the featured icon, that is a second image
         being shown in the middle over the featured image 
         file selected:
         <template_directory>/images/featured-icons-bldg_<titleOfPage> 
         if there is no such file (CAREFUL: 
         or it cant be accessed due to missing permissions!) 
         then show a default icon
         CAUTION: if the page title contains '/' then this is replaced by '_'
                  also, capitals are replaced by lower cap letters
                  i.e. if the pages name is 'Title/Of/Page' then the icon file
                  must be named bldg_title_of_page.png
         -->
 

      <div id="featured-image-overlay">
        <img class="<?php
            if (get_the_title() == "bildog") {
              echo 'featured-icon-bildog-cropped';
            } else {
              echo 'featured-icon';
            }
          ?>"
             src="<?php
                 $localDirectory = getcwd() . '/' . str_replace(get_bloginfo('url') . '/', '', get_bloginfo('template_directory')) . '/images/featured-icons/';
                 $hostDirectory = get_bloginfo('template_directory') . '/images/featured-icons/';
                 $defaultIconName = 'bldg_projekte.png';
                 if (is_home()) {
                   $iconName = 'bldg_blog.png';
                 } else {
                   $iconName = 'bldg_' . str_replace("/", "_", strtolower(get_the_title())) . '.png';
                 }
                 if (file_exists($localDirectory . $iconName)) {
                   echo $hostDirectory . $iconName;
                 } else {
                   echo $hostDirectory . $defaultIconName;
                 }
          ?>"
        />
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
     <!-- end featured-image-overlay -->
     </div>
   <!-- end featured -->
   </div>

   <script type="text/javascript">
      /* there is an empty site called 'Projekte'
         but we dont want the user to be able to access it */
      // Note: we are doing it the jQuery way in js/global.js
      // but if jQuery is not available, we use the clasical way here
      var x = document.getElementsByClassName("page_item_has_children");
      for (i = 0; i < x.length; i++) {
        var y = x[i].getElementsByTagName("a");
        if (y[0].innerHTML === "Projekte") {
          y[0].style.pointerEvents = "none";
        }
      }        
 
      /* If the device is a touch screen then the dropdown
         menu does not work as expected, we need to disable all the links
         of items with children then */
      if (Modernizr.touch) {   
        var x = document.getElementsByClassName("page_item_has_children");
        for (i = 0; i < x.length; i++) {
          var y = x[i].getElementsByTagName("a");
          y[0].style.pointerEvents = "none";
        }        
        //alert('Touch Screen');  
      } else {   
        //alert('No Touch Screen');  
      }  
    </script>
  <div id="content-wrap" class="container">


