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
    <script type="text/javascript">
      // DEBUG
      /*
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

      function collapse(toggleLinkArrowUp) {
        shortDescriptionBox = toggleLinkArrowUp.parentElement.firstElementChild;
        toggleLinkArrowDown = toggleLinkArrowUp.parentElement.firstElementChild.lastElementChild;

        var BOX_HEIGHT = "<?php 
          $file_name = getcwd() . '/' . str_replace(get_bloginfo('url') . '/', '', get_bloginfo('template_directory')) . '/scss/_short-description.scss';
          $variable = read_scss_variable($file_name, 'short-description_box_height');
          echo $variable;
        ?>";
        shortDescriptionBox.style.height = BOX_HEIGHT;
        toggleLinkArrowDown.style.display = "block";
        toggleLinkArrowUp.style.display = "none";

      }
      function expand(toggleLinkArrowDown) {
        shortDescriptionBox = toggleLinkArrowDown.parentElement;
        toggleLinkArrowUp = toggleLinkArrowDown.parentElement.nextElementSibling;
        shortDescriptionBox.style.height = "auto";
        toggleLinkArrowDown.style.display = "none";
        toggleLinkArrowUp.style.display = "block";
      }
    </script>
  </head>
  <?php // end head ?>
 
   
  <body <?php body_class() ?>>
  <?php
    /* the total header.php contains:
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
         
    */
    ?>
    <header id="page-header" class="container main-column">
      <?php // reference to frontpage with image ?>
      <div id="page-logo">
        <?php // but only if its not the frontpage were on ?>
        <?php if (!is_front_page()): ?>
          <a 
           href="<?php bloginfo('url') ?>" 
           title="<?php bloginfo('name') 
                   ?> - <?php bloginfo('description') ?>">
          
             <img 
              src="<?php bloginfo('template_directory');
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
     
      <?php /* 
           the navigation bar
           wp_nav_menu( ... ) is a wordpress function
           that offers a ertain 'type of style'-selection bar
           CAREFUL: we are using a custom walker defined in functions.php
           */
      ?> 
      <?php
        /* we dont want the following pages to be indexed 
           also, MyWalker() excludes all pages with a title
           of the form unsichtbar_* (and subpages of them)*/
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
          'depth'          => 2,
          'exclude'        => $ids,
          'walker'            => new MyWalker()
        )) 
      ?>
      <?php
        // print customized language switcher
        // pll_the_languages is a wordpress built-in php function which 
        // deals with getting the translation menu
        // however, the default call 
        //   pll_the_languages( array( 'show_flags' => 1,'show_names' => 0 ) );
        // prints out a shitty looking html list
        // --> do it in a customized way: just put the two flags next to each other
        //     below the nav bar
        
        // polylang interfers in such a way that when doing the call below one
        // gets a nice array with all the information about the current page one
        // needs
        $translations = pll_the_languages( array( 'raw' => 1 ) );
        // problematic: when there is no translation availble then the array still
        // contains an entry for the foreign language but its entry 'no_translation'
        // is then set to 1
        // --> throw out all languages for which no translation of the current 
        // (i.e. this page that the users looks at) exists 
        foreach ($translations as $languageKey => $translation) {
          if ($translation['no_translation']) {
            unset($translations[$languageKey]);
          }
        }
        // debug: check the language array
        //echo "\n\nDOLLY\n\n";
        //print_r($translations);
        //echo "\n\n DOLLYDOLLY \n\n";

        // for every translation available:
        // show a little flag with the corresponding country
        // 'containing' a link to the translated page
        if (count($translations) > 1) {
          echo "<div style=\"clear:right;text-align:center\">";
          foreach ($translations as $languageKey => $translation) {
            $url = $translation['url'];
            $flagURL = $translation['flag'];
            $languageName = $translation['name'];
            echo "<a href=\"$url\"><img style=\"margin:5px\" src=\"$flagURL\" alt=\"$languageName\"></a>";
          }
          echo "</div>";
        }
      ?>
    </header>
   

    <?php
         /*
          show the featured image (german: Beitragsbild) 
         i.e. the image that has been associated to this page as 
         'Beitragsbild' in wordpress 
         if there is none, show a default image and icon (overlayed) 
         */ 
    ?>
    <div id="featured">
      <div id="featured-image">
        <div id="parallax-layer" style="
           background:url(
           '<?php echo get_featured_image(my_get_current_page_ID()); ?>'
           ); 
           background-size: 100%;
           height:2000px; /* to be honest: i dont know what this setting actually does...*/
           " 
           data-0="margin-top:0px;" 
           data-600="margin-top:-130px;">
        </div>
      <?php // end featured-image ?>
      </div>

      <?php /*
         show the featured icon, that is a second image
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
         */ ?>
 
      <div id="featured-image-overlay">
        <img class="<?php
            if (get_the_title() == "bildog") {
              echo 'featured-icon-bildog-cropped';
            } else {
              echo 'featured-icon';
            }
          ?>"
             src="<?php
              echo get_icon(get_real_title());            
          ?>"
        />
        <div id="page-title">
          <h1>
      <!-- CLEAN TITLE: <?php echo get_clean_title(get_real_title());?> -->
            <?php
              if (get_the_title() == "bildog") {}
              else {
                echo strip_off_mytags(get_real_title());
              }
            ?>
          </h1>
        </div>
     <?php // end featured-image-overlay ?>
     </div>
   <?php // end featured ?>
   </div>

   <script type="text/javascript">
      <?php
      /* there is an empty site called 'Projekte'
         but we dont want the user to be able to access it */
      // Note: we are doing it the jQuery way in js/global.js
      // but if jQuery is not available, we use the clasical way here
      /* EDIT: now the design has changed, now the site 'Projekte'
         MUST be accesible!*/
      /*
      var x = document.getElementsByClassName("page_item_has_children");
      for (i = 0; i < x.length; i++) {
        var y = x[i].getElementsByTagName("a");
        if (y[0].innerHTML === "Projekte") {
          y[0].style.pointerEvents = "none";
        }
      }        
      */

      /* If the device is a touch screen then the dropdown
         menu does not work as expected, we need to disable all the links
         of items with children then */
      ?>
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


