<?php
  /* INPUT DATA:
   $page 
     Type:    WP_Post
     Value:   the page of which we have to show a short description

   $box_content
     Type:    String
     Value:   One of {'content', 'short_tag', 'nothing'}
       'page' then in the box, the page content is displayed
       'short_tag' then this file looks in the page content for the string
                   $SHORT_DESCRIPTION_TAGNAME{blah}
                   and displays 'blah' instead of the page content
       'nothing' then no text is displayed below the header

   $box_content_default
     Type:    String
     Value:   arbitrary
       This is shown if $box_content was set to 'short_tag'
       and no short tag has been found in the pages content.
       If there wasnt any short tag giving a short description
       and this variable has not been set then an ERROR is printed.

   $nr_of_entries_per_row : see list-of-short-descriptions.php

   $header_pic
     Type:    String
     Value:   one of {'icon', 'featured_image', 'nothing'}
       'icon' then the icon as defined by get_icon in functions.php
              is shown
       'featured_image' then the featured image (dt: Beitragsbild)
              as defined in get_featured_image in functions.php is shown
       'nothing' then no header picture is shown

   $expandable (optional)
     Type:    Boolean
     Value:   If true then there is an arrow under each short description
              allowing the user to expand it if the content is too high
              for the box.
              If it is not set then it is treated as 'FALSE'.

   $j (optional but must be given is expandable is set)
     Type:  Integer
     Value: The index of the current item in the list

   $link_to_page (optional)
     Type:  Boolean
     Value: If true then after each short description, a link
            to the actual page will be shown.

   $link_title (optional, must be set if $link_to_page is set) 
     Type:  String
     Value: This text will be shown as the links title, for example
            'read more'.

   $header_is_link_to_page (optional)
     Type:  Boolean
     Value: If true then the header area will be a link to the respective
            page of which we are showing the short description right now.

   $nr_of_entries_per_row (see list-of-short-descriptions.php)
   $css_classes (see list-of-short-descriptions.php)
   $add_custom_css_class (see list-of-short-descriptions.php)
   $custom_css_class 
     Type:  String
     Value: If $add_custom_css_class is set then 
            short-description-box-$custom_css_class is appended
            as a css class.
  */

  if (!isset($page)) {
    my_error(__FILE__, 'variable $page is undefined!');
  }
  if (!isset($box_content)) {
    my_error(__FILE__, 'variable $box_content is missing!');
  }
  if (!isset($header_pic)) {
    my_error(__FILE__, 'variable $header_pic is missing!');
  }

  if (isset($link_to_page)) {
    if (!is_boolean($link_to_page)) {
      my_error(__FILE__, '$link_to_page is not boolean!');
    }
    if ($link_to_page === TRUE) {
      if (!isset($link_title)) {
        my_error(__FILE__, 'variable $link_to_page is set but $link_title is unset!');
      }
    }
  }

?>

<!-- show the page including... -->
<div class="<?php 
  if (!isset($css_classes)) {
    echo 'col-md-' . floor(12/$nr_of_entries_per_row);
  } else {
    echo $css_classes;
  }
  ?> short-description-container">
  <div class="short-description-box<?php
    if (isset($add_custom_css_class)) {
      echo " short-description-box-" . $custom_css_class;
    }
    ?>">
    <?php if($header_is_link_to_page) : ?>
    <a href="<?php echo get_permalink($page->ID)?>" class="no-interaction-link">
    <?php endif; ?>
      <div class="header">
        <!-- header pic -->
          <?php 
            if ($header_pic != "nothing") {
              echo '<center><img src="';
              if ($header_pic === "icon") {
                echo get_icon($page->post_title);
              }
              if ($header_pic === "featured_image") {
                echo get_featured_image($page->ID);
              }
              echo '" class="icon"></center>';
            }
            ?>
        <!-- title -->
        <div class="titel">
          <h1> <?php echo $page->post_title ?></h1>
        </div>
      </div>
    <?php if ($header_is_link_to_page): ?>
    </a>
    <?php endif; ?>
      <div class="short-description-content">
        <!-- the content of the page
             precisely as wordpress would show it, i.e. including 
             shortcodes, etc) -->
        <?php
          $content = ""; 
          if ($box_content == "nothing") {
            $content = "";
          }
          if ($box_content == "content") {
            $content = $page->post_content;
          }
          if ($box_content == "short_tag") {
            global $SHORT_DESCRIPTION_TAGNAME;
            // if this page has a short description then show
            // the short description instead of the actual page content
            if (has_mytag($page->post_content, $SHORT_DESCRIPTION_TAGNAME)) {
              $content = get_mytag_contents(
                           $page->post_content, 
                           $SHORT_DESCRIPTION_TAGNAME);
            } else {
              if (isset($box_content_default)) {
                $content = $box_content_default;
              } else {
                $content = '<h1>ERROR: content-short-description-box.php:' .
                           '$content has been set to "short_tag", no short ' .
                           'tag has been found and no default content ' .
                           '(i.e. variable $box_content_default) was ' .
                           'specified!</h1>';
              }
            }
          }
          echo prepare_content_as_wordpress_would_do($content);
        ?>
        <?php if ($link_to_page) : ?>
          <center>
          <strong>
          <a href="<?php 
             echo get_permalink($page->ID)
             ?>" class="no-interaction-link short-description-link">
            <?php echo $link_title ?>
          </a>
          </strong>
          </center>
        <?php endif; ?>
      </div>
    <?php if($expandable): ?>
    <div class="toggle-link-inner" onclick="expand(<?php echo $j?>)">
      <img src="<?php echo get_bloginfo('template_directory')?>/images/arrow_down.png">
    </div>
    <?php endif; ?>
  </div>
  <?php if($expandable): ?>
  <div class="toggle-link-outer" onclick="collapse(<?php echo $j?>)">
    <img src="<?php echo get_bloginfo('template_directory')?>/images/arrow_up.png">
  </div>
  <?php endif; ?>
</div>

