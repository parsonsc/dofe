<?php

add_action('admin_menu', 'justgiving_admin_menu');
add_action('admin_init', 'justgiving_initialize');
register_activation_hook( __FILE__, 'justgiving_activate' );
register_deactivation_hook( __FILE__, 'justgiving_deactivate' );
    
function justgiving_admin_menu(){
     
    add_menu_page('JustGiving', 'JustGiving', 'manage_options', 'jg_plugin_page', array($this, 'jg_plugin_page'), JG_PLUGIN_URL . '/admin/img/jg.gif');
    add_submenu_page('jg_plugin_page', 'Default Settings | JustGiving', 'Default Settings', 'manage_options', 'jg_plugin_page', 'justgiving_options_page');
    add_submenu_page('jg_plugin_page', 'Add Page | JustGiving', 'Add Page', 'manage_options', 'jg_add_page', 'jg_add_page');
    add_submenu_page('jg_plugin_page', 'Teams | JustGiving', 'Teams', 'manage_options', 'jg_add_team_page', 'jg_add_team_page');
    add_submenu_page('jg_plugin_page', 'Edit Teams | JustGiving', 'Edit Teams', 'manage_options', 'jg_view_edit_teams_page', 'jg_view_edit_teams_page');        
    // add menu item
    //add_action( "admin_print_styles-$justgiving_options", array( $this, 'justgiving_load' ) );
    
    // adds "Settings" link to the plugin action page
    add_filter( 'plugin_action_links_justgiving', 'jg_add_settings_links', 10, 2);   
}

function jg_add_settings_links( $links ) {
    $settings_link = '<a href="' . admin_url( 'plugins.php?page=jg_plugin_page' ) . '">Settings</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
}

function justgiving_options_page(){
    // Grab Options Page
    include( JG_PLUGIN_DIR.'/admin/options.php' );
}    

function justgiving_initialize(){
    // check for activation
    $check = get_option( 'justgiving_activation' );
    register_setting( 'jg_general_settings', 'jg_general_settings' );
    
    wp_register_script('edit-teams-script', JG_PLUGIN_URL . '/js/jg_edit_teams.js');

    // redirect on activation
    if ($check != "set") {   
        // add theme options
        add_option( 'justgiving_activation', 'set');
        // load DB activation function if updating plugin
        justgiving_activate();
        // Redirect
        wp_redirect( admin_url( 'plugins.php?page=jg_plugin_page') );
    }
    if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        add_filter( 'mce_buttons', 'filter_jgmce_button' );
        //add_filter( 'mce_external_plugins', 'filter_mce_plugin' );
    }
    return false;
}

function filter_jgmce_button( $buttons ) {
	// add a separation before our button, here our button's id is &quot;mygallery_button&quot;
	array_push( $buttons, 'justgiving' );
	return $buttons;
}

add_filter('mce_external_plugins', 'justgiving_register_tinymce_javascript');
function justgiving_register_tinymce_javascript( $plugin_array ) {
	// this plugin file will work the magic of our button
	$plugin_array['justgiving'] = JG_PLUGIN_URL. '/admin/js/justgiving_plugin.js';
	return $plugin_array;
}

function justgiving_deactivate(){
    // remove activation check & version
    delete_option( 'justgiving_activation' );
    delete_option( 'justgiving_version' );
    
    // Flush rewrite rules when deactivated
    flush_rewrite_rules();
}

function justgiving_activate(){
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
        'stripekey' => '',
        'stripepkey' => '',
    );
    add_option( 'jg_general_settings', $wppb_default_settings);        
}

function jg_add_page(){
    include( JG_PLUGIN_DIR.'/admin/jg.add.page.php' );
    jg_add_page_fn();
}

function jg_add_team_page(){
    include( JG_PLUGIN_DIR.'/admin/jg.add.team.php' );
    jg_add_team_fn();
}

function jg_view_edit_teams_page(){
    include( JG_PLUGIN_DIR.'/admin/jg.edit.team.php' );
    jg_edit_team_fn();
}