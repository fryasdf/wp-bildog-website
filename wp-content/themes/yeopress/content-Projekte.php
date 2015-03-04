<?php
  // it is the projects page were on right now
  // --> show the short description of all the projects in
  // a reasonably nice format

  $invisible_projects_page = get_page_by_title( 'unsichtbar_Projekte' );
  $id_of_invisible_projects_page = $invisible_projects_page->ID;

  // get all the child pages of the site called 'unsichtbar_Projekte'
  $args = array(
    'child_of' => $id_of_invisible_projects_page,
    'sort_column' => 'menu_order'
  ); 
  $pages = get_pages($args); 
  $amount_pages = sizeof($pages);
  $i = 0;
  for ($i = 0; $i <= ceil($amount_pages/3); $i++) {
    echo '<div class="row">' . "\n";
    for ($j = 3*$i; $j < 3*($i + 1) && $j < $amount_pages; $j++) {
      $page = $pages[$j];
      include(locate_template('content-Projekte-Projekt.php'));
    } 
    // end <div class="row">
    echo '</div>' . "\n";
  }
