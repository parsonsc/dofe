<?php

function core_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
    
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'core_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'core_remove_wp_ver_css_js', 9999 );
    // Disable the emoji's
 	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    // kill off the pingback header
    add_filter( 'wp_headers', 'remove_x_pingback' );
} /* end head cleanup */


function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param    array  $plugins  
 * @return   array             Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

function rw_title( $title, $sep = ' : ', $seplocation ) {
  global $page, $paged;

  // Don't affect in feeds.
  if ( is_feed() ) return $title;

  if ( is_home() || is_front_page() ) $title = get_bloginfo( 'name' );
  else $title = get_bloginfo( 'name' ) . ' : ' . $title;


  // Add the blog description for the home/front page.
  /*
  $site_description = get_bloginfo( 'description', 'display' );

  if ( $site_description && ( is_home() || is_front_page() ) ) {
    $title .= " : {$site_description}";
  }
  */
  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " : " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} // end better title

// remove WP version from RSS
function core_rss_version() { return ''; }

// remove WP version from scripts
function core_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function core_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function core_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function core_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/
//Making jQuery Google API
function modify_jquery() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', ('//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'),array(), false , true);
		wp_enqueue_script('jquery');
		wp_deregister_script('jquery-ui');
        wp_enqueue_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
		wp_register_script('jquery-ui', ('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js'),array('jquery'), false , true);
		wp_enqueue_script('jquery-ui');        
	}
}
add_action('init', 'modify_jquery');


if ( !is_admin() ) :
/**
 * Hack to display fallback JavaScript *right* after jQuery loaded.
 */
function __jquery_fallback( $src, $handle = null )
{
    static $run_next = false;

    if ( $run_next ) {
        $local = get_template_directory_uri() . '/js/lib/jquery.min.js';
        $localui = get_template_directory_uri() . '/js/lib/jquery-ui.js';
        echo <<<JS
<script type="text/javascript">
/*//<![CDATA[*/
window.jQuery || document.write(unescape('%3Cscript type="text/javascript" src="$local" %3E%3C/script%3E'));
window.jQuery.ui || document.write(unescape('%3Cscript type="text/javascript" src="$localui" %3E%3C/script%3E'));
/*//]]>*/
</script>
JS;
        wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
        $run_next = false;
    }

    if ( $handle === 'jquery-ui' )
        $run_next = true;
    return $src;
}
    add_filter( 'script_loader_src', '__jquery_fallback', 10, 2 );
    add_action( 'wp_foot', '__jquery_fallback', 2 );
endif;

// loading modernizr and jquery, and reply script
function core_scripts_and_styles() {

    global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

    if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'modernizr', get_stylesheet_directory_uri() . '/js/lib/modernizr.js', array(), '2.8.3', false );

		// register main stylesheet
		wp_register_style( 'core-stylesheet', get_stylesheet_directory_uri() . '/css/dofe.css', array(), '', 'all' );
/*
		// ie-only style sheet
		wp_register_style( 'core-ie-only', get_stylesheet_directory_uri() . '/css/ie.css', array(), '' );

        // comment reply script for threaded comments
        if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
              wp_enqueue_script( 'comment-reply' );
        }
*/
		//adding scripts file in the footer
		wp_register_script( 'core-js', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ), '', true );
		wp_register_script( 'waypoint-js', get_stylesheet_directory_uri() . '/js/waypoints.js', array( 'jquery' ), '', true );

		// enqueue styles and scripts
		wp_enqueue_script( 'core-modernizr' );
		wp_enqueue_style( 'core-stylesheet' );
		//wp_enqueue_style( 'core-ie-only' );

		//$wp_styles->add_data( 'core-ie-only', 'conditional', 'IE' ); // add conditional wrapper around ie stylesheet

		/*
		I recommend using a plugin to call jQuery
		using the google cdn. That way it stays cached
		and your site will load faster.
		*/
		modify_jquery();
		wp_enqueue_script( 'core-js' );
		wp_enqueue_script( 'waypoint-js' );

	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function core_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
    /*
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);
    */
	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);

	// wp menus
	//add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => 'The Main Menu',   // main nav in header
			'footer-links' => 'Footer Links' // secondary nav in footer
         ) 
	);
} /* end theme support */


/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using core_related_posts(); )
function core_related_posts() {
	echo '<ul id="related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'coretheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} 

/*********************
PAGE NAVI
*********************/

