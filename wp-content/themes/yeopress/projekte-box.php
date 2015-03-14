<div class="col-xs-6 col-sm-3 col-md-2 short-description-container">
  <a href="<?php
      echo get_permalink($page->ID);
    ?>" class="no-interaction-link">
  <div class="short-description-box-mini">
      <img src="<?php echo get_icon($page->post_title);
          ?>" class="icon">
      <img src="<?php 
        echo get_icon_without_png($page->post_title) . "_white.png"; 
        ?>" class="icon icon-hover">
  </div>
  </a>
</div>

