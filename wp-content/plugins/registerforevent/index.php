<?php
/**
 * @package Register for Diamond Challenge by companies
 * @version 1.6
 */
/*
Plugin Name: RegisterForEvents 
Description:
Author: David Gurney
Version: 1
*/
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define( 'RFE_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'RFE_PLUGIN_URL', str_replace("http:","",plugins_url( 'registerforevent' ) ) );
define( 'RFE_VERSION', 1);
include_once(RFE_PLUGIN_DIR . '/admin/rfe_admin_settings.php');

register_activation_hook( __FILE__, 'rfe_install' );
include_once(RFE_PLUGIN_DIR.'/front-end/RFE.register.php'); 
include_once(RFE_PLUGIN_DIR.'/front-end/rfe.thankyou.php'); 
if (!is_admin()) {
    add_shortcode('rfe-register', 'rfe_front_end_register');
    add_shortcode('rfe-thankyou', 'rfe_front_end_thanks');
    add_action('wp_enqueue_scripts', 'rfe_enqueuescripts'); 
}
else{
    $wpjg_admin = RFE_PLUGIN_DIR . '/admin/';	

    if (file_exists ( $wpjg_admin.'class.admin.php' ))	require_once($wpjg_admin.'class.admin.php');
    $RFE_Admin = new RFE_Admin();
    add_action('wp_enqueue_scripts', 'rfe_enqueueascripts');
    register_activation_hook( __FILE__, array( $RFE_Admin, 'rfe_activate' ) );
    register_deactivation_hook( __FILE__, array( $RFE_Admin, 'rfe_deactivate' ) );
    add_action( 'admin_init', array( $RFE_Admin, 'rfe_initialize' ) );
    add_action( 'admin_menu', array( $RFE_Admin, 'rfe_admin' ) );
}

add_action( 'init', 'post_type'  );
add_action( 'save_post', 'save_partner_meta', 10, 3);

add_action( 'init','assets'  );
add_action( 'single_template', 'load_mytemplate' );

static $meta_keys = array(
    'event_id',
    'start_date',
    'end_date',
    'capacity',
    'event_template',
    'status',
    'custom_header',
    'custom_footer',
    'organizer_name',
    'organizer_email',
    'organizer_phone',
    'organizer_shortname',
    'organizer_logo',
    'venue',
    'address',
    'city',
    'region',
    'postal_code',
    'country_code',
    'country_name',
    'latitude',
    'longitude'
);

add_action('admin_head', 'embedUploaderCode');
function embedUploaderCode() {
  ?>
  <script type="text/javascript">
jQuery.noConflict();

(function($){
  $(document).ready(function() {
 
    $('.removeImageBtn').click(function() {
      $(this).closest('p').prev('.awdMetaImage').html('');   
      $(this).prev().prev().val('');
      return false;
    });
 
    $('.image_upload_button').click(function() {
      inputField = $(this).closest('p').find('.metaValueField');
      tb_show('', 'media-upload.php?TB_iframe=true');
      window.send_to_editor = function(html) {
        url = $(html).attr('href');
        inputField.val(url);
        inputField.closest('p').find('.awdMetaImage').html('<p>URL: '+ url + '</p>').prepend('<img id="theImg" src="'+url+'" width="200" />');  
        tb_remove();
      };
      return false;
    });

   /* $('#start_date,#end_date').datepicker({
        dateFormat : 'dd-mm-yy '
    });*/

  });
})(jQuery); 
  </script>
  <?php
 
}

function post_type() {
    
    check_template();
    
    register_post_type( 'partner', array(
        'public' => true,
        'map_meta_cap' => true,
        'rewrite' => array( 'slug' => 'partner' ),
        'supports' => array( 'title', 'editor', 'thumbnail', 'author' ),
        'register_meta_box_cb' => 'meta_boxes' ,
        'show_ui' => true,
        'labels' => array(
            'name' => __( 'Partners', 'registerforevent' ),
            'singular_name' => __( 'Partner', 'registerforevent' ),
            'add_new_item' => __( 'New Partner', 'registerforevent' ),
            'edit_item' => __( 'Edit Partner', 'registerforevent' ),
        )
    ) );
}

function meta_boxes( $post ) {
    // Basic settings box
    add_meta_box( 
        'company_details',
        __( 'Partner Event Details', 'registerforevent' ),
        'details_box' ,
        'partner',
        'side'
    );
    add_meta_box( 
        'event_organizer',
        __( 'Organizer', 'registerforevent' ),
        'organizer_box' ,
        'partner',
        'side'
    );    
    add_meta_box( 
        'event_venue',
        __( 'Venue', 'registerforevent' ),
        'venue_box' ,
        'partner',
        'side'
    );
    add_meta_box( 
        'event_header',
        __( 'Custom Header', 'registerforevent' ),
        'header_box',
        'partner'
    );
    // Custom footer
    add_meta_box( 
        'event_footer',
        __( 'Custom Footer', 'registerforevent' ),
        'footer_box' ,
        'partner'
    );
    // Event template
    add_meta_box( 
        'event_template',
        __( 'Template', 'registerforevent' ),
        'event_template' ,
        'partner',
        'side'
    ); 
}
function header_box( $post ) {
    template_render(
        'header_box',
        get_mysettings( $post->ID )
    );
}
function footer_box( $post ) {
    template_render(
        'footer_box',
        get_mysettings( $post->ID )
    );
}
function event_template( $post ) { 
    template_render(
        'event_template',
        get_mysettings( $post->ID )
    );
}
function details_box( $post ) {
    template_render(
        'details_box',
        get_mysettings( $post->ID )
    );
}
function organizer_box( $post ) {
    template_render(
        'organizer_box',
        get_mysettings( $post->ID )
    );
}
function venue_box( $post ) {
    template_render(
        'venue_box',
        get_mysettings( $post->ID )
    );
}

function save_partner_meta( $post_id, $post, $update ) {
    global $meta_keys;
    global $wpdb;
    $file_id = null;
    $restrict_to = null;
    $new_settings = null;

    // If this isn't a 'partner' post, don't update it.
    if ( 'partner' != $post->post_type ) {
        return;
    }    
    // Delete any previous errors
    //error_log(print_R($_POST, true));    
    if ( isset( $_POST['registerforevent_nonce'] ) && !wp_verify_nonce( $_POST['registerforevent_nonce'], 'registerforevent' ) ) return $post_id;
    //error_log(print_R($_POST, true));
    if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
    //error_log(print_R($_POST, true));
    if( isset( $_POST['event'] ) && !empty( $_POST['event'] ) ) $new_settings = $_POST['event'];
    else return $post_id;
    //error_log(print_R($_POST, true));
    //error_log('new settings p '. print_R($_POST, true));
    //error_log('new settings '. print_R($new_settings, true));
    //error_log('meta '. print_R($meta_keys, true));
    foreach( $meta_keys as $k )
        if( isset( $new_settings[$k] ) ){
            //error_log($k.' = '. $new_settings[$k].'||'. intval( $new_settings[$k] ) );
            if( in_array( $k, array( 'privacy', 'organizer_id', 'venue_id', 'venue_organizer_id', 'capacity' ) ) ){
                if( $new_settings[$k] != '' ) update_post_meta( $post_id, $k, bigintval( $new_settings[$k] ) );
                else update_post_meta( $post_id, $k, '' );
            }
            else{
                if ( in_array( $k, array( 'custom_header', 'custom_footer' ) ) ) {
                    $new_settings[$k] = htmlspecialchars($new_settings[$k]);
                    update_post_meta( $post_id, $k, wp_filter_post_kses( $new_settings[$k] ) );
                }
                elseif( in_array( $k, array( 'organizer_logo' ) ) ){
                    $new_settings[$k] = str_replace(get_option( 'siteurl' ), '', $new_settings[$k]);
                    update_post_meta( $post_id, $k, sanitize_text_field( $new_settings[$k] ) );
                }
                else update_post_meta( $post_id, $k, sanitize_text_field( $new_settings[$k] ) );
                //error_log($k.' = '. $new_settings[$k] ) ;
            }
        }
    
    // Save post template
    if( isset( $_POST['post_template'] ) ) update_post_meta( $post_id, '_post_template', sanitize_text_field( $_POST['post_template'] ) );
    
    // Make sure no cached data exists
    delete_transient( 'partners_' . $post_id );
    
    $settings = array();
    
    foreach ( $meta_keys as $k ) $settings[$k] = $_POST['event'][$k];
    
    set_transient('partners_' . $post_id, $settings, 86400 );  
    
    // Check if the template file is on place
    check_template();
    $wpdb->update(
        $wpdb->prefix . "posts",
        array(
            'post_name' => sanitize_title_with_dashes( $new_settings['organizer_shortname'] ),
        ),
        array('ID' => $post_id));
    /*
    
    wpdb->update(
        $wpdb->prefix . "posts",
        array(
            'post_name' => sanitize_title_with_dashes( $new_settings['organizer_shortname']  ),
        ),
        array('ID' => $post_id));
    $post = get_post($post_id);
    $post->post_name = sanitize_title_with_dashes( $new_settings['organizer_shortname']  );
    wp_update_post( $post );
    */
    return $post_id;
}

function template_render( $_name, $vars = null, $echo = true ) {
    ob_start();
    if( !empty( $vars ) )
        extract( $vars );
    
    if( !isset( $path ) )
        $path = dirname( __FILE__ ) . '/templates/';
    include $path . $_name . '.php';
    
    $data = ob_get_clean();
    
    if( $echo )
        echo $data;
    else
        return $data;
}

function check_template() {
    $template_name = 'single-partner.php';
    $source_template_file = dirname( __FILE__ ) .'/templates/' . $template_name;
    $theme_folder = get_stylesheet_directory();
    if( !file_exists( $theme_folder . '/' . $template_name ) )
        copy( $source_template_file, $theme_folder . '/' . $template_name );
}

function load_mytemplate( $template ) {
    global $wp_query;
    $post = $wp_query->get_queried_object();
    if ( $post ) {
        $post_template = get_post_meta( $post->ID, '_post_template', true );
        if( !empty( $post_template ) && $post_template != 'default' ) {
            $template = get_stylesheet_directory() . "/{$post_template}";
            if( !file_exists( $template ) ) $template = get_template_directory() . "/{$post_template}";
        }
    }
    return $template;
}

function get_mysettings( $post_id = null ) {
    global $meta_keys;
    $transient_name =  'partners_' . $post_id;
    // Check for a cache
    $settings = get_transient( $transient_name );
    //print_R(get_option( 'gmt_offset' ).'||');
    if( !$settings ) {
        if( !$post_id ) return $settings;
        foreach ( $meta_keys as $k ) $settings[$k] = get_post_meta( $post_id, $k, true );
        //error_log(print_R($settings, true));  
        set_transient( $transient_name, $settings, 86400 );
    }
    $settings['post_template'] = get_post_meta( $post_id, '_post_template', true );
    return $settings;
}
function rfe_enqueueascripts(){

    //wp_enqueue_script('rfe-geocode', JG_PLUGIN_URL.'/js/geocode.js', array('jquery'), '1', true);
}  
function rfe_enqueuescripts(){
    /*
    wp_enqueue_style( 'justgiving', JG_PLUGIN_URL.'/css/justgiving.css' );    
    wp_enqueue_script('justgiving-raised', JG_PLUGIN_URL.'/js/justgiving.js', array('jquery', 'modernizr'), '1', true);
    wp_localize_script('justgiving-raised', 'ajaxjustgiving', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('justgiving-raised', 'ajaxsiteroot', array('url' => get_home_url('/')));
    */
}

if(!function_exists('rfe_check_missing_http')){
    function rfe_check_missing_http($redirectLink) {
        //#^(?:[a-z\d]+(?:-+[a-z\d]+)*\.)+[a-z]+(?::\d+)?(?:/|$)#i
        return preg_match('#^(https?:\/\/)#i', $redirectLink);
    }
}

function bigintval($value) {
    $value = trim($value);
    if (ctype_digit($value)) {
        return $value;
    }
    $value = preg_replace("/[^0-9](.*)$/", '', $value);
    if (ctype_digit($value)) {
        return $value;
    }
    return 0;
}

function rfe_install() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . "registrants";
    //ALTER TABLE  `wp_jgusers` ADD  `address2` VARCHAR( 255 ) NOT NULL AFTER  `address`
    $sql = "CREATE TABLE IF NOT EXISTS  $table_name (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `userid` bigint(20) unsigned NOT NULL,
      `title` varchar(255) NOT NULL,
      `firstname` varchar(255) NOT NULL,
      `lastname` varchar(255) NOT NULL,
      `dob` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `address` varchar(255) NOT NULL,
      `address2` varchar(255) NOT NULL,
      `towncity` varchar(255) NOT NULL,
      `county` varchar(255) NOT NULL,
      `postcode` varchar(20) NOT NULL,
      `packbypost` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `cpage` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `hasaccount` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `userEnc` varchar(255) NOT NULL,  
      `corporate` varchar(255) NOT NULL,
      `signupdate` int(11) unsigned NOT NULL,
      `optin` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `country` varchar(40) NOT NUll,
      `heardabout` varchar(255) NOT NUll,
      `work` varchar(255) NOT NUll,
      `paidaccess` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `paytoken` varchar(300) NOT NULL,
      `txn_id` varchar(600) NOT NULL,
      `tsandcs` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `advocate` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      PRIMARY KEY (`id`)  
    );";
    dbDelta( $sql );
    
    $table_name = $wpdb->prefix . "corporate";
    //ALTER TABLE  `wp_jgusers` ADD  `address2` VARCHAR( 255 ) NOT NULL AFTER  `address`
    $sqlb = "CREATE TABLE IF NOT EXISTS  $table_name (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `userid` bigint(20) unsigned NOT NULL,
      `title` varchar(255) NOT NULL,
      `address` varchar(255) NOT NULL,
      `address2` varchar(255) NOT NULL,
      `towncity` varchar(255) NOT NULL,
      `county` varchar(255) NOT NULL,
      `postcode` varchar(20) NOT NULL,
      `eventdate` int(11) unsigned NOT NULL,
      PRIMARY KEY (`id`)  
    );";
    dbDelta( $sqlb );
}    