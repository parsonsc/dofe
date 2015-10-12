<?php
/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function h2only_head_cleanup() {
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
	add_filter( 'style_loader_src', 'h2only_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'h2only_remove_wp_ver_css_js', 9999 );
    

} 


function h2only_title( $title, $sep = ' : ', $seplocation ) {
  global $page, $paged;

  // Don't affect in feeds.
  if ( is_feed() ) return $title;

  if ( is_home() || is_front_page() ) $title = get_bloginfo( 'name' );
  else $title = get_bloginfo( 'name' ) . ' : ' . $title;

  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " : " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} // end better title

// remove WP version from RSS
function h2only_rss_version() { return ''; }

// remove WP version from scripts
function h2only_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function h2only_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function h2only_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function h2only_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/
//Making jQuery Google API
function modify_jquery() {
	if (!is_admin() && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', ('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'), false, null, true);
		wp_enqueue_script('jquery');
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
        $local = get_template_directory_uri() . '/js/jquery-1.11.0.min.js';
        echo <<<JS
<script type="text/javascript">/*//<![CDATA[*/window.jQuery || document.write('<script type="text/javascript" src="$local"><\/script>');/*//]]>*/</script>

JS;
        $run_next = false;
    }

    if ( $handle === 'jquery' )
        $run_next = true;
    return $src;
}
    add_filter( 'script_loader_src', '__jquery_fallback', 10, 2 );
    add_action( 'wp_foot', '__jquery_fallback', 2 );
endif;

// loading modernizr and jquery, and reply script
function h2only_scripts_and_styles() {

    global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
    global $wp_scripts;
    if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'modernizr', get_stylesheet_directory_uri() . '/js/modernizr-2.6.2.min.js', array(), '2.6.2', false );
        
		// ie-only style sheet
		wp_register_style( 'h2only-css', get_stylesheet_directory_uri() . '/css/h2only.css', array(), '' );
		wp_register_style( 'h2only-ie-only', get_stylesheet_directory_uri() . '/css/ie.css', array(), '' );
        // comment reply script for threaded comments
        if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
        //      wp_enqueue_script( 'comment-reply' );
        }
        
        add_action( 'wp_head', create_function( '','echo '."\t\n\t".' \'<!--[if lt IE 9]>'."\n\t".'<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>'. "\n\t" .'<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>'."\n\t".'<![endif]-->\';') );        

		//adding scripts file in the footer
		wp_register_script( 'h2only-js', get_stylesheet_directory_uri() . '/js/main.js', false, '', true );
       	wp_register_script( 'h2only-plugins', get_stylesheet_directory_uri() . '/js/plugins.js', false, '', true );
        
        wp_register_script( 'h2only-sc1', get_stylesheet_directory_uri() . '/js/plug/jquery.meanmenu.2.0.min.js', false, '', true ); 
        wp_register_script( 'h2only-sc2', get_stylesheet_directory_uri() . '/js/plug/jquery.meanmenu.js', false, '', true );
        wp_register_script( 'h2only-sc3', get_stylesheet_directory_uri() . '/js/plug/jquery.postcodes.js', false, '', true );
        wp_register_script( 'h2only-sc4', get_stylesheet_directory_uri() . '/js/plug/jquery.ui.touch-punch.min.js', false, '', true );  
        wp_register_script( 'h2only-sc5', get_stylesheet_directory_uri() . '/js/plug/jquery.validate.min.js', false, '', true );
        wp_register_script( 'h2only-sc6', get_stylesheet_directory_uri() . '/js/plug/jquery-easing.1.3.js', false, '', true ); 
        wp_register_script( 'h2only-sc7', get_stylesheet_directory_uri() . '/js/plug/jquery-ui-1.10.3.custom.min.js', false, '', true ); 
        wp_register_script( 'h2only-sc8', get_stylesheet_directory_uri() . '/js/plug/placeholders.min.js', false, '', true );
        wp_register_script( 'h2only-sc9', get_stylesheet_directory_uri() . '/js/lib/css3-mediaqueries.js', false, '', true );   
        wp_register_script( 'h2only-sca', get_stylesheet_directory_uri() . '/js/lib/jquery.nouislider.min.js', false, '', true );   
        wp_register_script( 'h2only-scb', get_stylesheet_directory_uri() . '/js/lib/selectivizr-1.0.2.min.js', false, '', true );
        wp_register_script( 'h2only-scc', get_stylesheet_directory_uri() . '/js/plug/additional-methods.min.js', false, '', true );

		// enqueue styles and scripts
		wp_enqueue_script( 'modernizr' );
        wp_enqueue_style( 'h2only-css' );
        wp_enqueue_style( 'h2only-ie-only' );
        $wp_styles->add_data( 'h2only-ie-only', 'conditional', 'lt IE 9' );

		modify_jquery();
		wp_enqueue_script( 'h2only-plugins' );
		wp_enqueue_script( 'h2only-sc1' );
		wp_enqueue_script( 'h2only-sc2' );
		wp_enqueue_script( 'h2only-sc3' );
		wp_enqueue_script( 'h2only-sc3' );
		wp_enqueue_script( 'h2only-sc4' );
		wp_enqueue_script( 'h2only-sc5' );
		wp_enqueue_script( 'h2only-sc6' );
		wp_enqueue_script( 'h2only-sc7' );
		wp_enqueue_script( 'h2only-sc8' );
		wp_enqueue_script( 'h2only-sc9' );
		wp_enqueue_script( 'h2only-sca' );
		wp_enqueue_script( 'h2only-scb' );
		wp_enqueue_script( 'h2only-scc' );
		wp_enqueue_script( 'h2only-js' );
	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function h2only_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);

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
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'h2onlytheme' ),   // main nav in header
			'mobile-nav' => __( 'The Mobile Menu', 'h2onlytheme' ),   // mobile nav in header
			'footer-links' => __( 'Footer Links', 'h2onlytheme' ) // secondary nav in footer
		)
	);
} 


