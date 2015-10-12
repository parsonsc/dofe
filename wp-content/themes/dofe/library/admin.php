<?php
/*
	- removing some default WordPress dashboard widgets
	- an example custom dashboard widget
	- adding custom login css
	- changing text in footer of admin
*/

/************* DASHBOARD WIDGETS *****************/

// disable default dashboard widgets
function disable_default_dashboard_widgets() {
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );       // Right Now Widget
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' ); // Comments Widget
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );  // Incoming Links Widget
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );         // Plugins Widget

	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );      // Quick Press Widget
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );   // Recent Drafts Widget
	remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );         //
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );       //

	// removing plugin dashboard boxes
	remove_meta_box( 'yoast_db_widget', 'dashboard', 'normal' );         // Yoast's SEO Plugin Widget
}

/*
Now let's talk about adding your own custom Dashboard widget.
Sometimes you want to show clients feeds relative to their
site's content. For example, the NBA.com feed for a sports
site. Here is an example Dashboard Widget that displays recent
entries from an RSS Feed.

For more information on creating Dashboard Widgets, view:
http://digwp.com/2010/10/customize-wordpress-dashboard/
*/

// removing the dashboard widgets
add_action( 'admin_menu', 'disable_default_dashboard_widgets' );
// adding any custom widgets
add_action( 'wp_dashboard_setup', 'core_custom_dashboard_widgets' );


/************* CUSTOM LOGIN PAGE *****************/
function core_login_css() {
	wp_enqueue_style( 'core_login_css', get_template_directory_uri() . '/library/css/login.css', false );
}
function core_login_url() {  return home_url(); }
function core_login_title() { return get_option( 'blogname' ); }

// calling it only on the login page
add_action( 'login_enqueue_scripts', 'core_login_css', 10 );
add_filter( 'login_headerurl', 'core_login_url' );
add_filter( 'login_headertitle', 'core_login_title' );

/************* CUSTOMIZE ADMIN *******************/
// Custom Backend Footer
function core_custom_admin_footer() {
	_e( '<span id="footer-thankyou">Developed by <a href="http://www.goodagency.co.uk" target="_blank">GOOD Agency</a></span>. ', 'coretheme' );
}

// adding it to the admin area
add_filter( 'admin_footer_text', 'core_custom_admin_footer' );