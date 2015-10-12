<?php
/*
Plugin Name: Second Column 
Description: Add a second column to page types 
Author: David Gurney
Version: 1.0
Author URI: http://www.goodagency.co.uk
*/
add_action( 'edit_page_form', 'second_column_box' );
/* Do something with the data entered */
add_action( 'save_post', 'myplugin_save_postdata' );


function second_column_box() {
	
	global $post;
	$custom = get_post_custom($post->ID);
	$second_column = $custom["second_column"][0];
	//$second_column = apply_filters('the_content', $second_column);
	//$second_column = str_replace(']]>', ']]&gt;', $second_column);	
	wp_nonce_field( plugin_basename( __FILE__ ), 'second_column_noncename' );

    wp_editor( $second_column, 'second_column',array(
      'media_buttons' => true,
      'textarea_name' => 'second_column',
      'textarea_rows' => 8,
      'tabindex' => 4,
      'tinymce' => array(
        'theme_advanced_buttons1' => 'bold, italic, ul, min_size, max_size',
        'theme_advanced_buttons2' => '',
        'theme_advanced_buttons3' => '',
        'theme_advanced_buttons4' => '',
        'setup' => 'function (ed) {
            tinymce.documentBaseURL = "' . get_admin_url() . '";
        }',
      ),
      'quicktags'     => TRUE,
      'editor_class'  => 'frontend-article-editor'
    ));
}
/* When the post is saved, saves our custom data */
function myplugin_save_postdata( $post_id ) {
	// verify nonce
	if (!wp_verify_nonce($_POST['second_column_noncename'], basename(__FILE__))) return $post_id;

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) return $post_id;
	} elseif (!current_user_can('edit_post', $post_id)) return $post_id;

	$old = get_post_meta($post_id, 'second_column', true);
	$new = $_POST['second_column'];
	if ($new && $new != $old) update_post_meta($post_id, 'second_column', $new);
	elseif ('' == $new && $old) delete_post_meta($post_id, 'second_column', $old);
}