/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using h2only_related_posts(); )
function h2only_related_posts() {
	echo '<ul id="h2only-related-posts">';
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
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'h2onlytheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} 

/*********************
PAGE NAVI
*********************/

function h2only_nav_menu($args, $cappo = true){
	$args['echo'] = 0;
	
	$current_url = get_permalink();
	$wpos = strpos($current_url, "work/");
	
	global $wp_query;

	
	$the_menu = wp_nav_menu( $args );
	$thisCat = get_category(get_query_var('cat'),false);
	//$the_menu = wp_list_pages($args );
	$the_menu = str_replace("current-menu-item","selected",$the_menu);
	$the_menu = str_replace('//"','/"',$the_menu);
	$the_menu = str_replace("current_page_item","selected",$the_menu);	
	$the_menu = str_replace("current-page-ancestor","selected",$the_menu);
	$pos = strpos($the_menu, "selected");

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
		elseif ( $wp_query->queried_object->taxonomy == 'thinksac' ){	
			$the_menu = str_replace("menu-item-8255","menu-item-8255 selected",$the_menu);
		} 	
	}
	
	$the_menu = preg_replace('/\s+id="[^"]*"/','',$the_menu);	
	$the_menu = str_replace("menu-item menu-item-type-custom menu-item-object-custom","",$the_menu);
	$the_menu = str_replace("current_page_item menu-item-home","",$the_menu);
	$the_menu = str_replace("menu-item menu-item-type-post_type menu-item-object-page","",$the_menu);
	//$the_menu = preg_replace("<li class=\"page([a-zA-Z0-9\-\_]+)\spage([a-zA-Z0-9\-\_]+)\scurrent_page_ancestor\scurrent_page_parent\">","li class=\"selected\"",$the_menu);
	$menu = $the_menu; 
    if (!$cappo) $menu  = strip_tags($menu, '<li><a>');
	//$menu .= '</ul></div>'. "\n";  
	print $menu;  	
}

// Numeric Page Navi (built into the theme by default)
function h2only_page_navi() {
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

// This removes the width and height on images
function h2only_image_downsize( $value = false, $id, $size ) {
    if ( !wp_attachment_is_image($id) )
        return false;

    $img_url = wp_get_attachment_url($id);
    $is_intermediate = false;
    $img_url_basename = wp_basename($img_url);

    // try for a new style intermediate size
    if ( $intermediate = image_get_intermediate_size($id, $size) ) {
        $img_url = str_replace($img_url_basename, $intermediate['file'], $img_url);
        $is_intermediate = true;
    }
    elseif ( $size == 'thumbnail' ) {
        // Fall back to the old thumbnail
        if ( ($thumb_file = wp_get_attachment_thumb_file($id)) && $info = getimagesize($thumb_file) ) {
            $img_url = str_replace($img_url_basename, wp_basename($thumb_file), $img_url);
            $is_intermediate = true;
        }
    }

    // We have the actual image size, but might need to further constrain it if content_width is narrower
    if ( $img_url) {
        return array( $img_url, 0, 0, $is_intermediate );
    }
    return false;
}
add_filter( 'image_downsize', 'h2only_image_downsize', 1, 3 );

function h2only_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

function image_tag($html, $id, $alt, $title) {
	return preg_replace(array(
			'/s+width="d+"/i',
			'/s+height="d+"/i',
			'/alt=""/i'
		),
		array(
			'',
			'',
			'alt="' . $title . '"'
		),
		$html);
}
add_filter('get_image_tag', 'image_tag', 0, 4);

// This removes the annoying […] to a Read More link
function h2only_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '';
	//return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'h2onlytheme' ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', 'h2onlytheme' ) .'</a>';
}


function the_excerpt_max_charlength($charlength) {
	$excerpt = get_the_excerpt();
	$charlength++;
	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			echo mb_substr( $subex, 0, $excut );
		} else {
			echo $subex;
		}
		echo '&hellip;';
	} else {
		echo $excerpt;
	}
}
function h2only_html_schema()
{
	$base = 'http://schema.org/';
	if( is_page( 23 /* type in the ID of your contact page here, 5 is an example */ ) )
	{
		$type = 'ContactPage';
	}
	elseif( is_page( 13 /* type in the ID of your about page here, 5 is an example */ ) )
	{
		$type = 'AboutPage';
	}
	elseif( is_singular( array( 'job', 'movie' ) /* add custom post types that describe a single item to this array */ )  )
	{
		$type = 'ItemPage';
	}
	elseif( is_author() )
	{
		$type = 'ProfilePage';
	}
	elseif( is_search() )
	{
		$type = 'SearchResultsPage';
	}
	else
	{
		$type = 'WebPage';
	}
	echo 'itemscope="itemscope" itemtype="' . $base . $type . '"';
}
add_filter('single_template', create_function('$the_template',
	'foreach( (array) get_the_category() as $cat ) {
		if ( file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php") )
		return TEMPLATEPATH . "/single-{$cat->slug}.php"; }
	return $the_template;' )
);
add_filter('next_posts_link_attributes', 'posts_link_attributes');

function posts_link_attributes(){
   return 'class="next"';

}
?>
