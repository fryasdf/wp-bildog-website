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

  // standard is '0'
  //echo "\n\n\n\n<strong>PAGED='$paged'</strong>\n\n\n\n";

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

  // count how many posts are in this category in total
  $params = array(
      'post_type' => 'post',
      'posts_per_page' => $posts_per_page,
      'paged' => $paged,
      'category_name' => $category_name,
    );
  $query = new WP_Query($params);
  $count_posts = $query->found_posts;

  // fetch the actual posts that are on the current pagination page
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
  $query = new WP_Query($params);
  // WP-documentation advices one not to use this function!
  //query_posts( $params );
  //echo "POST========" . $GLOBALS['wp_query']->request . "=============================<br><br>\n\n";
  $output = '';
  $output .= load_template_part($post_file_name . '_before');
  while ( $query->have_posts() ) {
    // for each post: dont come up with a new design of how to print
    // out posts, use the same design as the template
    $query->the_post();
    $output .= load_template_part($post_file_name);
  }
  $output .= load_template_part($post_file_name . '_after');

  // if specified, write a little navbar 
  // (newer posts) 1 2 3 ... (older posts)
  if ($pagination_script != "") {
    // wordpress is a little weird here:
    // the first page is $paged=0
    // but the second one is $paged=2
    // --> little gap between 0 and 2 :-)
    $real_human_page = 1;
    if ($paged != 0) {
      $real_human_page = $paged;
    }
    $GLOBALS['pagination_script_params'] = array(
      'posts_per_page' => $posts_per_page,
      'real_human_page' => $real_human_page,
      'count_posts' => $count_posts,
    );
    $output .= load_template_part($pagination_script);
  }

  // WP documentation: due to the fact that we are now using
  // the wp machinery correctly and cleanly (by creating a new
  // query and not messing with the main query which queries data for
  // the actual page were on) we must not do that
  // // Reset Query
  // //wp_reset_query();
  // but this:
  wp_reset_postdata();


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




//tell wordpress to register shortcode "[my-post-filter]"
add_shortcode("my-show-children", "my_show_children_shortcode_function");



// we obtain values from the wordpress shortcode but they are being treated 
// as strings, so when the user enters "false" as value for the variable 'test'
// and we test it using
//   if ($params['test]) {
//     ...
// then this will repeat TRUE, because test is not the empty string
// --> cast them to their real type

function cast_string_to_boolean($string) {
  if (preg_match("/true/i", $string)) {
    return TRUE;
  }
  if (preg_match("/false/i", $string)) {
    return FALSE;
  }
  return -1;
}


