<?php
  // it is the projects page were on right now
  // --> show the short description of all the projects in
  // a reasonably nice format

  $parent_page = get_page_by_title( 'unsichtbar_Team' );
  $box_content = "content";
  $box_content_default = "Keine Information vorhanden";
  $header_pic = "featured_image";
  $only_direct_children = TRUE;
  $expandable = TRUE;
  $nr_of_entries_per_row = 3;
  include(locate_template('list-of-short-descriptions.php'));
