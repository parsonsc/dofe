<?php
/*
Plugin Name: Celebs
Description: Celeb Supporters for DofE Diamond website
Author: David Gurney
Version: 1.0
Author URI: http://www.goodagency.co.uk
*/

class Celeba {
	//var $meta_fields = array("p30-length");
	
	function Celeba()
	{
        
        $args = array(
            'hierarchical'          => true,
            'show_in_nav_menus'     => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite' 				=> array('slug' => 'celeb-cat', 'with_front' => false)
        );

        register_taxonomy( 'celebc', null, $args );
        global $wp_rewrite;
		// Register custom post types
		register_post_type('celeb', array(
			'labels' => array(
				'name' => __( 'Celeb supporters' ),
				'singular_name' => __( 'Celeb supporter' ),
				'add_new' => _x('Add New', 'celeb'),
				'add_new_item' => __('Add New Celeb supporter'),
				'edit_item' => __('Edit Celeb supporter'),
				'new_item' => __('New Celeb supporter'),
				'view_item' => __('View Celeb supporter'),
				'search_items' => __('Search Celeb supporters'),
				'not_found' =>  __('No Celeb supporters found'),
				'not_found_in_trash' => __('No Celeb supporters found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'Celeb supporters'				
			),
			'public' => true,
			'label' => __('Celeb supporters'),
			'singular_label' => __('Celeb supporter'),
			'public' => true,
            'rewrite' => array('slug' => 'celeb-supporters'),
			'exclude_from_search' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,	
            'taxonomies' => array('celebc'),            
			'query_var' => "celeb",
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'author'),
		));
		$wp_rewrite->flush_rules();
		add_filter("manage_edit-celeb_columns", array(&$this, "celeb_edit_columns"));
		add_action("manage_posts_custom_column",  array(&$this, "celeb_custom_columns"));
		add_action("admin_init", array(&$this, "celeb_admin_init"));
		add_action("save_post", array(&$this, "save_celeb_meta"), 10, 2);		
        add_action( 'admin_enqueue_scripts', array(&$this, 'celeb_image_enqueue') );  

		if (class_exists('MultiPostThumbnails')) {
			new MultiPostThumbnails(array(
			'label' => 'Blogger thumb',
			'id' => 'blogger-thumb',
			'post_type' => 'celeb'
			)
		);	
		}
	}
    
    function celeb_image_enqueue($hook) {
        if ( 'post-new.php' != $hook && 'edit.php' != $hook && 'post.php' != $hook ) {
            return;
        }
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'client-box-image', plugin_dir_url( __FILE__ ) . 'celeb-image.js', array( 'jquery' ), null, true );
        wp_localize_script( 'client-box-image', 'client_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'celeb' ),
                'button' => __( 'Use this image', 'celeb' ),
            )
        );
        wp_enqueue_script( 'client-box-image' );
    }        
	
	function celeb_admin_init(){
		add_meta_box("celeb_meta", "Job Title", array(&$this, "celeb_meta_options"), "celeb", "normal", "high");
	}
	
	function celeb_meta_options(){
		global $post;
		$custom = get_post_custom($post->ID);
		$job_title = $custom["job_title"][0];
        $celebmini = isset($custom["celebmini"][0])? $custom["celebmini"][0] : '' ;
		$nonce = wp_create_nonce(basename(__FILE__));
?>
	<div style="padding:5px 15px;">
		<label for="job_title">Job title </label>
		<input type="text" name="job_title" size="70" autocomplete="on" value="<?php echo $job_title; ?>" />
	</div>
	<div style="padding:5px 15px;">
        <label for="celebmini" class="prfx-row-title"><?php _e( 'Front image', 'celeb' )?></label>
        <input type="text" name="celebmini" id="celebmini" value="<?php echo $celebmini; ?>" />
        <input type="button" id="celebmini_button" class="button" value="<?php _e( 'Choose or Upload an Image', 'celeb' )?>" />
    </div>	
    <input type="hidden" name="staff_meta_box_nonce" value="<?php echo $nonce;?>" />  
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>	
<?php
	}
	
	function save_celeb_meta(){
		global $post;
		// verify nonce
		
		if (!isset($_POST['staff_meta_box_nonce']) || !wp_verify_nonce($_POST['staff_meta_box_nonce'], basename(__FILE__))) {
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

		$fields = array('job_title');
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

	function celeb_edit_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Name",
			"job_title" => "Job Title",
		);
		return $columns;
	}

	function celeb_custom_columns($column){
		global $post;
		switch ($column){
			case "job_title":
				$custom = get_post_custom();
				echo $custom["job_title"][0];
				break;	
		}
	}
}
// Initiate the plugin
add_action("init", "CelebInit");
function CelebInit() { global $sblock; $sblock = new Celeba(); }