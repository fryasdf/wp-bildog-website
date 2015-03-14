<?php

global $TITLE_LOCALE_TAGNAME;
// checks if the page has a mytag TITLE_LOCALE_TAGNAME
// and if its contents contains "Hamburg"
function check_locale_Hamburg($page) {
  // DOLLY DEBUG REMOVE
  $cont = $page->post_content;
  if (has_mytag($cont, $TITLE_LOCALE_TAGNAME)) {
    if (preg_match_all('/Hamburg/', get_mytag_contents($cont, $TITLE_LOCALE_TAGNAME), $hits)) {
      return TRUE;
    } else {
      return FALSE;
    } 
  }
  return FALSE;
} 

  $parent_page = get_page_by_title('Projekte');
  $box_content = "nothing";
  $header_pic = "icon";
  $item_template_file = "projekte-box.php";
  $nr_of_entries_per_row = 1;   // this is ignored in this case,
                                // see projekt-box.php
  $check_function = "check_locale_Hamburg";
  // show short summary of all projects in Hamburg
  include(locate_template('list-of-short-descriptions.php'));

  // show the contents of the page
  $content = get_the_content();
  echo prepare_content_as_wordpress_would_do($content);
 
   
  
