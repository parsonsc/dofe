<?php
include_once('upl_libs.php');

//include_once('advanced_settings.php');
require_once('uplFlickr/uplFlickr.php');

add_action('admin_init', 'upl_admin_init');
add_action('admin_init', 'upl_auth_write');
add_action('admin_menu', 'upl_admin_menu');
add_action('wp_ajax_upl_upload_auth', 'upl_auth_init');

function upl_admin_menu() {
    add_menu_page('Flickr Uploader', 'Flickr Uploader', 'activate_plugins', 'upl_plugin_page', 'upl_admin_html_page');
    add_submenu_page('upl_plugin_page', 'Default Settings | Flickr Uploader', 'Default Settings', 'activate_plugins', 'upl_plugin_page', 'upl_admin_html_page');
   
    
    // adds "Settings" link to the plugin action page
    add_filter( 'plugin_action_links', 'upl_add_settings_links', 10, 2);
}

function upl_add_settings_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__)) . '/index.php' ) {
        $settings_link = '<a href="plugins.php?page=upl_plugin_page">' . 'Settings</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

function upl_admin_init() {
    register_setting('upl_settings_group', 'afg_api_key');
    register_setting('upl_settings_group', 'afg_user_id');
    register_setting('upl_settings_group', 'afg_api_secret');
    register_setting('upl_settings_group', 'afg_auth_token');  
    register_setting('upl_settings_group', 'afg_flickr_token');    
}

function upl_get_all_options() {
    return array(
        'afg_api_key' => get_option('afg_api_key'),
        'afg_user_id' => get_option('afg_user_id'),
        'afg_api_secret' => get_option('afg_api_secret'),
        'afg_auth_token' => get_option('afg_auth_token'),
        'afg_flickr_token' => get_option('afg_flickr_token'),
    );
}

function upl_auth_init() {
    session_start();
    global $bhfpf;
    unset($_SESSION['uplFlickr_auth_token']);
    $bhfpf->setToken('');
    $bhfpf->auth('write', $_SERVER['HTTP_REFERER']);
    exit;
}

function upl_auth_read() {
    if ( isset($_GET['frob']) ) {
        global $bhfpf;
        print_R($_GET);
        $auth = $bhfpf->auth_getToken($_GET['frob']);
        //print_R($auth);
        update_option('afg_flickr_token', $auth['token']['_content']);
        $bhfpf->setToken($auth['token']['_content']);
        //exit;
        header('Location: ' . $_SESSION['uplFlickr_auth_redirect']);
        exit;
    }
}

create_uplFlickr_obj();

function upl_admin_html_page() {

?>
   <div class='wrap'>
  

<?php
    if ($_POST) {
        global $bhfpf;
        print_R($_POST);
        if (isset($_POST['submit']) && $_POST['submit'] == 'Save Changes') {
            update_option('afg_api_key', $_POST['afg_api_key']);
            if (!$_POST['afg_api_secret'] || $_POST['afg_api_secret'] != get_option('afg_api_secret'))
                update_option('afg_flickr_token', '');
            update_option('afg_api_secret', $_POST['afg_api_secret']);
            update_option('afg_user_id', $_POST['afg_user_id']);      
            echo "<div class='updated'><p><strong>Settings updated successfully.</br></br><font style='color:red'>Important Note:</font> If you have installed a caching plugin (like WP Super Cache or W3 Total Cache etc.), you may have to delete your cached pages for the settings to take effect.</strong></p></div>";
            if (get_option('afg_api_secret') && !get_option('afg_flickr_token')) {
                echo "<div class='updated'><p><strong>Click \"Grant Access\" button to authorize Flickr uploader access to upload to your Flickr account.</strong></p></div>";
            } 
        }
        create_afgFlickr_obj(); 
    }
    $url=$_SERVER['REQUEST_URI'];   
?>
    <form method='post' action='<?php echo $url ?>'>
        <div class="postbox-container" style="width:69%; margin-right:1%">
            <div id="poststuff">
             <div class="postbox" style='box-shadow:0 0 2px'>
                <h3>Flickr Settings</h3>
                <table class='form-table'>
                   <tr valign='top'>
                      <th scope='row'>Flickr API Key</th>
                      <td style='width:28%'><input type='text' name='afg_api_key' size='30' value="<?php echo get_option('afg_api_key'); ?>" ><font style='color:red; font-weight:bold'>*</font></input> </td>
                      <td><font size='2'>Don't have a Flickr API Key?  Get it from <a href="http://www.flickr.com/services/api/keys/" target='blank'>here.</a> Go through the <a href='http://www.flickr.com/services/api/tos/'>Flickr API Terms of Service.</a></font></td>
                   </tr>
                        <th scope='row'>Flickr API Secret</th>
                   <td style="vertical-align:top"><input type='text' name='afg_api_secret' id='afg_api_secret' value="<?php echo get_option('afg_api_secret'); ?>"/>
                    <br /><br />
            <?php if (get_option('afg_api_secret')) { 
            if (get_option('afg_flickr_token')) { echo "<input type='button' class='button-secondary' value='Access Granted' disabled=''"; } else {
            ?>
            <input type="button" class="button-primary" value="Grant Access" onClick="document.location.href='<?php echo get_admin_url() .  'admin-ajax.php?action=upl_upload_auth'; ?>';"/>
                <?php }}
            else {
            echo "<input type='button' class='button-secondary' value='Grant Access' disabled=''";    
            } ?>
                   </td>
                   <td style="vertical-align:top"><font size='2'><b>ONLY</b> If you want to include your <b>Private Photos</b> in your galleries, enter your Flickr API Secret here
                    and click Save Changes.</font>
                </td>
            </tr>

                   <tr valign='top'>
                      <th scope='row'>Flickr User ID</th>
                      <td><input type='text' name='afg_user_id' size='30' value="<?php echo get_option('afg_user_id'); ?>" /><font style='color:red; font-weight:bold'>*</font> </td>
                      <td><font size='2'>Don't know your Flickr User ID?  Get it from <a href="http://idgettr.com/" target='blank'>here.</a></font></td>
                   </tr>
                </table>
             </div>
            </div>
            <input type="submit" name="submit" id="bhf_save_changes" class="button-primary" value="Save Changes" />
<?php
    if (BHFDEBUG) {
        //bhfprint_all_options();
    }
?>    
        </div>
    </form>
<?php

}
/*
function bhfprint_all_options() {
    $all_options = bhf_get_all_options();
    foreach($all_options as $key => $value) {
        echo $key . ' => ' . $value . '<br />';
    }
} 
*/           