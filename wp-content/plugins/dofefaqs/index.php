<?php
/*
Plugin Name: DofE F A Qs
Author: David Gurney
Version: 1.0
Author URI: http://www.goodagency.co.uk
*/

class FAQa {
	//var $meta_fields = array("p30-length");
	
	function FAQa()
	{
        global $wp_rewrite;
		// Register custom post types
		register_post_type('faq', array(
			'labels' => array(
				'name' => __( 'FAQ supporters' ),
				'singular_name' => __( 'FAQ' ),
				'add_new' => _x('Add New', 'faq'),
				'add_new_item' => __('Add New FAQ '),
				'edit_item' => __('Edit FAQ '),
				'new_item' => __('New FAQ '),
				'view_item' => __('View FAQ '),
				'search_items' => __('Search FAQ '),
				'not_found' =>  __('No FAQ  found'),
				'not_found_in_trash' => __('No FAQ  found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'FAQ'				
			),
			'public' => true,
			'label' => __('FAQ'),
			'singular_label' => __('FAQ'),
			'public' => true,
            'rewrite' => array('slug' => 'faqs'),
			'exclude_from_search' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,	
			'query_var' => "faq",
			'supports' => array('title', 'editor','revisions'),
		));
		$wp_rewrite->flush_rules();
		$wp_rewrite->flush_rules();
	}
}
// Initiate the plugin
add_action("init", "FAQInit");
function FAQInit() { global $sblock; $sblock = new FAQa(); }