// SHORTCODE: my-show-children
//
// This shortcode goes through all the children of a specific page and
// shows them by applying a template file which must be specified by the user.
//
// EXAMPLE:
// [my-show-children parent_page_name="Team" template_filename="short-description-box.php" mytag_filter="ISTCOOL=ja"]
//
// This lists all the children of the page with title 'Team'. For every child,
// the file short-description-box.php is called with the following information:
// - $GLOBALS['my_show_children_params']
//   contains a key-value array of the parameters, see below
// - $GLOBALS["my_show_children_page"]
//   contains the current child
//
// Additionally, before the loop starts, '$template_filename'_before.php
// is called and afterwards, '$template_filename'_after.php is called.
// For example, the shortcode above would interact well with
//   short-description-box_before.php:
//   <ul>
//
//   short-description-box_after.php:
//   </ul>
//
/*   short-description-box.php:
     <?php
        $params = $GLOBALS['my_show_children_params'];
        $page = $GLOBALS["my_show_children_page"]
      ?>  
     <li> <?php $page->post_title ?> </li>
*/
// This would show all the team members which contain ISTCOOL{ja}
// somewhere in their page's content.
// CAREFUL: MYTAGS must be known in functions.php in the themes folder
     
  /* INPUT DATA:
   $box_content
     Type:    String
     Value:   One of {'content', 'short_tag', 'nothing'}
       'page' then in the box, the page content is displayed
       'short_tag' then this file looks in the page content for the string
                   $SHORT_DESCRIPTION_TAGNAME{blah}
                   and displays 'blah' instead of the page content
       'nothing' then no text is displayed below the header

   $box_content_default
     Type:    String
     Value:   arbitrary
       This is shown if $box_content was set to 'short_tag'
       and no short tag has been found in the pages content.
       If there wasnt any short tag giving a short description
       and this variable has not been set then an ERROR is printed.

   $header_pic
     Type:    String
     Value:   one of {'icon', 'featured_image', 'nothing'}
       'icon' then the icon as defined by get_icon in functions.php
              is shown
       'featured_image' then the featured image (dt: Beitragsbild)
              as defined in get_featured_image in functions.php is shown
       'nothing' then no header picture is shown

   $expandable (optional)
     Type:    Boolean
     Value:   If true then there is an arrow under each short description
              allowing the user to expand it if the content is too high
              for the box.
              If it is not set then it is treated as 'FALSE'.

   $link_to_page (optional)
     Type:  Boolean
     Value: If true then after each short description, a link
            to the actual page will be shown.

   $link_title (optional, must be set if $link_to_page is set) 
     Type:  String
     Value: This text will be shown as the links title, for example
            'read more'.

   $header_is_link_to_page (optional)
     Type:  Boolean
     Value: If true then the header area will be a link to the respective
            page of which we are showing the short description right now.

   $css_classes
     Type:  String
     Value: The final html-section will be
            <div class="row">
            and then for each item
            <div class="$css_classes short-description-box">
            ...

   $add_custom_css_class
     Type:  Boolean
     Value: If set then the final output will be
            <div class="row">
            and then for each object
            <div class="... short-description-box 
               short-description-box-{cleaned Title of parent}">
             

   $custom_css_class 
     Type:  String
     Value: If $add_custom_css_class is set then 
            short-description-box-$custom_css_class is appended
            as a css class.

   $mytag_filter 
     Type:  String
     Value: For example "ORT:CONTAINS:Berlin" then only those children are 
            shown that contain a mytag "ORT" in their page content
            and such that the string 'Berlin' is contained as a substring.
            See the function mytag_filter_function() below.


  */



// filter is supposed to be a string like
//   MYTAG1=VALUE1;MYTAG2=VALUE2;...
// this function return true if the content of the page
// contains all the mytags referenced and the value inside them
// coincide with the given values
//
//  for example:
//    $filter="LOCATION:EQUALS:Berlin";
//    $page->content = "blah blah blah LOCATION{Berlin} blah blah blah"
//  will return true while
//    $page->content = "blah blah blah LOCATION{Hamburg} blah blah blah"
//  and
//    $page->content = "blah blah blah blah blah blah"
//  will return false
//
//  modes currently implemented:
//   EQUALS
//   CONTAINS
//
// CAUTION: only the first occurrence of the mytag will be considered
function mytag_filter_function($page, $filter) {
  if($filter == "") {
    return TRUE;
  }
  $conditions = explode(';', $filter);
  $tags = array();
  $modes = array();
  $values = array();

  foreach ($conditions as $condition) {
    $tagValue = explode(":", $condition);
    array_push($tags, $tagValue[0]);
    array_push($modes, $tagValue[1]);
    array_push($values, $tagValue[2]);
  }
  if (sizeof($tags) != sizeof($values) || sizeof($tags) != sizeof($modes)) {
    trigger_error("<strong>mytag_filter_function(): sizes of tags and" . 
                  "values and or modes do not match</strong>", E_USER_ERROR);
  }
  $content = $page->post_content;
  //echo "modes=" . print_r($modes, TRUE) . "<br>\n";
  for($i = 0; $i < sizeof($tags); $i++) {
    if (!has_mytag($content, $tags[$i])) {
      return FALSE;
    }
    $mytag_value = get_mytag_contents($content, $tags[$i], TRUE);
    switch ($modes[$i]) {
      case "EQUALS":
        if ($mytag_value != $values[$i]) {
          return FALSE;
        }
        break;
      case "CONTAINS":
        if (!preg_match("/" . $values[$i] . "/", $mytag_value)) {
          return FALSE;
        }
        break;
      default:
        trigger_error("<strong>mytag_filter_function(): the given comparison " .
                       "mode '" . $modes[$i] . "' is not implemented.",
                  E_USER_ERROR);
    }
  }
  return TRUE;
}

