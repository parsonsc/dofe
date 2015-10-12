<?php
/*
Plugin Name: Case Studies
Description: Case Studies for DofE Diamond website
Author: David Gurney
Version: 1.0
Author URI: http://www.goodagency.co.uk
*/

class Studya {
	//var $meta_fields = array("p30-length");
	
	function Studya()
	{
        $labels = array(
            'name'                       => _x( 'Disciplines', 'taxonomy general name' ),
            'singular_name'              => _x( 'Discipline', 'taxonomy singular name' ),
            'search_items'               => __( 'Search Disciplines' ),
            'popular_items'              => __( 'Popular Disciplines' ),
            'all_items'                  => __( 'All Disciplines' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Discipline' ),
            'update_item'                => __( 'Update Discipline' ),
            'add_new_item'               => __( 'Add New Discipline' ),
            'new_item_name'              => __( 'New Discipline Name' ),
            'separate_items_with_commas' => __( 'Separate disciplines with commas' ),
            'add_or_remove_items'        => __( 'Add or remove disciplines' ),
            'choose_from_most_used'      => __( 'Choose from the most used disciplines' ),
            'not_found'                  => __( 'No disciplines found.' ),
            'menu_name'                  => __( 'Disciplines' ),
        );        
        $args = array(
            'hierarchical'          => true,
            'show_in_nav_menus'     => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite' 				=> array('slug' => 'good-thinking-about', 'with_front' => false)
        );

        register_taxonomy( 'studyac', null, $args );
        wp_insert_term('Cause', 'studyac', array('slug' => 'cause'));
        wp_insert_term('Challenge Ideas', 'studyac', array('slug' => 'challenge-ideas'));

        
        $labels = array(
            'name'                       => _x( 'SubCat', 'taxonomy general name' ),
            'singular_name'              => _x( 'SubCat', 'taxonomy singular name' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'menu_name'                  => __( 'Sub Categories' ),
        );        
        $args = array(
            'hierarchical'          => true,
            'show_in_nav_menus'     => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => false
        );

        register_taxonomy( 'studyacc', null, $args );  
        wp_insert_term('Popular', 'studyacc');
        wp_insert_term('Archive', 'studyacc');
        
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
		register_post_type('studya', array(
			'labels' => array(
				'name' => __( 'Case Studies' ),
				'singular_name' => __( 'Case Study' ),
				'add_new' => _x('Add New', 'studya'),
				'add_new_item' => __('Add New Case Study'),
				'edit_item' => __('Edit Case Study'),
				'new_item' => __('New Case Study'),
				'view_item' => __('View Case Study'),
				'search_items' => __('Search Case Study'),
				'not_found' =>  __('No Case Study found'),
				'not_found_in_trash' => __('No Case Study found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'Case Studies'				
			),
			'public' => false,
			'label' => __('Case Studies'),
			'singular_label' => __('Case Study'),
			'public' => true,
			'exclude_from_search' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,		
			'query_var' => "studya",
            'rewrite' => array('slug' => 'case-study'),
            'taxonomies' => array('studyac', 'studyacc'),
            'has_archive' => false,
            'show_in_nav_menus' => false,
			'supports' => array('title', 'author', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'),
		));
        $wp_rewrite->flush_rules();
        
        add_action( 'show_user_profile', array(&$this, 'casestudy_extra_profile_fields' ) );
        add_action( 'edit_user_profile', array(&$this, 'casestudy_extra_profile_fields' ) );
        add_action( 'personal_options_update', array(&$this, 'casestudy_save_extra_profile_fields') );
        add_action( 'edit_user_profile_update', array(&$this, 'casestudy_save_extra_profile_fields') );
        
		add_action( 'load-post.php', array(&$this, 'studya_custom_meta') );
        add_action( 'load-post-new.php', array(&$this, 'studya_custom_meta') );
		add_action("save_post", array(&$this, "save_studya_meta"), 10, 2);	
        add_action( 'admin_enqueue_scripts', array(&$this, 'studya_image_enqueue') );  
        add_action('restrict_manage_posts',array(&$this, 'restrict_manage_casestudy_sort_by_archive'));        
		//add_action('add_meta_boxes', 'client_custom_meta' );
        add_filter( 'parse_query', array(&$this, 'sort_casestudy_by_meta_value') );
        add_action('manage_casestudy_posts_columns', array(&$this, 'manage_casestudy_posts_columns'));
        add_action('manage_pages_custom_column', array(&$this, 'manage_casestudy_pages_custom_column'),10,2);
		
	}
	function casestudy_save_extra_profile_fields( $user_id ) {

        if ( !current_user_can( 'edit_user', $user_id ) )
            return false;
        update_usermeta( $user_id, 'jobtitle', $_POST['jobtitle'] );
    }
    
    function casestudy_extra_profile_fields( $user ) { ?>
        <h3>Extra profile information</h3>
        <table class="form-table">
		<tr>
			<th><label for="jobtitle">Job title</label></th>
			<td>
				<input type="text" name="jobtitle" id="jobtitle" value="<?php echo esc_attr( get_the_author_meta( 'jobtitle', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Job Title.</span>
			</td>
		</tr>

        </table>
<?php 
    }    
    
    function studya_image_enqueue($hook) {
        if ( 'post-new.php' != $hook && 'edit.php' != $hook && 'post.php' != $hook ) {
            return;
        }
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'client-box-image', plugin_dir_url( __FILE__ ) . 'study-image.js', array( 'jquery' ), null, true );
        wp_localize_script( 'client-box-image', 'client_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'studya' ),
                'button' => __( 'Use this image', 'studya' ),
            )
        );
        wp_enqueue_script( 'client-box-image' );
    }    
    
	function studya_custom_meta() {
    	add_meta_box( 'studya_meta', 'Case Study promo', array(&$this, "studya_meta_callback"), "studya", "normal", "high");

	}
		
	function studya_meta_callback($post){
		global $post;
		$custom = get_post_custom($post->ID);
		$clienttitle = isset($custom["clienttitle"][0])? $custom["clienttitle"][0] : '' ;
        $clientdesc = isset($custom["clientdesc"][0])? $custom["clientdesc"][0] : '' ;
        $clientimage = isset($custom["clientimage"][0])? $custom["clientimage"][0] : '' ;
        $related = isset($custom["related"][0])? $custom["related"][0] : '' ;
        //echo $related;
		$nonce = wp_create_nonce(basename(__FILE__));
?>
	<div style="padding:5px 15px;">
		<label for="job_title">Short title </label>
		<input type="text" name="clienttitle" size="70" autocomplete="on" value="<?php echo $clienttitle; ?>" />
	</div>	
	<div style="padding:5px 15px;">
		<label for="job_title">Short Description </label>
		<input type="text" name="clientdesc" size="70" autocomplete="on" value="<?php echo $clientdesc; ?>" />
	</div>
	<div style="padding:5px 15px;">
        <label for="clientimage" class="prfx-row-title"><?php _e( 'Front image', 'studya' )?></label>
        <input type="text" name="clientimage" id="clientimage" value="<?php echo $clientimage; ?>" />
        <input type="button" id="clientimage_button" class="button" value="<?php _e( 'Choose or Upload an Image', 'studya' )?>" />
    </div>
	<div style="padding:5px 15px;">
        <label for="related" class="prfx-row-title"><?php _e( 'Related items', 'studya' )?></label>
        <select type="text" name="related[]" id="related" multiple="multiple" size="10">
        <?php
        $relateds = unserialize($related);
        //echo print_R($relateds, true);
        $the_query = new WP_Query( array('post_type' => 'studya', 'post_status' => 'publish','posts_per_page'=>-1 ) );
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
?>
                <option value="<?php the_ID();?>"<?php echo (in_array(get_the_ID(), $relateds)) ? ' selected="selected"' : '' ?>><?php the_title(); ?></option>
<?php                
            }
        }        
        ?>
        </select>
    </div>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<input type="hidden" name="studya_meta_box_nonce" value="<?php echo $nonce;?>" />  
	<p>&nbsp;</p>	
<?php
	}

    
    function restrict_manage_casestudy_sort_by_archive() {
        if (isset($_GET['post_type'])) {
            $post_type = $_GET['post_type'];
            if (post_type_exists($post_type) && $post_type=='studya') {
                global $wpdb;
                $sql="SELECT pm.meta_key FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID=pm.post_id WHERE p.post_type='studya' GROUP BY pm.meta_key ORDER BY pm.meta_key";
                $results = $wpdb->get_results($sql);
                $html = array();
                $html[] = "<select id=\"sortby\" name=\"sortby\">";
                $html[] = "<option value=\"None\">No Sort</option>";
                $this_sort = '';
                if (isset($_GET['sortby'])) $this_sort = $_GET['sortby'];
                foreach($results as $meta_key) {
                    $default = ($this_sort==$meta_key->meta_key ? ' selected="selected"' : '');
                    $value = esc_attr($meta_key->meta_key);
                    $html[] = "<option value=\"{$meta_key->meta_key}\"$default>{$value}</option>";
                }
                $html[] = "</select>";
                echo "Sort by: " . implode("\n",$html);
            }
        }
    }    
    
