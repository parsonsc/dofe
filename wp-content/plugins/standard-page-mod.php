<?php
/*
Plugin Name: Standard page mod
Description: Standard page modifications for TheGoodAgency website
Author: David Gurney
Version: 1.0
Author URI: http://www.thegoodagency.co.uk
*/
add_action( 'add_meta_boxes', 'choosepopup_add_custom_box' );

/* Do something with the data entered */
add_action('save_post', 'choosepopup_save_postdata');

/* Adds a box to the main column on the Post and Page edit screens */
function choosepopup_add_custom_box() {
	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'choosepopup_sectionid',
			__( 'Box link ', 'choosepopup_textdomain' ),
			'choosepopup_inner_custom_box',
			$screen
		);
	}	
}

add_action('wp_enqueue_scripts', 'se_wp_enqueue_scripts');
function se_wp_enqueue_scripts() {
    wp_enqueue_script('suggest');
}

add_action('wp_head', 'se_wp_head');
function se_wp_head() {
if (is_admin()) { 
?>
<script type="text/javascript">
    var se_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';

    jQuery(document).ready(function() {
        jQuery('#choosepopup_new_field').suggest(se_ajax_url + '?action=se_lookup');
    });
</script>
<?php
}
}

add_action('wp_ajax_se_lookup', 'se_lookup');
add_action('wp_ajax_nopriv_se_lookup', 'se_lookup');

function se_lookup() {
    global $wpdb;

    $search = like_escape($_REQUEST['q']);

    $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
        WHERE post_title LIKE \'' . $search . '%\'
        AND post_status = \'publish\'
        ORDER BY post_title ASC';
    foreach ($wpdb->get_results($query) as $row) {
        $post_title = $row->post_title;
        $id = $row->ID;
        echo get_permalink($id) ."\n";
    }
    die();
}

/* Prints the box content */
function choosepopup_inner_custom_box() {
	// Use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'choosepopup_noncename' );
	$popup_list = new WP_Query();
    	$popup_list->query(
		array(
			'post_status' => 'publish',
			'post_type' => array( 'post', 'page' ),
			'order_by' => 'menu_order, post_title',
			'posts_per_page' => '-1'
			)
		);
	//print_r($popup_list);
	if ( ! empty($popup_list) ) {
		$meta_values = get_post_meta($_GET['post'], 'popup', true);
		echo '<label for="choosepopup_new_field">' . __("Popup post", 'choosepopup_textdomain' ) . '</label> ';
		echo '<input name="choosepopup_new_field" id="choosepopup_new_field" value="'.$meta_values.'" />';
        /*
		echo '<option></option>';
		foreach ($popup_list->posts as $q) {
			if (intval($q->ID) > 0) echo '<option value="' . $q->ID . '"';
			if (intval($q->ID) > 0 && intval($q->ID) == $meta_values) echo ' selected';
			if (intval($q->ID) > 0) echo '>' . $q->post_title . '        '. ucfirst($q->post_type) . '</option>';
		}
		echo '</select>';
        */
        $anc_values = get_post_meta($_GET['post'], 'anchor', true);
		echo '<br /><label for="chooseanchor_new_field">Popup anchor</label> ';
		echo '<input name="chooseanchor_new_field" id="chooseanchor_new_field" type="text" value="'.$anc_values.'"/>';
	}
}

/* When the post is saved, saves our custom data */
function choosepopup_save_postdata( $post_id ) {

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( !wp_verify_nonce( $_POST['choosepopup_noncename'], plugin_basename(__FILE__) )) {
		return $post_id;
	}

	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
	// to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

  
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	// OK, we're authenticated: we need to find and save the data

	$mydata = $_POST['choosepopup_new_field'];
    $anc = $_POST['chooseanchor_new_field'];
	// Do something with $mydata 
	// probably using add_post_meta(), update_post_meta(), or 
	// a custom table (see Further Reading section below)
	if (!update_post_meta($post_id, 'popup', $mydata)) add_post_meta($post_id, 'popup', $mydata);
	if (!update_post_meta($post_id, 'anchor', $anc)) add_post_meta($post_id, 'anchor', $anc);
	return $mydata;
}
