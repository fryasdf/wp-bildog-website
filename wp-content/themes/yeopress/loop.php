<?php if (is_page()): the_post() ?>
  <article id="page-<?php the_ID() ?>">
    <?php 
    $content = get_the_content();
    // if this page is a child of the projects site than
    // strip the first '{short_description}' part
    // (including possible linebreaks that would
    // [wrongly!] be interpreted as <br>'s by wordpress)
    // off from the content before displaying it
    global $post;
    if (empty($post->post_parent) != 1) {
      if ( get_post($post->post_parent)->post_title === 'Projekte') {
        if (has_short_description($content) != 0) {
          $short_description = get_short_description_with_enclosing_and_newlines($content);
          $content = str_replace($short_description , "" , $content);
        }
      }
    }

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
   

      $projects_page = get_page_by_title( 'Projekte' );
      $id_of_projects_page = $projects_page->ID;

      // get all the child pages of the site called 'Projekte'
      $args = array(
        'child_of' => $id_of_projects_page
      ); 
      $pages = get_pages($args); 
      $amount_pages = sizeof($pages);

      // list them in a neat way 
      echo 'DOLLY:<ul>';
      foreach ( $pages as $page ) {
        echo '<li>';
        echo 'CLEANTITLE=' . get_clean_title($page->post_title);
        echo 'TITLE=' . $page->post_title;
        if (has_short_description($page->post_content)) {
            echo 'SHORT DESCR=' . get_short_description($page->post_content);
        }
        echo 'ICON=' . get_icon($page->post_title);
        echo '</li>';
      }
      echo '</ul>';
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
