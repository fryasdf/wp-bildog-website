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
  // = 1 if temporarily nothing is to be printed
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


  // like explode but
  // splitAtLastOccurrence('<li class="page_item page-item-2 page_item_has_children current_page_item"><a href="http://bildog.de/">bildog</a>', 'bildog')
  // returns an array of just 2 elements, not three (as explode), namely
  // array(
  //  [0] = '<li class="page_item page-item-2 page_item_has_children current_page_item"><a href="http://bildog.de/">'
  //  [1] = '</a>
  // )
  //
  // i.e. explode("AAAsssBBBsssCCC", "sss")
  // returns "AAA", "BBB", "CCC" while
  // splitAtLastOccurrence("AAAsssBBBsssCCC", "sss") rerturns just
  // "AAAsssBBB" and "CCC"
  // (actually static but there is some php version issue with the 1
  //  and 1 server which has some old version of php installed
  //  and static does not exist in this php version)
  private function splitAtLastOccurrence($needle, $string) {
    $res = explode($needle, $string);
    $lengthRes = count($res);
    if ($lengthRes > 2) {
      $resLeft = "";
      $resRight = $res[$lengthRes-1];
      for ($i = 0; $i < $lengthRes - 2; $i++) {
        $resLeft = $resLeft . $res[$i] . $needle;
      }
      $resLeft = $resLeft . $res[$lengthRes - 2];
      return array($resLeft, $resRight);
    } elseif ($lengthRes == 2) {
      return $res;
    }
    trigger_error("Error: needle was not found in string!");
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
      $this->disabled = 1;
      $output = $outputBefore;
      return;
    }
    
    // there is no mouseover, just a click
    // disable sublists for "Projekte" and "Blog"
    if (!user_has_mouse()) {
      if ($page->post_title == "Projekte" || $page->post_title == "Blog") {
        $newPart = str_replace("page_item_has_children", "page_item",  $newPart);
        $output = $outputBefore . $newPart;
        $this->disabled = 1;
        return;
      }
    }
    
    //print "START_EL:" . "\n";
    //print "START_EL:depth=" . $depth . "\n";
    //print "START_EL: the following was added:" . $newPart . "\n";
    
    // search for the first occurrence of <a ...>SOMETHING</a>
    // and extract it, save the part left and right of it
    $titleAdded = self::getTextBetweenTags($newPart, 'a');
    // careful: special case with the start page
    // the string 'bildog' actually occurs twice in
    // $newPart = '<li class="page_item page-item-2 page_item_has_children current_page_item"><a href="http://bildog.de/">bildog</a>'
    // -> dont explode, split at the last occurence
    $newPartSplittet = self::splitAtLastOccurrence($titleAdded, $newPart);
    $newPartLeft = $newPartSplittet[0];
    $newPartRight = $newPartSplittet[1];

    // substitute $specialPageName by $resultTitle
    $specialPageName = "Spenden + Mitmachen";
    $resultTitle = '<span id="spenden-mitmachen-one-line">Spenden + Mitmachen</span><span id="spenden-mitmachen-large">Spenden<br>+<br>Mitmachen</span>';
    if ($titleAdded == $specialPageName) {
      $titleAdded = $resultTitle;
    }
    // if it is the entry 'Hamburg' that is a direct child of 'Projekte'
    // then make it a little wider (widthout messing with the css of 
    // the navbar... brrr) in order for the child list to look nicer
    $specialPageName = "Hamburg";
    $resultTitle = "Hamburg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($titleAdded == $specialPageName) {
      // && $page->post_parent === get_page_by_title('Projekte')->ID) {
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
$NO_WORDPRESS_TAGNAME = "NO_WORDPRESS";

$mytags = array($SHORT_DESCRIPTION_TAGNAME, $TITLE_LOCALE_TAGNAME, $NO_WORDPRESS_TAGNAME);

function has_mytag($search_string, $tag) {
  return (preg_match_all('/' . $tag . '{([^}]*)}/', $search_string, $hits) > 0);
}

function get_mytag_contents($search_string, $tag, $only_return_first=FALSE) {
  $res = preg_match_all('/' . $tag . '{([^}]*)}/', $search_string, $hits);
  if ($res > 0) {
    if ($only_return_first == TRUE) {
      return $hits[1][0];
    } else {
      return $hits[1];
    }
  }
  echo 'ERROR: get_mytag_contents(): this string (' . $search_string . 
                                     ') does not have such a tag (' . 
                                     $tag . ')';
  return 'ERROR: NO TAG AVAILABLE!!!';
}

