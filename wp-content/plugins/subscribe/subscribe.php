<?php
/*
Plugin Name: Subscribe
*/

if ( is_admin() ) {
	register_deactivation_hook(__FILE__, 'subscribe_deactivate');
    register_activation_hook( __FILE__, 'subscribe_install' );
}

add_action('admin_menu', 'subscribe_plugin_menu');

function subscribe_plugin_menu() {
	add_options_page('Ajax Subscribe Form Options', 'Ajax Subscribe', 'manage_options', 'subscribe-topmenu', 'subscribe_options_page');

}

function subscribe_options_page() {
	if (!current_user_can('manage_options')) {
		die('do one');
	}
	$link = WP_PLUGIN_URL.'/'.plugin_basename( __FILE__ ).'../../export_data.php';
    echo '<div class="wrap">';
    echo '<h2><a style="float: right" href="'. $link .'">Export signups</a></h2>';
	echo '</div>';
}


function subscribeform_add_script() {
	global $post;
	if( !is_admin() ) {
		wp_enqueue_script( 'subscribe-script', plugin_dir_url(__FILE__).'contact.js', array('jquery'), false , true );
		wp_localize_script( 'subscribe-script', 'ajax_object_acf',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'plugin_base_path' => plugin_dir_url(__FILE__),
				'js_alt_loading' => 'Loading...',
				'js_msg_empty_fields' => 'At least one of the form fields is empty.',
				'js_msg_invalid_email' => 'The entered email address isn\'t valid.'
			)
		);
	}
}
add_action('wp_enqueue_scripts', 'subscribeform_add_script');


function createAjaxSignupForm($atts = null) {
	$html = '<form action="" class="email_signup" role="form" method="post">
                        <div class="form-item">
                            <label for="emailSignUp">Sign up to the DofE newsletter</label>                                    
                            <input type="email" name="emailSignUp" class="email">
                            <button type="sumbmit">Sign up</button>                                
                        </div>
                        '. wp_nonce_field('ajax_contactform', '_acf_nonce', true, false) .'
                    </form>';
	return $html;
}
add_shortcode('subscribe', 'createAjaxSignupForm');

function check_email_address_mailgun($email) {
    if (is_email($email)) {
        return 1;
    } else {
        return __( 'The entered email address isn\'t valid.', 'fws-ajax-contact-form' );
    }
}

function ajax_subscribeform_action_callback() {
    global $wpdb;
	$error = '';
	$status = 'error';
    
    parse_str($_POST['data'], $_POST);
    //error_log(print_R($_POST,true));
	if (empty($_POST['email'])) {
		$error = 'All fields are required to enter.';
	} else {
		if (!wp_verify_nonce($_POST['_acf_nonce'], 'ajax_contactform')) {
			$error = __( 'Verification error, try again.' , 'fws-ajax-contact-form' );
		} else {
			$firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $email_check = check_email_address_mailgun($email);
			if ($email_check == 1) {
                $status = 'success';
                $wpdb->insert( 
                    $wpdb->prefix . "signups", 
                    array( 
                        'title' => ($_POST['title'] =='Other' && trim($_POST['other_title']) !== '') ? $_POST['other_title'] : $_POST['title'], 
                        'firstname' => $firstname, 
                        'lastname' => $lastname,
                        'email' => $email, 
                        'submittedtime' => date('Y-m-d H:i:s')
                    )
                );
                $error =  'Thanks for signing up for campaign updates. We hope you find them useful. ';
            } else {
				$error = $email_check;
			}
		}
	}
	$resp = array('status' => $status, 'errmessage' => $error);
	wp_send_json($resp);
}
add_action( 'wp_ajax_contactform_action', 'ajax_subscribeform_action_callback' );
add_action( 'wp_ajax_nopriv_contactform_action', 'ajax_subscribeform_action_callback' );


function subscribe_deactivate() {
	// nothing to do
}

function subscribe_install() {
    global $wpdb;
    $table_nameb = $wpdb->prefix . "signups";   
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $sql = "CREATE TABLE IF NOT EXISTS $table_nameb (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(50) not null default '',
        `firstname` varchar(255) not null default '',
        `lastname` varchar(255) not null default '',
        `email` varchar(255) not null default '',
        `submittedtime` timestamp default '0000-00-00 00:00:00',
        `lastmodified` timestamp default now() on update now(),
        PRIMARY KEY (`id`)
    );";    
    dbDelta( $sql );
}
    

