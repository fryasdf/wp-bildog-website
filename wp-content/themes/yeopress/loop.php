<?php if (is_page()): the_post() ?>
  <article id="page-<?php the_ID() ?>">
    <?php 
    $content = get_the_content();
    // clean the content from my tags
    $content = strip_off_mytags($content);
    
    // the output of 'the_content()' and 'echo get_the_content();'
    // are different... -->
    // do whatever wordpress would do with the remaining content
    $content = apply_filters( 'the_content', $content );
    $content = str_replace( ']]>', ']]&gt;', $content );
 
    // display the content if it is not the page called 'Projekte'
    // which will be an index-like site for the actual projects
    if (get_the_title() != 'Projekte') {
      echo $content;
    } else {
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

          // show the page including...
          echo '<div class="col-md-4 projektbeschreibung-container">' . "\n";
          echo '<div class="projektbeschreibung">' . "\n";
          echo '<div class="header">' . "\n";
          // ... its icon ...
          echo '<center>' . "\n";
          echo '<img src="' . get_icon($page->post_title) 
                               . '" class="icon">';
          echo '</center>' . "\n";
          // ...its title...
          echo '<div class="titel">' . "\n";
          echo '<h1>' . $page->post_title . '</h1>' . "\n";
          echo '</div>' . "\n";
          echo '</div>' . "\n";
          
          echo '<div class="projekt-inhalt">' . "\n";

          // and the content of the page
          // precisely as wordpress would show it, i.e. including 
          // shortcodes, etc)
          $page_content = apply_filters( 'the_content', $page->post_content );
          $page_content = str_replace( ']]>', ']]&gt;', $page_content );
          echo $page_content;
          echo '</div>' . "\n";

          echo '</div>' . "\n";
          echo '</div>' . "\n";
        } 
        echo '</div>' . "\n";
      }
    }
    ?>
  </article>
<?php else: ?>
  <?php if (have_posts()):
    while (have_posts()) : the_post() ?>
      <?php get_template_part('yeopress_content_post'); ?>
    <?php endwhile; ?>
  <?php else: ?>
    <p>Nothing matches your query.</p>
  <?php  endif; ?>
<?php  endif; ?>