function my_show_children_shortcode_function($params_from_shortcode) {
  // not implemented yet
  //$MY_SHOW_CHILDREN_DEFAULT_ENTRIES_PER_ROW = 3;
  $defaultArray = array(
    "parent_page_name" => NULL,
    "box_content" => "content",
    "box_content_default" => NULL, 
    "header_pic" => "nothing", 
    "expandable" => FALSE, 
    "link_to_page" => FALSE, 
    "link_title" => "more ...", 
    "header_is_link_to_page" => TRUE,
    "custom_css_class" => "WILL BE OVERWRITTEN IN ANY CASE, SEE BELOW",
    "css_classes" => NULL, 
    "add_custom_css_class" => TRUE, 
    "template_filename" => NULL,
    "mytag_filter" => "",
  );  

  $boolean_values = array(
    "expandable", 
    "link_to_page", 
    "header_is_link_to_page",
    "add_custom_css_class", 
   );


  
  // sanitize parameters to prevent MYSQL injections
  foreach ($params_from_shortcode as $key => $value) {
    $params_from_shortcode[$key] = wp_specialchars_decode($value);
  }

  // now that they are sanitized, we can use them securely
  $defaultArray['custom_css_class'] = 
    $params_from_shortcode['parent_page_name'];
  

  $keys_before = array_keys($params_from_shortcode);

  // I dont know what this function is supposed to do but it does not do it...
  $params =
    shortcode_atts(
      $defaultArray,
      $params_from_shortcode,
      "my-show-children"
  );


  foreach (array_keys($params) as $key) {
    // if the value was not specified by the user then insert its default value
    if (!in_array($key, $keys_before)) {
      $params[$key] = $defaultArray[$key];
    }
  }
   
  /*echo "DOLLY";
  print_r($params_from_shortcode);
  echo '<br><br>';
  print_r($defaultArray);
  echo '<br><br>';
  print_r($params);
  echo '<br><br><br><br>';
  return;*/


  // cast all the values that are of boolean type to boolean
  foreach($boolean_values as $key) {
    if (!is_bool($params[$key])) {
      $try = cast_string_to_boolean($params[$key]);
      if ($try === -1) {
        trigger_error("<strong>Tried to cast variable $key to boolean but i dont know what this value is supposed to be: " . $params[$key] . "</strong>", E_USER_ERROR);
      } else {
        $params[$key] = $try;
      }
    }
  }

  $parent = get_page_by_title($params['parent_page_name']);
  if ($parent==NULL) {
    trigger_error("<strong>Tried to load the page with title " . $params['parent_page_name'] . " but no such page was found...</strong>", E_USER_ERROR);
  }

  // if post_file_name is something like 'filename.php' then
  // remove the trailing '.php' as wordpress
  // wants it to be like this
  $params['template_filename'] = 
    strip_trailing_dot_php($params['template_filename']);

  $args = array(
    'child_of' => $parent->ID,
    'sort_column' => 'menu_order'
  );

  //$filtered_posts = new WP_Query( $params );
  //echo "DOLLY=" . have_posts();
  //echo "DOLLY........................";
  //print_r($filtered_posts);
  // The Query
  $GLOBALS["my_show_children_params"] = $params;
  $pages = get_pages($args); 


  $new_pages = array();
  for ($i = 0; $i < sizeof($pages); $i++) {
    // if the check fails then 'delete' the page from the array
    if (mytag_filter_function($pages[$i], $params['mytag_filter'])) {
      array_push($new_pages, $pages[$i]);
    }
  }
  $pages = $new_pages;


  $output = '';
  $output .= load_template_part($params['template_filename'] . '_before');
  for ($j=0; $j < sizeof($pages); $j++) {
    $GLOBALS["my_show_children_page"] = $pages[$j];
    // for each post: dont come up with a new design of how to print
    // out posts, use the same design as the template
    $output .= load_template_part($params['template_filename']);
    unset($GLOBALS["my_show_children_page"]);
  }
  $output .= load_template_part($params['template_filename'] . '_after');

  unset($GLOBALS["my_show_children_params"]);
  return $output;
}

