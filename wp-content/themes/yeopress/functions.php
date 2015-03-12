<?php

add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
function theme_enqueue_scripts(){

	wp_register_script('modernizr', get_bloginfo('template_url') . '/js/modernizr.js');
	wp_enqueue_script('modernizr');

	wp_register_script('require', get_bloginfo('template_url') . '/js/vendor/requirejs/require.js', array(), false, true);
	wp_enqueue_script('require');

	wp_register_script('global', get_bloginfo('template_url') . '/js/global.js', array('require'), false, true);
	wp_enqueue_script('global');

	//wp_register_script('livereload', 'http://bildog.de:35729/livereload.js?snipver=1', null, false, true);
	//wp_enqueue_script('livereload');

	wp_enqueue_style('global', get_bloginfo('template_url') . '/css/global.css');
}

//Add Featured Image Support
add_theme_support('post-thumbnails');

// Clean up the <head>
function removeHeadLinks() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}
add_action('init', 'removeHeadLinks');
remove_action('wp_head', 'wp_generator');

function register_menus() {
	register_nav_menus(
		array(
			'main-nav' => 'Main Navigation',
			'secondary-nav' => 'Secondary Navigation',
			'sidebar-menu' => 'Sidebar Menu'
		)
	);
}
add_action( 'init', 'register_menus' );

function register_widgets(){

	register_sidebar( array(
		'name' => __( 'Sidebar' ),
		'id' => 'main-sidebar',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}//end register_widgets()
add_action( 'widgets_init', 'register_widgets' );

// Register Custom Navigation Walker
//require_once('wp_bootstrap_navwalker.php');

$defaults = array(
    'default-image'          => get_bloginfo('template_url') . '/images/bldg_bg_tile.png',
    'default-repeat'         => '',
    'default-position-x'     => '',
    'default-attachment'     => '',
    'wp-head-callback'       => '_custom_background_cb',
    'admin-head-callback'    => '',
    'admin-preview-callback' => ''
);
add_theme_support( 'custom-background', $defaults );

function getPageLinkByTitle($pageTitle) {
    $page = get_page_by_title($pageTitle);
    $pageLink = get_page_link($page->ID);
    return $pageLink;
}


class MyWalker extends Walker_Page {
  private $disabled = 0;
  /* if $string is
   *   ... <$tagname something uninteresting>TEXT</$tagname> ...
   * then this function gets
   *   TEXT
   * for the __first__ occurrence of <$tagname ...> ... </$tagname>
   */
  private function getTextBetweenTags($string, $tagname)
  {
    $pattern = "/<$tagname.*>(.*?)<\/$tagname>/";
    preg_match($pattern, $string, $matches);
    return $matches[1];
  }
  /* if $string is
   *   ... <$tagname someadditionalinfo>TEXT</$tagname> ...
   * then this function gets
   *   <$tagname someadditionalinfo>TEXT</$tagname>
   * for the __first__ occurrence of <$tagname ...> ... </$tagname>
   */
  private function getTagAndText($string, $tagname)
  {
    $pattern = "/<$tagname.*>.*<\/$tagname>/";
    preg_match($pattern, $string, $matches);
    return $matches[0];
  }

  /* is called whenever a <li> <a href=...>...</a> is added
   */
  public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
    if ($this->disabled != 0) {
      return;
    }
    // call the parent start_el function
    // (this adds something to output)
    $outputBefore = $output;
    parent::start_el($output, $page, $depth, $args , $current_page );
    // extract the part that was added by the parent function
    $newPart = str_replace($outputBefore, "", $output);

    // the complete subtree under 'unsichtbar' is invisible!
    if (preg_match('/\Aunsichtbar/', $page->post_title)) {
      //$output = $outputBefore . "CUTDOLLY" . $newPart;
      //$this->test=1;

      $this->disabled = 1;
      $output = $outputBefore;
      
      return;
    }
    //$newPart = str_replace("page_item_has_children", "page_item_has_children_depth_" . $depth, $newPart);
    
    //print "START_EL:" . "\n";
    //print "START_EL:depth=" . $depth . "\n";
    //print "START_EL: the following was added:" . $newPart . "\n";
    
    // search for the first occurrence of <a ...>SOMETHING</a>
    // and extract it, save the part left and right of it
    $titleAdded = self::getTextBetweenTags($newPart, 'a');

    $newPartSplittet = explode($titleAdded, $newPart);
    $newPartLeft = $newPartSplittet[0];
    $newPartRight = $newPartSplittet[1];

    // substitute $specialPageName by $resultTitle
    $specialPageName = "Mitmachen";
    $resultTitle = '<span id="spenden-mitmachen-one-line">Spenden + Mitmachen</span><span id="spenden-mitmachen-large">Spenden<br>+<br>Mitmachen</span>';
    if ($titleAdded == $specialPageName) {
      $titleAdded = $resultTitle;
    }
    // if it is the entry 'Hamburg' that is a direct child of 'Projekte'
    // then make it a little wider (widthout messing with the css of 
    // the navbar... brrr) in order for the child list to look nicer
    $specialPageName = "Hamburg";
    $resultTitle = "Hamburg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($titleAdded == $specialPageName && $page->post_parent === get_page_by_title('Projekte')->ID) {
      $titleAdded = $resultTitle;
    }

    $toAppend = $newPartLeft . $titleAdded . $newPartRight;
    $output = $outputBefore . $toAppend;
  }
  public function end_el( &$output, $page, $depth = 0, $args = array() ) {
    if ($depth === 0 && $this->disabled != 0) {
      $this->disabled = 0;
      return;
    }
    if ($this->disabled != 0) {
      return;
    }
    parent::end_el($output, $page, $depth, $args);
  }

  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    if ($this->disabled != 0) {
      return;
    }
    parent::start_lvl($output, $depth, $args);
  }
  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ($this->disabled != 0) {
      return;
    }
    parent::end_lvl($output, $depth, $args);  
  }
  
}


