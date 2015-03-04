<!-- show the page including... -->
<div class="col-md-4 projektbeschreibung-container">
  <div class="projekt-box">
    <div class="header">
      <!-- icon -->
      <center>
        <img src="<?php echo get_icon($page->post_title) ?>" class="icon">
      </center>
      <!-- title -->
      <div class="titel">
        <h1> <?php echo $page->post_title ?></h1>
      </div>
    </div>
      <div class="projekt-inhalt">
        <!-- the content of the page
             precisely as wordpress would show it, i.e. including 
             shortcodes, etc) -->
        <?php 
          $page_content = apply_filters( 'the_content', $page->post_content );
          $page_content = str_replace( ']]>', ']]&gt;', $page_content );
          echo $page_content;
        ?>
      </div>
    <div class="toggle-link-inner" onclick="expand(<?php echo $j?>)">
      <img src="<?php echo get_bloginfo('template_directory')?>/images/arrow_down.png">
    </div>
  </div>
  <div class="toggle-link-outer" onclick="collapse(<?php echo $j?>)">
    <img src="<?php echo get_bloginfo('template_directory')?>/images/arrow_up.png">
  </div>
</div>