function get_mytag_with_brackets_and_trailing_newlines($search_string, $tag, $only_return_first=FALSE) {
  $res = preg_match_all('/(' . $tag . '{[^}]*}[\ ,\n]*)/', $search_string, $hits);
  if ($res > 0) {
    if ($only_return_first == TRUE) {
      return $hits[1][0];
    } else {
      return $hits[1];
    }
  }
  echo 'ERROR: get_mytag_with_brackets_and_trailing_newlines():'; 
  echo '       this string does not have such a tag';
  return 'ERROR: NO TAG AVAILABLE!!!';
}

function strip_off_mytags($text) {
  global $mytags;
  foreach($mytags as $tag) {
    if (has_mytag($text, $tag)) {
      $tags_with_contents = get_mytag_with_brackets_and_trailing_newlines($text, $tag);
      foreach($tags_with_contents as $tag_with_content) {
        $text = str_replace($tag_with_content , "" , $text);
      }
    }
  } 
  return $text;
}
function replace_mytag_with_content($text, $tag) {
  if (has_mytag($text, $tag)) {
    $tags_with_contents = get_mytag_with_brackets_and_trailing_newlines($text, $tag);
    $only_contents = get_mytag_contents($text, $tag);
    for($i=0;$i<sizeof($tags_with_contents);$i++) {
      $text = str_replace($tags_with_contents[$i] , $only_contents[$i] , $text);
    }
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
  return get_mytag_contents($search_string, $SHORT_DESCRIPTION_TAGNAME, TRUE);
}
function get_short_description_with_enclosing_and_newlines($search_string) {
  global $SHORT_DESCRIPTION_TAGNAME;
  return get_mytag_with_brackets_and_trailing_newlines($search_string, $SHORT_DESCRIPTION_TAGNAME, TRUE);
}

function get_locale_tag_title($title) {
  global $TITLE_LOCALE_TAGNAME;
  return get_mytag_contents($title, $TITLE_LOCALE_TAGNAME, TRUE);
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
function get_icon_without_png($page_title) {
  $localDirectory = get_stylesheet_directory() . "/images/featured-icons/";
  $hostDirectory = get_bloginfo('template_directory') . '/images/featured-icons/';
  $defaultIconName = 'default';
  $iconName = get_clean_title($page_title);
  if (file_exists($localDirectory . $iconName . ".png")) {
    return $hostDirectory . $iconName;
  } else {
    return $hostDirectory . $defaultIconName;
  }
}

function get_icon($page_title) {
  return get_icon_without_png($page_title) . ".png";
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


function byteStr2byteArray($s) {
    return array_slice(unpack("C*", "\0".$s), 1);
}


// conversion hex <-> decimal <-> strings
function hexArrayToDecArray($hexArray) {
  $decArray = array();
  foreach ($hexArray as $hexValue) {
    array_push($decArray, hexdec($hexValue));
  }
  return $decArray;
}

function hexArrayToString($hexArray) {
  $decArray = hexArrayToDecArray($hexArray);
  return decArrayToString($decArray);
}

function decArrayToString($decimalArray) {
  return implode(array_map("chr", $decimalArray));
}


function prepare_content_as_wordpress_would_do($content) {
  // before we strip off all tags, there is one special tag:
  // if there is a NO_WORDPRESS{...} in the content then we want to display
  // '...' without preprocessing by wordpress
  global $NO_WORDPRESS_TAGNAME;
  if (has_mytag($content, $NO_WORDPRESS_TAGNAME)) {
    $tags_with_brackets = get_mytag_with_brackets_and_trailing_newlines($content, $NO_WORDPRESS_TAGNAME);
    $tags_contents = get_mytag_contents($content, $NO_WORDPRESS_TAGNAME);
 
    // split content using the different TAG{...} as delimiters
    $reg_exp = "(";
    foreach($tags_with_brackets as $delimiter) {
      $reg_exp = $reg_exp . preg_quote($delimiter) . "|";
    }
    // delete the last "|"
    $reg_exp="/" . substr($reg_exp, 0, -1) . ")/";
    $res = preg_split($reg_exp, $content);
        

    // there is a little bug in wordpress filter function:
    // usually, it just inserts <p> ... </p> around some of the content
    // but it gets confused when there are html tags that are not properly 
    // closed... however, this is necessary sometimes:
    // for example, in this situation here we have
    // some content before NO_WORDPRESS{...} some content after
    // for example
    //
    // <div class="row">
    //   <div class="col-xs-5 col-md-3 col-lg-1 vcenter">
    //     <div style="height:10em;border:1px solid #000">Big</div>
    //   </div>NO_WORDPRESS{xyz}
    //   <div ....>
    //
    // gets filtered to
    //
    // <div class="row">
    //   <div class="col-xs-5 col-md-3 col-lg-1 vcenter">
    //     <div style="height:10em;border:1px solid #000">Big</div>
    //   </p></div>xyz
    //   <div ....>
    //
    // notice the </p>? This messes up the rest of the page TOTALLY
    //
    // solution for now: let wordpress filter the content but
    // disable the <p> </p>-insertion completely...
    remove_filter( 'the_content', 'wpautop' );
    remove_filter( 'the_excerpt', 'wpautop' );

    $final_content = "";
    for ($i=0; $i<sizeof($res); $i++) {
      // for debugging
      //echo "res[$i]='" . $res[$i] . "'\n";
      //echo "wordpress makes '" . apply_filters( 'the_content', $res[$i] ) . "' out of it\n";
      $final_content = $final_content . 
        apply_filters( 'the_content', $res[$i] );
      if ($i < sizeof($res)-1) {
        $final_content = $final_content . $tags_contents[$i];
      }
    }
    $content = $final_content;
  } else {
    $content = apply_filters( 'the_content', $content );
  }
  $content = str_replace( ']]>', ']]&gt;', $content );

  // there is an encoding issue here:
  // in firefox and linux console and some other devices,
  // one can represent the german umlauts in two different ways
  // 1) by their usual unicode representation
  // 2) by the usual letter (a,o,u,A,O,U) followed by a special
  //    unicode character (0xcc 0x88) that represents 'two dots'
  //    firefox actually MISinterprets a followed by 'two dots'
  //    as a Umlaut but other browsers do not do that
  //    --> substitute {a,o,u,A,U,O} + two dots by their normal
  //    unicode characters
  //
  // by the way: how do these strange letter + two dots things enter?
  // well, if someone edits a page in wordpress and pastes from a
  // different text editor then this can actually happen (and it did happen!)
  $aUmlaut = hexArrayToString(array("c3", "a4")); // unicode a umlaut
  $oUmlaut = hexArrayToString(array("c3", "b6")); // unicode o umlaut
  $uUmlaut = hexArrayToString(array("c3", "bc")); // unicode u umlaut

  $AUmlaut = hexArrayToString(array("c3","84")); // unicode A umlaut
  $OUmlaut = hexArrayToString(array("c3","96")); // unicode O umlaut
  $UUmlaut = hexArrayToString(array("c3","9c")); // unicode U umlaut

  // {a,o,u,A,O,U} and unicode letter 'two dots' (0xcc 0x88)
  $aUmlautStrange = hexArrayToString(array("61", "cc", "88"));
  $oUmlautStrange = hexArrayToString(array("6f", "cc", "88"));
  $uUmlautStrange = hexArrayToString(array("75", "cc", "88")); 

  $AUmlautStrange = hexArrayToString(array("41", "cc", "88"));
  $OUmlautStrange = hexArrayToString(array("4f", "cc", "88"));
  $UUmlautStrange = hexArrayToString(array("55", "cc", "88"));

  // replace the strange letter + two dots combination by the usual unicode
  $content = str_replace($aUmlautStrange, $aUmlaut, $content);
  $content = str_replace($oUmlautStrange, $oUmlaut, $content);
  $content = str_replace($uUmlautStrange, $uUmlaut, $content);

  $content = str_replace($AUmlautStrange, $AUmlaut, $content);
  $content = str_replace($OUmlautStrange, $OUmlaut, $content);
  $content = str_replace($UUmlautStrange, $UUmlaut, $content);

  // some debug code for the encoding issue
  /*
  $letter_before = substr($content, 784, 1);
  $selection = substr($content, 785, 2);
  $letter_after = substr($content, 787, 1);
  
  echo 'STRLEN(FIRST)=' . strlen($letter_before) . "\n\n<br><br>";
  echo 'STRLEN(SELECTION)=' . strlen($selection) . "\n\n<br><br>";
  echo 'STRLEN(AFTER)=' . strlen($letter_after) . "\n\n<br><br>";
  //echo 'selection[0]=' . $selection[0] . "\n\n<br><br>";
  //echo 'selection[1]=' . $selection[1] . "\n\n<br><br>";
  //echo 'selection[2]=' . $selection[2] . "\n\n<br><br>";

  $byteArray = unpack("C*", $selection);
  
  for ($i=1; $i <= sizeof($byteArray); $i++) {
    echo "byteArray[$i] = " . dechex($byteArray[$i]) . "<br>\n";
  }

  echo 'ENCODING="' . mb_detect_encoding ( $content) . '"<br>' . "\n\n";
  echo 'DOLLY BEFORE="' . $letter_before . '"|SELECTION = "' . $selection . '" AFTER="' . $letter_after . '" ---' . "<br><br>\n\n";
  */

  $content = strip_off_mytags($content);
  return $content;
}

// this adds the images caption after the image is shown in the gallery
add_filter('envira_gallery_output_after_image', 'envira_gallery_add_image_caption',10,5);
function envira_gallery_add_image_caption( $output, $id, $item, $data, $i) {
  $toAppend = "";
  // DEBUG
  //$toAppend = $toAppend . "DATAATT=" . print_r(wp_prepare_attachment_for_js( $id ), TRUE) . "\n";
  //$toAppend = $toAppend . "ID=" . print_r($id, TRUE) . "\n";
  //$toAppend = $toAppend . "ITEM=" . print_r($item, TRUE) . "\n";
  //$toAppend = $toAppend . "DATA=" . print_r($data, TRUE) . "\n";
  //$toAppend = $toAppend . "i=" . print_r($i, TRUE) . "\n";



  // $description = wp_prepare_attachment_for_js($id)['description'];
  // $alt = wp_prepare_attachment_for_js($id)['alt'];
  
  $caption = wp_prepare_attachment_for_js($id);
  $caption = $caption['caption'];
  $toAppend = "<center>$caption</center>";
  return $output . $toAppend;
}

function my_error($file, $text) {
  echo '<h1>' . $file . ": ERROR: " . $text . "</h1>\n\n";
}
function is_boolean($variable) {
  if ($variable == TRUE || $variable == FALSE) {
    return TRUE;
  }
  return FALSE;
}

// is supposed to check whether the user has a pointer device with which
// a mouseover event is possible
// dirty user agent sniffing... but... can you show me a better method?
// --> send it to fab_wer@web.de
function user_has_mouse() {
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  $user_agent_beginning = substr($user_agent, 0, 4);
  $res = preg_match("/android|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(ad|hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino|playbook|silk/i", $user_agent);

  $res_beginning = preg_match("/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i", $user_agent_beginning);
  if ($res || $res_beginning) {
    return FALSE;
  } else {
    return TRUE;
  } 
}

// turn http://hostname/path/to/file.xyz
// into /home/user/path_to_wordpress_installation/path/to/file.xyz
function getFullPath($url){
  return str_replace(get_bloginfo('url') . "/", get_home_path(), $url);
}


// If the user adds a png image with a filename beginning with "ehrenamtliche_"
// or "team_"
// then a mask is applied (making the borders of the image 'smoothly'
// transparent) and the image is copied and registered as a wordpress attachment
// with a filename_masked.png in the same folder
//   maskfile: theme_directory/images/mask.png
function mask_and_copy($post_ID) {
  // get the path and directory
  $url = wp_get_attachment_image_src( $post_ID, 'large' );
  $url = $url[0];
  $filename_complete = getFullPath($url);
  $dir = pathinfo($filename_complete);
  $dir = $dir['dirname'] . "/";
  $filename = pathinfo($filename_complete);
  $filename = $filename['filename'];
  $extension = pathinfo($filename_complete);
  $extension = $extension['extension'];

  $attachment_post = get_post( $post_ID );
  $type = get_post_mime_type($post_ID);



  // only if the filename starts with ehrenamtliche_ and its a png...
  // (if its a jpg then we cant apply transparency because jpg
  // does not know this concept!)
  if ($type == "image/png" && 
       (preg_match('/\Aehrenamtliche_/', $filename) || 
        preg_match('/\Ateam_/', $filename))
     ) {
    $source = imagecreatefrompng($filename_complete);
 
    // /home/.../ 
    $localDirectory = get_stylesheet_directory() . "/images/";
    // http://host/,,,
    $hostDirectory = get_bloginfo('template_directory') . '/images/';

    // load masking image
    $mask = imagecreatefrompng( $localDirectory . 'mask.png' );

    // Apply mask to source
    imagealphamask( $source, $mask );

    $newFilenameComplete =  $dir . $filename . "_masked" . "." . $extension;

    // Output
    imagepng( $source, $newFilenameComplete );

    // register this image with wordpress so it appears in the media section

    // Check the type of file. We'll use this as the 'post_mime_type'.
    $filetype = wp_check_filetype( basename( $newFilenameComplete ), null );

    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();

    // Prepare an array of post data for the attachment.
    $attachment = array(
	'guid'           => $wp_upload_dir['url'] . '/' . basename( $newFilenameComplete ), 
	'post_mime_type' => $filetype['type'],
	'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $newFilenameComplete ) ),
	'post_content'   => '',
	'post_status'    => 'inherit'
    );

    // remove the hook, otherwise there will be a cycle:
    // the user inserts some new image
    // this hook is fired
    // this hook triggers th registration of a new image
    // this hook is fired
    // ... 
    remove_filter('add_attachment', 'mask_and_copy');


    // Insert the attachment.
    $attach_id = wp_insert_attachment( $attachment, $newFilenameComplete);
 
  // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Generate the metadata for the attachment, and update the database record.
    $attach_data = wp_generate_attachment_metadata( $attach_id, $newFilenameComplete );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    add_filter('add_attachment', 'mask_and_copy');

  }
  return $post_ID;
}

// see the function
add_filter('add_attachment', 'mask_and_copy');

// applies a mask:
//  mask contains a png image
//  picture is a reference to the image to be masked
//
// the mask is applied as follows: for each pixel,
// the alpha value of the mask pixel (i.e. the amount of non-transparency)
// is subtracted from the non-transparency of the picture pixel,
// hence, making it more transparent the thicker the color is in the mask
// ACTUALLY, THE COLOR ITSELF OF THE MASK PIXEL DOES NOT MATTER, ONLY ITS
// TRANSPARENCY VALUE IS USED!!!
function imagealphamask( &$picture, $mask ) {
    // Get sizes and set up new picture
    $xSize = imagesx( $picture );
    $ySize = imagesy( $picture );
    $newPicture = imagecreatetruecolor( $xSize, $ySize );
    imagesavealpha( $newPicture, true );
    imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

    // Resize mask if necessary
    if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
        $tempPic = imagecreatetruecolor( $xSize, $ySize );
        imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
        imagedestroy( $mask );
        $mask = $tempPic;
    }

    // Perform pixel-based alpha map application
    for( $x = 0; $x < $xSize; $x++ ) {
        for( $y = 0; $y < $ySize; $y++ ) {
            $maskPixel = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
            $imagePixel = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
            //file_put_contents("/home/fabi/test.txt", "x=$x|y=$y|maskPixel=" . print_r($maskPixel, TRUE) . "\n" . "imagePixel=" . print_r($imagePixel, TRUE), FILE_APPEND);
            // how much more transparent do we want to make the pixel?
            // pixel['alpha'] gives a value between 0 and 127
            // 0 means: not transparent
            // 127 means: absolutely transparent
            $transparencyAdd = 127 - $maskPixel["alpha"];
            $newTransparency = $imagePixel["alpha"] + $transparencyAdd;
            if ($newTransparency < 0) {
              $newTransparency = 0;
            }
            if ($newTransparency > 127) {
              $newTransparency = 127;
            }
            $color = 
              imagecolorsforindex( 
                $picture, 
                imagecolorat( $picture, $x, $y ) );
            imagesetpixel( $newPicture, $x, $y, 
              imagecolorallocatealpha( 
                $newPicture, 
                $color[ 'red' ], 
                $color[ 'green' ], 
                $color[ 'blue' ], 
                $newTransparency ) );
        }
    }

    // Copy back to original picture
    imagedestroy( $picture );
    $picture = $newPicture;
}

