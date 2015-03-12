<?php if (is_page()): the_post() ?>
  <article id="page-<?php the_ID() ?>">
    <?php 
    // redirect the page with title (first entry) to
    // the usual header footer stuff but the contents replaced by
    // (second entry).php
    $special_contents = array(
      'Projekte' => 'content-Projekte',
      'Team' => 'content-Team',
      'Hamburg' => 'content-Blog-Hamburg',
      'Berlin' => 'content-Blog-Berlin',
      'Afrika' => 'content-Blog-Afrika',
    );

    $title = get_the_title();
    if (array_key_exists($title, $special_contents)) {
      get_template_part($special_contents[$title]); 
    } else {
      $content = get_the_content();
      // the output of 'the_content()' and 'echo get_the_content();'
      // are different... -->
      // do whatever wordpress would do with the remaining content
      // also: strip off mytags
      echo prepare_content_as_wordpress_would_do($content);
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
