<?php
  // it is the projects page were on right now
  // --> show the short description of all the projects in
  // a reasonably nice format

  $parent_page = get_page_by_title( 'Projekte' );
  $page = $pages[$j];
  $box_content = "short_tag";
  $box_content_default = "Keine Information vorhanden";
  $header_pic = "icon";
  $nr_of_entries_per_row = 3;
  include(locate_template('list-of-short-descriptions.php'));
