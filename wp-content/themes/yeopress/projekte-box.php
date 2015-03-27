<?php
  $params = $GLOBALS['my_show_children_params'];
  $page = $GLOBALS["my_show_children_page"];

  $css_classes = $params['css_classes'];
  $add_custom_css_class = $params['add_custom_css_class'];
  $custom_css_class = $params['custom_css_class'];
  $header_is_link_to_page = $params['header_is_link_to_page'];
  $header_pic = $params['header_pic'];
  $box_content = $params['box_content'];
  $box_content_default = $params['box_content_default'];
  $expandable = $params['expandable'];
  $nr_of_entries_per_row = $params['nr_of_entries_per_row'];
?>

<div class="col-xs-6 col-sm-3 col-md-2 short-description-container">
  <a href="<?php
      echo get_permalink($page->ID);
    ?>" class="no-interaction-link">
  <div class="short-description-box-mini" 
       alt="<?php echo $page->post_title ?>" 
       title="<?php echo $page->post_title ?>">
      <img src="<?php echo get_icon($page->post_title);
          ?>" 
           alt="<?php echo $page->post_title ?>" 
           title="<?php echo $page->post_title ?>"
           class="icon">
      <img src="<?php 
        echo get_icon_without_png($page->post_title) . "_w.png"; 
        ?>" 
        alt="<?php echo $page->post_title ?>" 
        title="<?php echo $page->post_title ?>" 
        class="icon icon-hover">
  </div>
  </a>
</div>

