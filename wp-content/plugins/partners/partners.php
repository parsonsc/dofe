<?php
/*
Plugin Name: Partners
Description: Partners for TheGoodAgency website
Author: David Gurney
Version: 1.0
Author URI: http://www.goodagency.co.uk
*/

class Partner {
	//var $meta_fields = array("p30-length");
	
	function Partner()
	{
		// Register custom post types
		register_post_type('partners', array(
			'labels' => array(
				'name' => __( 'Partners' ),
				'singular_name' => __( 'Partner' ),
				'add_new' => _x('Add New', 'partner'),
				'add_new_item' => __('Add New Partner'),
				'edit_item' => __('Edit Partner'),
				'new_item' => __('New Partner'),
				'view_item' => __('View Partner'),
				'search_items' => __('Search Partners'),
				'not_found' =>  __('No Partners found'),
				'not_found_in_trash' => __('No Partners found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'Partners'				
			),
			'public' => false,
			'label' => __('Partners'),
			'singular_label' => __('Partner'),
			'public' => true,
			'exclude_from_search' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,			
			'query_var' => "partner",
			'supports' => array('title', 'thumbnail', 'revisions', 'page-attributes'),
		));
		add_action( 'load-post.php', array(&$this, 'partner_custom_meta') );
        add_action( 'load-post-new.php', array(&$this, 'partner_custom_meta') );
		add_action("save_post", array(&$this, "save_partner_meta"), 10, 2);		
		//add_action('add_meta_boxes', 'partner_custom_meta' );
		
	}
	
	function partner_custom_meta() {
    	add_meta_box( 'partner_meta', 'Work intro', array(&$this, "partner_meta_callback"), "partners", "normal", "high");
	}

		
	function partner_meta_callback($post){
		global $post;
		$custom = get_post_custom($post->ID);
		$partnerurl = $custom["partnerurl"][0];
		$nonce = wp_create_nonce(basename(__FILE__));
?>
	<div style="float:left;padding:5px 15px;">
		<label for="job_title">URL </label>
		<input type="text" name="partnerurl" size="70" autocomplete="on" value="<?php echo $partnerurl; ?>" />
		<input type="hidden" name="partner_meta_box_nonce" value="<?php echo $nonce;?>" />  
	</div>	
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>	
<?php
	}
	
	function save_partner_meta(){
		global $post;
		// verify nonce
		
		if (!isset($_POST['partner_meta_box_nonce']) || !wp_verify_nonce($_POST['partner_meta_box_nonce'], basename(__FILE__))) {
			return (is_object($post))? $post->ID : 0;
		}
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return (is_object($post))? $post->ID : 0;
		}
		// check permissions
		
		if (!current_user_can('edit_post', $post->ID)) {
			return (is_object($post))? $post->ID : 0;
		}

		$fields = array('partnerurl');
		foreach ($fields as $field) {
			$old = get_post_meta($post->ID, $field, true);
			$new = $_POST[$field];
			if ($new && $new != $old) {
				update_post_meta($post->ID, $field, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post->ID, $field, $old);
			}
		}
	}
}

// Initiate the plugin
add_action("init", "PartnersInit");
function PartnersInit() { global $clblock; $clblock = new Partner(); }
