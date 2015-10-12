<?php
include_once('afg_libs.php');
include_once('afg_edit_galleries.php');
include_once('afg_add_galleries.php');
include_once('afg_saved_galleries.php');
include_once('afg_advanced_settings.php');
include_once('afg_admin_settings.php');
require_once('afgFlickr/afgFlickr.php');

add_action('admin_menu', 'afg_admin_menu3');
add_action( 'admin_enqueue_scripts', 'register_admintwin_scripts'  );

function register_admintwin_scripts() {
    wp_register_script( 'ajax-adminreport_scripts-admin', AFGSURL . '/js/imagetwins.js' );
    wp_enqueue_script( 'ajax-adminreport_scripts-admin' );
} 

function afg_admin_menu3() {
    $afg_twins_page = add_submenu_page('afg_plugin_page', 'Twin images | Flickr Gallery', 'Twin images', 'create_users', 'afg_plugin_page3', 'afg_twins_html_page');
}


create_afgFlickr_obj();

function afg_twins_html_page() {
    global $afg_photo_size_map, $afg_on_off_map, $afg_descr_map, 
        $afg_columns_map, $afg_bg_color_map, $afg_width_map, $pf,
        $afg_sort_order_map, $afg_slideshow_map, $wpdb;
?>
   <div class='wrap'>
   <h2><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Flickr Gallery Settings</h2>
<?php
    $url_parts = parse_url($_SERVER['REQUEST_URI']);
    $url =(isset($url_parts['path'])?$url_parts['path']:'') . (isset($url_parts['query']) ? '?'.$url_parts['query']:'');
?>
    <form method='post' action='<?php echo $url ?>'>
        <?php echo afg_generate_version_line() ?>
        <div class="postbox-container" style="width:69%; margin-right:1%">
            <div id="poststuff">
                <div class="postbox" style='box-shadow:0 0 2px'>
<?php
//echo $_POST['nonce'] ."<br />\n";
//echo wp_create_nonce( 'ajax-afg-editimg-nonce-'. $_POST['twin']) ."<br />\n";
if ($_POST &&  ! wp_verify_nonce( trim($_POST['editnonce']), 'ajax-afg-editimg-nonce-'.  $_POST['twin'] ) ) {
    die( 'Security check' ); 
}
if ($_POST && wp_verify_nonce( trim($_POST['editnonce']), 'ajax-afg-editimg-nonce-'.  $_POST['twin'] ) ) {
    $wpdb->show_errors();
    $ret = $wpdb->update( 
        $wpdb->prefix."flickrtwins", 
        array( 
            'sport' => isset($_POST['sport']) ? $_POST['sport'] : '',
            'age' => isset($_POST['age']) ? $_POST['age'] : '',
            'gender' => isset($_POST['gender']) ? $_POST['gender'] : '',
            'imagetitle' => isset($_POST['imagetitle']) ? $_POST['imagetitle'] : '',
            'imagestory' => isset($_POST['imagestory']) ? $_POST['imagestory'] : '',
            'published' => isset($_POST['published']) ? 1 : 0,
            'photoid' => $_POST['twin'],
        ), 
        array( 'photoid' => $_POST['twin'] ), 
        array( 
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d'
        ), 
        array( '%s' )
    );
    if ($ret !== 1 ){
        $ret = $wpdb->insert( 
            $wpdb->prefix."flickrtwins", 
            array( 
                'sport' => isset($_POST['sport']) ? $_POST['sport'] : '',
                'age' => isset($_POST['age']) ? $_POST['age'] : '',
                'gender' => isset($_POST['gender']) ? $_POST['gender'] : '',
                'imagetitle' => isset($_POST['imagetitle']) ? $_POST['imagetitle'] : '',
                'imagestory' => isset($_POST['imagestory']) ? $_POST['imagestory'] : '',
                'published' => isset($_POST['published']) ? 1 : 0,
                'photoid' => $_POST['twin'],
                'photosecret' => $_POST['photosecret'],
                'farm' => $_POST['farm'],
                'server' => $_POST['server'],
                'submittedtime' => time()
            ),
            array( 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            )
        );
        if ($ret === False){ echo '<h4>Insert failed</h4>';}
        else { echo '<h4>Inserted</h4>';}
    }    
    else{echo '<h4>Updated</h4>';}
    //exit( var_dump( $wpdb->last_query ) );
}
if (!$_POST && $_GET && wp_verify_nonce( $_REQUEST['nonce'], 'ajax-afg-twinimg-nonce-'.  $_GET['twin'] ) ) {
?>
                    <h3>Twin image</h3>       
<?php
    $photoinfo = $pf->photos_getInfo($_REQUEST['twin']);
    $photo_url = "http://farm{$photoinfo['photo']['farm']}.static.flickr.com/{$photoinfo['photo']['server']}/{$photoinfo['photo']['id']}_{$photoinfo['photo']['secret']}_s.jpg";
    echo '<img src="'.$photo_url.'"/>';
    $twin = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."flickrtwins WHERE photoid ='". $photoinfo['photo']['id'] ."';");
    //var_dump( $wpdb->last_query );
    //print_R($twin);
    
?>
            <div class="form-item ">
                <label for="sport">
                    Sport <em class="error the-red" id="sport_error"></em>
                </label>
                <select name="sport" id="sport" class="form-input " />
                    <option value=""></option>
<?php      
$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."flickrsports WHERE published = 1" );    
foreach ( $results as $row ) 
{
?>
                    <option value="<?php echo $row->id; ?>" <?php echo ($row->id == $twin->sport)? 'selected="selected"':'';?> ><?php echo $row->sport; ?></option>
<?php      
}
?>
                </select>
            </div>  
            <div class="form-item ">
                <label for="age">
                    Age <em class="error the-red" id="age_error"></em>
                </label>
                <select name="age" id="age" class="form-input "  >
                    <option value="1" <?php echo (intval($twin->age) == 1) ? 'selected="selected"':''; ?>>0-3</option>
                    <option value="2" <?php echo (intval($twin->age) == 2) ? 'selected="selected"':''; ?>>4-7</option>
                    <option value="3" <?php echo (intval($twin->age) == 3) ? 'selected="selected"':''; ?>>8-12</option>
                    <option value="4" <?php echo (intval($twin->age) == 4) ? 'selected="selected"':''; ?>>13-15</option>
                    <option value="5" <?php echo (intval($twin->age) == 5) ? 'selected="selected"':''; ?>>16+</option>                
                </select>
            </div> 
            <div class="form-item ">
                <label for="gender">
                    Gender <em class="error the-red" id="gender_error"></em>
                </label>
                <select name="gender" id="gender" class="form-input "  >
                    <option value=""></option>               
                    <option value="m" <?php echo ('m' == $twin->gender)? 'selected="selected"':'';?>>Male</option>
                    <option value="f" <?php echo ('f' == $twin->gender)? 'selected="selected"':'';?>>Female</option>
                    <option value="n" <?php echo ('n' == $twin->gender)? 'selected="selected"':'';?>>Not specified</option>              
                </select>
            </div>                 
            <div class="form-item">
                <label for="imagetitle">
                    The Title of your image <em class="error the-red" id="build_error"></em>
                </label>
                <input type="text" name="imagetitle" id="imagetitle" class="form-input" value="<?php echo stripslashes($twin->imagetitle); ?>" />
            </div> 
            <div class="form-item">
                <label for="imagestory">
                    The story of your image <em class="error the-red" id="imagestory_error"></em>
                </label>
                <textarea name="imagestory" id="imagestory" class="form-input"><?php echo stripslashes($twin->imagestory); ?></textarea>
            </div>   
            <?php wp_nonce_field('ajax-afg-editimg-nonce-'. $photoinfo['photo']['id'] , 'editnonce'); ?>
            <input type="hidden" name="twin" value="<?php echo $photoinfo['photo']['id'];?>" />
            <input type="hidden" name="photosecret" value="<?php echo $photoinfo['photo']['secret'];?>" />
            <input type="hidden" name="farm" value="<?php echo $photoinfo['photo']['farm'];?>" />
            <input type="hidden" name="server" value="<?php echo $photoinfo['photo']['server'];?>" />
            <div class="form-item ">
                <label for="published">
                    Published <em class="error the-red" id="published_error"></em>
                <input type="checkbox" name="published" id="published" class="form-input" value="1" <?php echo ('1' == $twin->published)? 'checked="checked"':'';?> />
                </label>
            </div>
            <div class="form-item ">
                <button type="submit" value="save">
                    Save                
                </button>
            </div>             
<?php    
}    
else{    
?>
                    <h3>Twin images</h3>       
<?php
    global $pf;
    $extras = 'url_l, description, date_upload, date_taken, owner_name';
    if (trim(get_option('afg_twins_photoset')) != '') {
        $rsp_obj = $pf->photosets_getPhotos(get_option('afg_twins_photoset'), $extras, NULL, 500);
        if (!$rsp_obj) echo afg_error();
        else {
            foreach($rsp_obj['photoset']['photo'] as $photo) {
                $photo_url = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
                $linkurl = $url . '&twin=' . $photo['id'] . '&nonce=' . wp_create_nonce( 'ajax-afg-twinimg-nonce-'. $photo['id'] );
                echo '<a href="'. $linkurl .'"><img src="'.$photo_url.'"/></a>';
            }
        }
    }
}    
?>
                </div> 
            </div>
        </div>
    </form>
<?php

}    
?>    