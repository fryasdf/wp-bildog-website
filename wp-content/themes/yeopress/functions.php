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
    // call the parent start_el function
    // (this adds something to output)
    $outputBefore = $output;
    parent::start_el($output, $page, $depth, $args , $current_page );
    // extract the part that was added by the parent function
    $newPart = str_replace($outputBefore, "", $output);
    // search for the first occurrence of <a ...>SOMETHING</a>
    // and extract it, save the part left and right of it
    $titleAdded = self::getTextBetweenTags($newPart, 'a');

    $newPartSplittet = explode($titleAdded, $newPart);
    $newPartLeft = $newPartSplittet[0];
    $newPartRight = $newPartSplittet[1];

    $specialPageName = "Mitmachen";
    $resultTitle = "Spenden <br> + <br> Mitmachen";
    if ($titleAdded == $specialPageName) {
      $titleAdded = $resultTitle;
    }
    $toAppend = $newPartLeft . $titleAdded . $newPartRight;
    $output = $outputBefore . $toAppend;
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