    function sort_casestudy_by_meta_value($query) {
        global $pagenow;
        if (is_admin() && $pagenow=='edit.php' &&
            isset($_GET['post_type']) && $_GET['post_type']=='studya' && 
            isset($_GET['sortby'])  && $_GET['sortby'] !='None')  {
            $query->query_vars['orderby'] = 'meta_value';
            $query->query_vars['meta_key'] = $_GET['sortby'];
        }
    }    
    
    function manage_casestudy_posts_columns($posts_columns) {
        $posts_columns = array(
            'cb' => $posts_columns['cb'],
            'title' => 'GOOD Thinking title',
            );
        if (isset($_GET['sortby']) && $_GET['sortby'] !='None') 
            $posts_columns['meta_value'] = 'Sorted By';

        return $posts_columns;
    }
    
    function manage_casestudy_pages_custom_column($column_name,$post_id) {
        global $pagenow;
        $post = get_post($post_id);
        if ($post->post_type=='studya' && is_admin() && $pagenow=='edit.php')  {
            switch ($column_name) {
                case 'meta_value':
                    if (isset($_GET['sortby']) && $_GET['sortby'] !='None') {
                        echo get_post_meta($post_id,$_GET['sortby'],true);
                    }
                    break;
            }
        }
    }    
	
	function save_studya_meta(){
		global $post;
		// verify nonce
		
		if (!isset($_POST['studya_meta_box_nonce']) || !wp_verify_nonce($_POST['studya_meta_box_nonce'], basename(__FILE__))) {
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
      
		$fields = array('clienttitle', 'clientdesc', 'clientimage', 'related');
		foreach ($fields as $field) {
			$old = get_post_meta($post->ID, $field, true);
			$new = $_POST[$field];
            if ($field == 'fullwidth'){
                if (!isset( $_POST['fullwidth'])) $new = 0;
                //echo $new.'|'.$old.'xx';
                //if ((int)$new != -1) echo 'bb';
                //if (isset($old) && empty($old)) echo 'dd';
            }
            //if ($field == 'related') $related = implode(',', $_POST['related']);
			if (((int)$new != -1 && $new != $old) || isset($old) && empty($old)) {
                //echo 'a';
				update_post_meta($post->ID, $field, $new);
			} elseif ('' == $new && $old) {
                //echo 'b';
				delete_post_meta($post->ID, $field, $old);
			}
		}
        //exit;
	}
}

// Initiate the plugin
add_action("init", "ThinkInit");
function ThinkInit() { global $gtblock; $gtblock = new Studya(); }
