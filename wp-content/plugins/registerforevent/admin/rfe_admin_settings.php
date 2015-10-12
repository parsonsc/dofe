<?php

add_action('admin_menu', 'rfe_admin_menu');
add_action('admin_init', 'rfe_initialize');
register_activation_hook( __FILE__, 'rfe_activate' );
register_deactivation_hook( __FILE__, 'rfe_deactivate' );
    
function rfe_admin_menu(){
     
    add_menu_page('RFE', 'RFE', 'manage_options', 'rfe_plugin_page', array($this, 'rfe_plugin_page'), RFE_PLUGIN_URL . '/admin/img/jg.gif');
    
    // add menu item
    //add_action( "admin_print_styles-$justgiving_options", array( $this, 'justgiving_load' ) );
    
    // adds "Settings" link to the plugin action page
    add_filter( 'plugin_action_links_registerforevent', 'rfe_add_settings_links', 10, 2);   
}

function rfe_add_settings_links( $links ) {
    $settings_link = '<a href="' . admin_url( 'plugins.php?page=rfe_plugin_page' ) . '">Settings</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
}


function rfe_initialize(){
    // check for activation
    $check = get_option( 'rfe_activation' );
    register_setting( 'rfe_general_settings', 'rfe_general_settings' );
    
    wp_register_script('edit-teams-script', RFE_PLUGIN_URL . '/js/jg_edit_teams.js');

    // redirect on activation
    if ($check != "set") {   
        // add theme options
        add_option( 'rfe_activation', 'set');
        // load DB activation function if updating plugin
        rfe_activate();
        // Redirect
        wp_redirect( admin_url( 'plugins.php?page=rfe_plugin_page') );
    }
    if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        add_filter( 'mce_buttons', 'filter_mce_button' );
        //add_filter( 'mce_external_plugins', 'filter_mce_plugin' );
    }
    return false;
}

function filter_mce_button( $buttons ) {
	// add a separation before our button, here our button's id is &quot;mygallery_button&quot;
	array_push( $buttons, 'registerforevent' );
	return $buttons;
}

add_filter('mce_external_plugins', 'rfe_register_tinymce_javascript');
function rfe_register_tinymce_javascript( $plugin_array ) {
	// this plugin file will work the magic of our button
	$plugin_array['registerforevent'] = RFE_PLUGIN_URL. '/admin/js/rfe_plugin.js';
	return $plugin_array;
}

function rfe_deactivate(){
    // remove activation check & version
    delete_option( 'rfe_activation' );

    
    // Flush rewrite rules when deactivated
    flush_rewrite_rules();
}

function rfe_activate(){
    $wppb_default_settings = array(
        'ApiLocation' => 	"https://api-sandbox.justgiving.com/",
        'ApiKey' => 	"decbf1d2",
        'TestUsername' 	=> "david.gurney@thegoodagency.co.uk",
        'TestValidPassword' => "DGcp!091",
        'TestInvalidPassword' => "badPassword",
        'ApiVersion' => 1,
        'Charity' => '2050',
        'Event' => '',
        'pageStory' => "I'm saying farewell to fizzy pop, bye-bye to beer and laters to lattes and drinking nothing but water for 2 weeks to raise money for the RNLI and their lifesavers at sea who drop everything at a moment's notice to save lives on the water.",
        'pageSummaryWhat' => 'taking the H2Only challenge for 2 weeks',
        'pageSummaryWhy' => 'I want to raise money for the RNLI!',
        'imageurl' => "https://s3.amazonaws.com/h2only.3sidedcube.com/profile-image.png",
        'targetAmount' => 150,
        'cc1' => '',            
        'cc2' => '',            
        'cc3' => '',            
        'cc4' => '',            
        'cc5' => '',            
        'cc6' => '',
        'paidaccess' => 0,
        'payamount' => 0,
        'paypal_email' => 'david.gjurney@gmail.com',
        'paypal_femail' => 'david.gjurney@gmail.com',
        'fbappid' => '1452796035009634',
        'useSMTP' => true,
        'smtp_server' => "smtp.mailgun.org",
        'smtp_port' => 25,        
        'smtp_helo' => "mailgun.org",
        'smtp_uname' => "postmaster@sandboxe7036c41314545d9bf71a372e89ad22f.mailgun.org",
        'smtp_pword' => "4r8hrkso1he2",
        'mailer_id' => "UndieRun mail client",
        'email_from' => "hello@theundierun.co.uk",
        'reply_to' => "hello@theundierun.co.uk",
        'friendly_name' => "Thank you",
        'lolagrove' => 0,  
    );
    add_option( 'rfe_general_settings', $wppb_default_settings);        
}