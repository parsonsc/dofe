<?php
/*
Author: David Gurney
URL: htp://www.goodagency.co.uk/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, ect.
*/

require_once( 'library/h2only.php' );

// USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
require_once( 'library/custom-post-type.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
// require_once( 'library/admin.php' );

/*********************
LAUNCH goodagency
Let's get everything up and running.
*********************/

function h2only_ahoy() {

  // launching operation cleanup
  add_action( 'init', 'h2only_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'h2only_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'h2only_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'h2only_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'h2only_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'h2only_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'h2only_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  h2only_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'h2only_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'h2only_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'h2only_excerpt_more' );
  if (class_exists('MultiPostThumbnails')) {
    new MultiPostThumbnails(
        array(
            'label' => 'Listing image',
            'id' => 'list-image',
            'post_type' => 'post'
        )
    );
    new MultiPostThumbnails(
        array(
            'label' => 'Listing image',
            'id' => 'list-image',
            'post_type' => 'page'
        )
    ); 
    new MultiPostThumbnails(
        array(
            'label' => 'Mobile Listing image',
            'id' => 'mlist-image',
            'post_type' => 'page'
        )
    ); 
  }
} 

// let's get this party started
add_action( 'after_setup_theme', 'h2only_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'h2only-thumb-600', 600, 150, true );
add_image_size( 'h2only-thumb-300', 300, 100, true );
add_image_size( 'h2only-news', 300);


add_filter( 'image_size_names_choose', 'h2only_custom_image_sizes' );

function h2only_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'h2only-thumb-600' => __('600px by 150px'),
        'h2only-thumb-300' => __('300px by 100px'),
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
function h2only_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'h2onlytheme' ),
		'description' => __( 'The first (primary) sidebar.', 'h2onlytheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

    
    // Area 1, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'h2onlytheme' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'h2onlytheme' ),
		'before_widget' => '<section class="footer_box contact_details">',
		'after_widget' => '</section>',
		'before_title' => '',
		'after_title' => '',
	) );   
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'h2onlytheme' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'h2onlytheme' ),
		'before_widget' => '<section class="footer_box address">',
		'after_widget' => '</section>',
		'before_title' => '',
		'after_title' => '',
	) );
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'h2onlytheme' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'h2onlytheme' ),
		'before_widget' => '<section class="footer_box footer_social">',
		'after_widget' => '</section>',
		'before_title' => '',
		'after_title' => '',
	) );    
	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'h2onlytheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'h2onlytheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function h2only_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; 
   
} // don't remove this bracket!

/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
/*
function h2only_fonts() {
  wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic');
  wp_enqueue_style( 'googleFonts');
}

add_action('wp_print_styles', 'h2only_fonts');
*/


/* DON'T DELETE THIS CLOSING TAG */ ?>
