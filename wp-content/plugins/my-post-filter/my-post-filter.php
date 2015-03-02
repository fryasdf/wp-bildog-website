<?php
/*
Plugin Name: my post filter
Plugin URI: http://myPostFilterURI.de
Description: Lists posts based on a php file and filters by category
Version: 0.1 BETA
Author: Fabian Werner
Author URI: http://happy-werner.de
*/

// is this script is called directly (without wordpress
// around it) then die
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//tell wordpress to register shortcode "[my-post-filter]"
add_shortcode("my-post-filter", "mpfil_shortcode_function");

DEFINE("MPFIL_DEFAULT_CATEGORY_NAME", "");
DEFINE("MPFIL_DEFAULT_POST_FILE_NAME", "");
DEFINE("MPFIL_DEFAULT_PAGINATION_SCRIPT", "");
DEFINE("MPFIL_DEFAULT_POSTS_PER_PAGE", 5);

// EXAMPLE:
// [my-post-filter category_name="Hamburg" posts_per_page=10 post_file_name="yeopress_content_post_brief"]
// will show all posts from the category "Hamburg"
// in the design as given in the file
// <template directory>/yeopress_content_post_brief.php
// i.e. a file of the form
//
// <li style="background-color:red"><?php  the_title() QUESTIONMARK></li>
//
// also, before this is done with every post, the file
//   yeopress_content_post_brief_before.php
// is executed and afterwards the file
//   yeopress_content_post_brief_after.php
// those may contain just <ul> and </ul> for example (or may be more 
// complicated)


function mpfil_shortcode_function($params_from_shortcode) {
  global $post;
  $paged = wp_specialchars_decode(get_query_var( 'paged', 1 ));

  // careful: wordpress automatically makes "categoryName" to "categoryname"
  // thats the reason for us to use underscore notation 
  $defaultArray = array(
    "category_name" => MPFIL_DEFAULT_CATEGORY_NAME,
    "post_file_name" => MPFIL_DEFAULT_POST_FILE_NAME,
    "pagination_script" => MPFIL_DEFAULT_PAGINATION_SCRIPT,
    "posts_per_page" => MPFIL_DEFAULT_POSTS_PER_PAGE
  );

  $params_from_shortcode=
    shortcode_atts(
      $defaultArray,
      $params_from_shortcode,
      "my-post-filter"
    );

  // for navigation:
  //  'posts_per_page' => 5,
  //  'paged' = $pageNumber
  $category_name = wp_specialchars_decode($params_from_shortcode['category_name']);
  $post_file_name = wp_specialchars_decode($params_from_shortcode['post_file_name']);
  $posts_per_page = wp_specialchars_decode($params_from_shortcode['posts_per_page']);
  $pagination_script = wp_specialchars_decode($params_from_shortcode['pagination_script']);


  // if post_file_name is something like 'filename.php' then
  // remove the trailing '.php' as wordpress
  // wants it to be like this
  $post_file_name = strip_trailing_dot_php($post_file_name);
  $pagination_script = strip_trailing_dot_php($pagination_script);
   //$output = "DOLLY....................";
  //$output .= "CAT NAME='" . $category_name . "'<br><br>\n\n";
  //$output .= "POST NAME='" . $post_file_name . "'<br><br>\n\n";
  $params = array(
      'post_type' => 'post',
      'posts_per_page' => $posts_per_page,
      'paged' => $paged,
      'category_name' => $category_name,
    );
  //$filtered_posts = new WP_Query( $params );
  //echo "DOLLY=" . have_posts();
  //echo "DOLLY........................";
  //print_r($filtered_posts);
  // The Query
  query_posts( $params );
  //echo "POST========" . $GLOBALS['wp_query']->request . "=============================<br><br>\n\n";
  $output = '';
  $output .= load_template_part($post_file_name . '_before');
  while ( have_posts() ) : the_post();
    // for each post: dont come up with a new design of how to print
    // out posts, use the same design as the template
    $output .= load_template_part($post_file_name);
  endwhile;
  $output .= load_template_part($post_file_name . '_after');

  // if specified, write a little navbar 
  // (newer posts) 1 2 3 ... (older posts)
  if ($pagination_script != "") {  
    $output .= load_template_part($pagination_script);
  }

  // Reset Query
  wp_reset_query();

  return $output;
}

// the function get_template_part echoes out immediately
// the result of the php file. Problem:
// if the page looks like
//
//  some content before
//  [shortcode]
//  some content afterwards
//
// then the result of the shortcode will appear ABOVE 
// 'some content before' which is not what we want.
// Instead, we are executing the php-part in the template
// file and store the resulting stuff in a variable.
// This is the purpose of this function.
function load_template_part($template_name) {
    ob_start();
    get_template_part($template_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

// if the given string $str ends in '.php' then stript this off
function strip_trailing_dot_php($str) {
  if (preg_match ('/.php\z/' , $str)) {
    return substr($str, 0, strlen($str) - 4);
  }
  return $str;
}