add_filter( 'wp_title', 'custom_title' );
// on the home page show 'bildog | description of home page'
// on the other pages show
// 'bildog | title of the page'
function custom_title( $title ) {
  if( empty( $title ) && ( is_home() || is_front_page() ) ) {
    return __( 'bildog', 'theme_domain' ) . ' | ' . get_bloginfo( 'description' );
  }
  return 'bildog |' . $title;
}



/*
 * MYTAGS SECTION
 *
 * A 'mytag' is a thing SOMETHINGINALLCAPITALS{some content}
 * which appears on some pages. This is an input-output machinery for
 * interacting with worpdress, essentially these functions provide methods
 * of getting the contents or the tag with contents, stripping them off
 * and showing them on other places.
 */
$SHORT_DESCRIPTION_TAGNAME = "KURZBESCHREIBUNG";
$TITLE_LOCALE_TAGNAME = "ORT";

$mytags = array($SHORT_DESCRIPTION_TAGNAME, $TITLE_LOCALE_TAGNAME);

function has_mytag($search_string, $tag) {
  return preg_match_all('/' . $tag . '{([^}]*)}/', $search_string, $hits);
}
function get_mytag_contents($search_string, $tag) {
  $res = preg_match_all('/' . $tag . '{([^}]*)}/', $search_string, $hits);
  if ($res === 1) {
    return $hits[1][0];
  }
  echo 'ERROR: get_mytag_contents(): this string (' . $search_string . 
                                     ') does not have such a tag (' . 
                                     $tag . ')';
  return 'ERROR: NO TAG AVAILABLE!!!';
}

function get_mytag_with_brackets_and_trailing_newlines($search_string, $tag) {
  $res = preg_match_all('/(' . $tag . '{[^}]*}[\n]*)/', $search_string, $hits);
  if ($res === 1) {
    return $hits[1][0];
  }
  echo 'ERROR: get_mytag_with_brackets_and_trailing_newlines():'; 
  echo '       this string does not have such a tag';
  return 'ERROR: NO TAG AVAILABLE!!!';
}

function strip_off_mytags($text) {
  global $mytags;
  foreach($mytags as $tag) {
    if (has_mytag($text, $tag)) {
      $tag_with_content = get_mytag_with_brackets_and_trailing_newlines($text, $tag);
      $text = str_replace($tag_with_content , "" , $text);
    }
  } 
  return $text;
}
function replace_mytag_with_content($text, $tag) {
  if (has_mytag($text, $tag)) {
    $tag_with_content = get_mytag_with_brackets_and_trailing_newlines($text, $tag);
    $only_content = get_mytag_contents($text, $tag);
    $text = str_replace($tag_with_content , $only_content , $text);
  }
  return $text;
}

