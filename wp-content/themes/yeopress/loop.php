<?php if (is_page()): the_post() ?>
  <article id="page-<?php the_ID() ?>">
    <?php 
    $special_contents = array(
      'Projekte' => 'content-Projekte',
    );

    $title = get_the_title();
    if (array_key_exists($title, $special_contents)) {
      get_template_part($special_contents[$title]); 
    } else {
      $content = get_the_content();
      // clean the content from my tags
      $content = strip_off_mytags($content);
    
      // the output of 'the_content()' and 'echo get_the_content();'
      // are different... -->
      // do whatever wordpress would do with the remaining content
      $content = apply_filters( 'the_content', $content );
      $content = str_replace( ']]>', ']]&gt;', $content );
      echo $content;
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
