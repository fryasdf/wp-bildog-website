<?php
  /*
   This file reads out the wordpress database and displays a table
   of the children of a given page using a specificable template file.
   It does not necessarily display all of them:
   if $check_function is specified then only those pages are shown for which
   $check_function($page) return true.

   REQUIRED VARIABLES:
   $parent_page
     Type:  WP_Post
     Value: The page of which all the children should be listed
  
   $check_function
     Type:  String
     Value: If specified, needs to be the name of a function
            that accepts WP_Post as input and returns a Boolean.
            Only those pages are shown that pass this test.

   $item_template_file (optional)
     Type:  String
     Value: The filename that is used to render every list item
            If this is unset, then 'content-short-description-box.php'
            is used.

   $only_direct_children (optional)
     Type:  Boolean
     Value: If true then only the direct children of the parent page
            will be displayed. Otherwise, all children (including grandchildren)
            will be displayed.

   $nr_of_entries_per_row
     Type:  Integer
     Value: Either 1,2,3,4,6 or 12
            Self explaining, the number of columns per row.

   $box_content: see short-description-box.php
   $box_content_default: see short-description-box.php
   $header_pic: see short-description-box.php
*/
  
  if (!isset($parent_page)) {
    echo '<h1>content-list-of-short-descriptions.php: ERROR: ' .
         'varible $parent_page required but not set yet!' .
         '</h1>';
  }
  if (!isset($nr_of_entries_per_row)) {
    echo '<h1>content-list-of-short-descriptions.php: ERROR: ' .
         'variable $nr_of_entries_per_row required but not set yet!' .
         '</h1>';
  }
  if ($nr_of_entries_per_row != 1 &&
    $nr_of_entries_per_row != 2 &&
    $nr_of_entries_per_row != 3 &&
    $nr_of_entries_per_row != 4 &&
    $nr_of_entries_per_row != 6 &&
    $nr_of_entries_per_row != 12) {
    echo '<h1>content-list-of-short-descriptions.php: ERROR: ' .
         'variable $nr_of_entries_per_row required to be 1,2,3,4,6 or 12 ' .
         'but it was set to a different value: ' . $nr_of_entries_per_row;
         '</h1>';
  }

  $id_of_parent_page = $parent_page->ID;

  // get all the child pages of the site called 'unsichtbar_Projekte'
  $args = array(
    'child_of' => $id_of_parent_page,
    'sort_column' => 'menu_order'
  ); 
  // exclude grandchildren
  if ($only_direct_children) {
    $args['parent'] = $id_of_parent_page;
  }
  $pages = get_pages($args);

  
  if (isset($check_function)) {
    $new_pages = array();
    for ($i = 0; $i < sizeof($pages); $i++) {
      // if the check fails then 'delete' the page from the array
      if ($check_function($pages[$i])) {
        array_push($new_pages, $pages[$i]);
      }
    }
    $pages = $new_pages;
  }

  
  $amount_pages = sizeof($pages);
  // for improved readability
  $a = $nr_of_entries_per_row;
  for ($i = 0; $i <= ceil($amount_pages/$a); $i++) {
    echo '<div class="row">' . "\n";
    for ($j = $a*$i; $j < $a*($i + 1) && $j < $amount_pages; $j++) {
      // variables needed for content-short-description-box.php
      $page = $pages[$j];
      if (isset($item_template_file)) {
        include(locate_template($item_template_file));
      } else {
        include(locate_template('short-description-box.php'));
      }
    } 
    // end <div class="row">
    echo '</div>' . "\n";
  }