// some pages contents start with '{SOME TEXT}'
// this is a special feature: it is a short description for
// an index-like page. This needs to be stripped off
// before showing the actual content
// these functions get the 'SOME TEXT'
function has_short_description($search_string) {
  global $SHORT_DESCRIPTION_TAGNAME;
  return has_mytag($search_string, $SHORT_DESCRIPTION_TAGNAME);
}
function get_short_description($search_string) {
  global $SHORT_DESCRIPTION_TAGNAME;
  return get_mytag_contents($search_string, $SHORT_DESCRIPTION_TAGNAME);
}
function get_short_description_with_enclosing_and_newlines($search_string) {
  global $SHORT_DESCRIPTION_TAGNAME;
  return get_mytag_with_brackets_and_trailing_newlines($search_string, $SHORT_DESCRIPTION_TAGNAME);
}

function get_locale_tag_title($title) {
  global $TITLE_LOCALE_TAGNAME;
  return get_mytag_contents($title, $TITLE_LOCALE_TAGNAME);
}



// for a general string messy_title...
//   1) mytags are removed
//   2) its is turned to lower case
//   3) german umlauts are substituted by ae, ue, oe, ss
//   4) blanks ' ' are replaced by '_'
//   5) all remaining chars (/&%$) are removed
function get_clean_title($messy_title) {
  $res = $messy_title;
  $res = strip_off_mytags($res);
  $res = strtolower($res);
  $res = str_replace("ä", "ae", $res);
  $res = str_replace("ö", "oe", $res);
  $res = str_replace("ü", "ue", $res);
  $res = str_replace("ß", "ss", $res);
  $res = str_replace(" ", "_", strtolower($res));
  $res = preg_replace("/[^A-Za-z0-9_]/", '', $res);
  return $res;
}

// get the associated icon to the page, i.e. the file located in
// TEMPLATE_DIRECTORY/images/featured-icons/{...}
// where {...} is either (the 'cleaned' page title).png
// (see above)
// or the default 'default.png' if the icon file does not exist
function get_icon($page_title) {
  $localDirectory = getcwd() . '/' . str_replace(get_bloginfo('url') . '/', '', get_bloginfo('template_directory')) . '/images/featured-icons/';
  $hostDirectory = get_bloginfo('template_directory') . '/images/featured-icons/';
  $defaultIconName = 'default.png';
  $iconName = get_clean_title($page_title) . '.png';
  if (file_exists($localDirectory . $iconName)) {
    return $hostDirectory . $iconName;
  } else {
    return $hostDirectory . $defaultIconName;
  }
}

// wordpress is a little weird when it comes to the blog page
function get_real_title() {
  if (is_home()) {
    return "Blog";
  }
  return get_the_title();
}

// gets the path to the featured image (dt: Beitragsbild)
// associated with the page
function get_featured_image($pageID) {
  if ($pageID == get_option('page_for_posts' )) {
    // its the blog page were on
    return get_bloginfo('template_directory') . '/images/head_blog.jpg';
  }
  if (has_post_thumbnail($pageID)) {
    return wp_get_attachment_url( get_post_thumbnail_id($pageID));
  } else {
    return get_bloginfo('template_directory') . 
                        '/images/head_projekte.jpg';
  }
  return "get_featured_image(): ERROR";
}
// returns the current page's id
function my_get_current_page_ID() {
  return get_queried_object_id();

  //global $wp;
  //return $wp->query_vars['page_id'];
}

// read the value of an scss variable from an scss file
  function read_scss_variable($file_name, $variable_name) {
    $file_content = file_get_contents($file_name);                             
    $res = preg_match_all('/\$' . $variable_name . '[\ ]*:[\ ]*([^;]*);/', $file_content, $hits);
    if ($res === 1) {
      return $hits[1][0];
    }
    return NULL;
  }
  function read_scss_variable_without_unit($file_name, $variable_name, $unit) {
    $value = read_scss_variable($file_name, $variable_name);
    $value = str_replace($unit, '', $value);
    return $value;
  }

function prepare_content_as_wordpress_would_do($content) {
  $content = apply_filters( 'the_content', $content );
  $content = str_replace( ']]>', ']]&gt;', $content );
  $content = strip_off_mytags($content);
  return $content;
}


