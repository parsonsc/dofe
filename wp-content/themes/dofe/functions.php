<?php
/*
Theme Name: DofE Diamond CHallenge
Author: David Gurney
Author URI: http://www.goodagency.co.uk
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/core.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
require_once( 'library/admin.php' );
/*
function remove_related_videos($embed) {
    error_log($embed);
	if (strstr($embed,'http://www.youtube.com/embed/')) {
		return str_replace('?fs=1','?fs=1&rel=0',$embed);
	} else {
		return $embed;
	}
}
add_filter('oembed_result', 'remove_related_videos', 1, true);
*/

function my_favicon() { ?>
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" />
<?php 
}
add_action('wp_head', 'my_favicon');

function strip_tags_content($text, $tags = '', $invert = FALSE) { 
  preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
  $tags = array_unique($tags[1]); 
  if(is_array($tags) AND count($tags) > 0) { 
    if($invert == FALSE) { 
      return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
    } 
    else { 
      return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
    } 
  } 
  elseif($invert == FALSE) { 
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
  } 
  return $text; 
} 

function core_ahoy() {

  // launching operation cleanup
  add_action( 'init', 'core_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'core_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'core_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'core_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'core_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'core_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  core_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'core_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'core_filter_ptags_on_images' );
  // cleaning up random code around iframes
  add_filter( 'the_content', 'core_filter_video_iframes' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'core_excerpt_more' );
} /* end ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'core_ahoy' );

/************* OEMBED SIZE OPTIONS *************/
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'core-thumb-600', 600, 150, true );
add_image_size( 'core-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 300 sized image,
we would use the function:
<?php the_post_thumbnail( 'core-thumb-300' ); ?>
for the 600 x 100 image:
<?php the_post_thumbnail( 'core-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'core_custom_image_sizes' );

function core_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'core-thumb-600' => __('600px by 150px'),
        'core-thumb-300' => __('300px by 100px'),
    ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function core_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'coretheme' ),
		'description' => __( 'The first (primary) sidebar.', 'coretheme' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));
	/*

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar1.php

	*/    
	register_sidebar(array(
		'id' => 'footer1',
		'name' => __( 'Pre Footer', 'coretheme' ),
		'description' => __( 'The prefooter.', 'coretheme' ),
        'class' => 'signup-footer',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));   
	register_sidebar(array(
		'id' => 'footer2',
		'name' => __( 'Footer', 'coretheme' ),
		'description' => __( 'The footer.', 'coretheme' ),
        'class' => 'footer',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));     
    register_sidebar(array(
		'id' => 'copyright',
		'name' => __( 'Copyright', 'coretheme' ),
		'description' => __( 'The copyright notice.', 'coretheme' ),
        'class' => '',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	)); 
    register_widget( 'core_widget' );
} // don't remove this bracket!

class core_widget extends WP_Widget {
    public function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'Core widget w. no container' );
	}    
    public function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
        echo $before_widget;
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
            <?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
        <?php
        echo $after_widget;
    }
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
        $instance['filter'] = ! empty( $new_instance['filter'] );
        return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
        $text = esc_textarea($instance['text']);
?>
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
 
        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}    
}
add_action( 'dynamic_sidebar_before', 'core_sidebar_before' );
function core_sidebar_before( $index ){
    if ( $index == 'copyright' ){
        echo '';
    }
}
add_action( 'dynamic_sidebar_after', 'core_sidebar_after' );
function core_sidebar_after( $index ){
    if ( $index == 'copyright' ){
        echo '<!-- Widgetized footer area -->';
    }
}

//add_action( 'widgets_init', function() {  });

add_action( 'dynamic_sidebar', 'core_sidebar' );
function core_sidebar( $obj ){
    //print_R($obj);
    //if ( $obj['callback'][0]->id_base == 'text' ){
    //    printf( '<span>%s</span>', __( 'So much meta', 'dademo' ) );
    //}
    // Alternatively
    // if ( preg_match( '/meta-/', $obj['id'] ) ){
    //  printf( '<span>%s</span>', __( 'So much meta', 'dademo' ) );
    // }
}
/************* COMMENT LAYOUT *********************/

// Comment Layout
function core_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'coretheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'coretheme' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'coretheme' )); ?> </a></time>

      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'coretheme' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php comment_text() ?>
      </section>
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!


/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
/*
function core_fonts() {
  wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic');
  wp_enqueue_style( 'googleFonts');
}
*/
//add_action('wp_print_styles', 'core_fonts');

function dofe_html_schema()
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

add_filter('next_posts_link_attributes', 'core_link_attributes');

function core_link_attributes() {
    return 'class="load_more"';
}

function html5autop($pee, $br = 1) {
   if ( trim($pee) === '' )
      return '';
   $pee = $pee . "\n"; // just to make things a little easier, pad the end
   $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
   // Space things out a little
    // *insertion* of section|article|aside|header|footer|hgroup|figure|details|figcaption|summary
   $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|header|footer|hgroup|figure|details|figcaption|summary)';
   $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
   $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
   $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
   if ( strpos($pee, '<object') !== false ) {
      $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
      $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
   }
   $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
// make paragraphs, including one at the end
   $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
   $pee = '';
   foreach ( $pees as $tinkle )
      $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
   $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
// *insertion* of section|article|aside
   $pee = preg_replace('!<p>([^<]+)</(div|address|form|section|article|aside)>!', "<p>$1</p></$2>", $pee);
   $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
   $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
   $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
   $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
   $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
   $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
   if ($br) {
      $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
      $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
      $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
   }
   $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
// *insertion* of img|figcaption|summary
   $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol|img|figcaption|summary)[^>]*>)!', '$1', $pee);
   if (strpos($pee, '<pre') !== false)
      $pee = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'clean_pre', $pee );
   $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

   return $pee;
}

// remove the original wpautop function
remove_filter('the_excerpt', 'wpautop');
remove_filter('the_content', 'wpautop');

// add our new html5autop function
add_filter('the_excerpt', 'html5autop');
add_filter('the_content', 'html5autop');

/* DON'T DELETE THIS CLOSING TAG */ ?>