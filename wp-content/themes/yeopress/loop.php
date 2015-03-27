<?php // BUG IN WORDPRESS??
//  echo 'wordpress filter: ---' . apply_filters( 'the_content', '<div style="background-color:red;">aaa' ) . '---';
// gives <div ...>aaa</p>  --> just </p>, without <p>?
?>

<?php if (is_page()): the_post() ?>
  <article id="page-<?php the_ID() ?>">
    <?php 

    // redirect the page with title (first entry) to
    // the usual header footer stuff but the contents replaced by
    // (second entry).php
    // for example:
    //  'AAAA' => 'BBBB',
    // makes the page 'AAAAA' execute 'BBBB.php' in the template directory
    $special_contents = array(
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