function core_nav_menu($args){
	$args['echo'] = 0;
	//print_R($args);
	$current_url = get_permalink();
	$wpos = strpos($current_url, "work/");
	
	global $wp_query;
	//print_R($wp_query);
	//$tag_id = $wp_query->query_vars['term']; 

	$the_menu = wp_nav_menu( $args );
	$thisCat = get_category(get_query_var('cat'),false);
	//$the_menu = wp_list_pages($args );
	$the_menu = str_replace("current-menu-item","selected",$the_menu);
	$the_menu = str_replace('//"','/"',$the_menu);
	$the_menu = str_replace("current_page_item","selected",$the_menu);	
	$the_menu = str_replace("current-page-ancestor","selected",$the_menu);
	$pos = strpos($the_menu, "selected");
	/*
	if ($pos === false) {
		if ( 'post' == get_post_type() ) {
			$the_menu = str_replace("menu-item-26"," menu-item-26 selected",$the_menu);
		}
		elseif ( 'worksa' == get_post_type() ){	
			$the_menu = str_replace("menu-item-28","menu-item-28 selected",$the_menu);
		}
		elseif ( 'thinksa' == get_post_type() ){	
			$the_menu = str_replace("menu-item-8255","menu-item-8255 selected",$the_menu);
		}        
		elseif ( is_author() ){	
			$the_menu = str_replace("menu-item-8255","menu-item-8255 selected",$the_menu);
		}        
		elseif ( is_object($wp_query) && is_object($wp_query->queried_object) && $wp_query->queried_object->taxonomy == 'thinksac' ){	
			$the_menu = str_replace("menu-item-8255","menu-item-8255 selected",$the_menu);
		}	
	}
	*/
	$the_menu = preg_replace('/\s+id="[^"]*"/','',$the_menu);	
	$the_menu = str_replace("menu-item menu-item-type-custom menu-item-object-custom","",$the_menu);
	$the_menu = str_replace("current_page_item menu-item-home","",$the_menu);
	$the_menu = str_replace("menu-item menu-item-type-post_type menu-item-object-page","",$the_menu);
	//$the_menu = preg_replace("<li class=\"page([a-zA-Z0-9\-\_]+)\spage([a-zA-Z0-9\-\_]+)\scurrent_page_ancestor\scurrent_page_parent\">","li class=\"selected\"",$the_menu);
    //echo $the_menu;   
	$menu = preg_replace(array('#^<ul[^>]*>#', '#</ul>$#'), '', $the_menu);
	//$menu .= '</ul></div>'. "\n";  
	print $menu;  	
}

function default_page_menu() {
   wp_list_pages('title_li=');
} 

function core_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

function get_picture_from_yt($content){
	if (strpos($content, "youtu" ) !== false) {
		$content = preg_replace('~
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://         # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)? # Optional subdomain.
        (?:               # Group host alternatives.
          youtu\.be/      # Either youtu.be,
        | youtube         # or youtube.com or
          (?:-nocookie)?  # youtube-nocookie.com
          \.com           # followed by
          \S*             # Allow anything up to VIDEO_ID,
          [^\w\s-]        # but char before ID is non-ID char.
        )                 # End host alternatives.
        ([\w-]{11})       # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)      # Assert next char is non-ID or EOS.
        (?!               # Assert URL is not pre-linked.
          [?=&+%\w.-]*    # Allow URL (query) remainder.
          (?:             # Group pre-linked alternatives.
            [\'"][^<>]*>  # Either inside a start tag,
          | </a>          # or inside <a> element text contents.
          )               # End recognized pre-linked alts.
        )                 # End negative lookahead assertion.
        [?=&+%\w.-]*      # Consume any URL (query) remainder.
        ~ix', 
        'http://img.youtube.com/vi/$1/hqdefault.jpg',
        $content);
   		return $content;
    } else {
        return $content;
    }	
}


function core_filter_video_iframes($content){
	if (strpos($content, "<iframe" ) !== false) {
    	$search = array('<p><iframe', '</iframe></p>', '<div class="video">', '</div>');
		$replace = array('<iframe', '</iframe>', '', '');
		$content = str_replace($search, $replace, $content);
		preg_match('/src="([^"]+)"/', $content, $match);				
		$url = htmlspecialchars_decode($match[1]);
		$content = preg_replace('/(<iframe .*?\s*>)(<\/iframe>)/', $url, $content);
		$content = preg_replace('~
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://         # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)? # Optional subdomain.
        (?:               # Group host alternatives.
          youtu\.be/      # Either youtu.be,
        | youtube         # or youtube.com or
          (?:-nocookie)?  # youtube-nocookie.com
          \.com           # followed by
          \S*             # Allow anything up to VIDEO_ID,
          [^\w\s-]        # but char before ID is non-ID char.
        )                 # End host alternatives.
        ([\w-]{11})       # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)      # Assert next char is non-ID or EOS.
        (?!               # Assert URL is not pre-linked.
          [?=&+%\w.-]*    # Allow URL (query) remainder.
          (?:             # Group pre-linked alternatives.
            [\'"][^<>]*>  # Either inside a start tag,
          | </a>          # or inside <a> element text contents.
          )               # End recognized pre-linked alts.
        )                 # End negative lookahead assertion.
        [?=&+%\w.-]*      # Consume any URL (query) remainder.
        ~ix', 
        '<div class="video"><iframe  src="https://www.youtube.com/embed/$1?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe></div>',
        $content);
   		return $content;
    } else {
        return $content;
    }	
}

function core_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function core_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '&hellip;  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'coretheme' ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', 'coretheme' ) .'</a>';
}