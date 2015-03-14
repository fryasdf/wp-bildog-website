<?php
  // it is the projects page were on right now
  // --> show the short description of all the projects in
  // a reasonably nice format

  $parent_page = get_page_by_title( 'Team' );
  $box_content = "content";
  $box_content_default = "Keine Information vorhanden";
  $header_pic = "featured_image";
  $only_direct_children = TRUE;
  $expandable = TRUE;
  // this value is ignored as $css_values is set!
  $nr_of_entries_per_row = 1;
  
  // i.e. for extra small displays only   1 entry per row
  //      for small displays              2 entries per row
  //      for medium and large displays   3 entries per row
  $css_classes = "col-xs-12 col-sm-6 col-md-4";
  
  $add_custom_css_class = TRUE;
  include(locate_template('list-of-short-descriptions.php'));
