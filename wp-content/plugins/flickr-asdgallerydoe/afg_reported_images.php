<?php
include_once('afg_libs.php');
include_once('afg_edit_galleries.php');
include_once('afg_add_galleries.php');
include_once('afg_saved_galleries.php');
include_once('afg_advanced_settings.php');
include_once('afg_admin_settings.php');
require_once('afgFlickr/afgFlickr.php');

add_action('admin_init', 'afg_admin_init');
add_action('admin_init', 'afg_auth_read');
add_action('admin_menu', 'afg_admin_menu2');
add_action( 'admin_enqueue_scripts', 'register_adminreport_scripts'  );
add_action( 'wp_ajax_afg_report_images', 'ajax_afg_ban_images' );
 

function register_adminreport_scripts() {
    wp_register_script( 'ajax-adminreport_scripts-admin', AFGSURL . '/js/imagereport.js' );
    wp_enqueue_script( 'ajax-adminreport_scripts-admin' );
} 

function ajax_afg_ban_images() {
    global $pf;
    // First, check the nonce to make sure it matches what we created when displaying the message.
    // If not, we won't do anything.
    
    if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax-afg-removeimg-nonce-'.  $_REQUEST['report'] ) ) {
        $photo = $pf->photos_getAllContexts($_REQUEST['report']);

        foreach ($photo['set'] as $id => $set){
            $pf->photosets_removePhoto($set['id'] ,$_REQUEST['report']);
        }
        $pf->photosets_addPhoto(get_option('afg_banned_photoset'),$_REQUEST['report'] );    
        die( '1' );
    } 
    die( '0' ); 
} // end hide_admin_notification


function afg_admin_menu2() {
    $afg_reports_page = add_submenu_page('afg_plugin_page', 'Reported images | Flickr Gallery', 'Reported images', 'create_users', 'afg_plugin_page2', 'afg_reports_html_page');
}


create_afgFlickr_obj();

function afg_reports_html_page() {
    global $afg_photo_size_map, $afg_on_off_map, $afg_descr_map, 
        $afg_columns_map, $afg_bg_color_map, $afg_width_map, $pf,
        $afg_sort_order_map, $afg_slideshow_map, $wpdb;
?>
   <div class='wrap'>
   <h2><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Flickr Gallery Settings</h2>

<?php

    if ($_GET && wp_verify_nonce( $_REQUEST['nonce'], 'ajax-afg-removeimg-nonce-'.  $_REQUEST['report'] ) ) {

        //$photo = $pf->photos_getAllContexts($_REQUEST['report']);
        $ret = $wpdb->update( 
            $wpdb->prefix."flickruploads", 
            array( 
                'status' => -1
            ), 
            array( 'photoid' => $_REQUEST['report'] ), 
            array( 
                '%d'
            ),
            array( 
                '%s'
            )
        );
        /*
        foreach ($photo['set'] as $id => $set){
            $pf->photosets_removePhoto($set['id'] ,$_REQUEST['report']);
        }
        $pf->photosets_addPhoto(get_option('afg_banned_photoset'),$_REQUEST['report'] );    
        */
    }
    else{
        echo $_REQUEST['nonce'];
        echo 'ajax-afg-removeimg-nonce-'.  $_REQUEST['report'];
        echo wp_create_nonce( 'ajax-afg-removeimg-nonce-'. $photo['id'] );
        echo wp_verify_nonce( $_REQUEST['nonce'], 'ajax-afg-removeimg-nonce-'.  $_REQUEST['report'] );
    }
    $url=$_SERVER['REQUEST_URI'];

?>
    <form method='post' action='<?php echo $url ?>'>
        <?php echo afg_generate_version_line() ?>
               <div class="postbox-container" style="width:69%; margin-right:1%">
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Reported images</h3>       
<?php
        $rsp_obj = $wpdb->get_results("
            SELECT photoid as id, photosecret as secret, farm, server 
            FROM {$wpdb->prefix}flickruploads
            WHERE status=1  
        ", ARRAY_A);

        
        if (!$rsp_obj) echo afg_error();
        else {
            foreach($rsp_obj as $photo) {
                $photo_url = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
                $linkurl = $url . '&report=' . $photo['id'] . '&nonce=' . wp_create_nonce( 'ajax-afg-removeimg-nonce-'. $photo['id'] );
                echo '<a href="'. $linkurl .'" class="ban-image"><img src="'.$photo_url.'"/></a>';
            }
        }

?>
                     </div> 
                  </div>
               </div>
    </form>
<?php
    if (DEBUG) {
        print_all_options();
    }
}    
?>    