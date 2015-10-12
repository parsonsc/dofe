<?php
include_once('afg_libs.php');
include_once('afg_edit_galleries.php');
include_once('afg_add_galleries.php');
include_once('afg_saved_galleries.php');
include_once('afg_advanced_settings.php');
include_once('afg_admin_settings.php');
require_once('afgFlickr/afgFlickr.php');

add_action('admin_menu', 'afg_admin_menu4');
add_action( 'admin_enqueue_scripts', 'register_adminceleb_scripts'  );

function register_adminceleb_scripts() {
    wp_register_script( 'ajax-adminreport_scripts-admin', AFGSURL . '/js/imageceleb.js' );
    wp_enqueue_script( 'ajax-adminreport_scripts-admin' );
} 

function afg_admin_menu4() {
    $afg_celeb_page = add_submenu_page('afg_plugin_page', 'Celeb images | Flickr Gallery', 'Celeb images', 'create_users', 'afg_plugin_page4', 'afg_celeb_html_page');
}


create_afgFlickr_obj();

function afg_celeb_html_page() {
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
//echo wp_create_nonce( 'ajax-afg-editimg-nonce-'. $_POST['celeb']) ."<br />\n";
if ($_POST &&  ! wp_verify_nonce( trim($_POST['editnonce']), 'ajax-afg-editimg-nonce-'.  $_POST['celeb'] ) ) {
    die( 'Security check' ); 
}
if ($_POST && wp_verify_nonce( trim($_POST['editnonce']), 'ajax-afg-editimg-nonce-'.  $_POST['celeb'] ) ) {
    $wpdb->show_errors();
    $ret = $wpdb->update( 
        $wpdb->prefix."flickrcelebs", 
        array( 
            'sport' => isset($_POST['sport']) ? $_POST['sport'] : '',
            'gender' => isset($_POST['gender']) ? $_POST['gender'] : '',
            'celebname' => isset($_POST['celebname']) ? $_POST['celebname'] : '',
            'imagetitle' => isset($_POST['imagetitle']) ? $_POST['imagetitle'] : '',
            'imagestory' => isset($_POST['imagestory']) ? $_POST['imagestory'] : '',
            'published' => isset($_POST['published']) ? 1 : 0,
            'unicef' => isset($_POST['unicef']) ? 1 : 0,
            'photoid' => $_POST['celeb'],
        ), 
        array( 'photoid' => $_POST['celeb'] ), 
        array( 
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%s'
        ), 
        array( '%s' )
    );
    if ($ret !== 1 ){
        /*
        print_R($_POST);
        echo $_POST['celeb'];
        echo $_POST['photosecret'];
        echo $_POST['farm'];
        echo $_POST['server'];
    $photo_url = "http://farm{$_POST['farm']}.static.flickr.com/{$_POST['server']}/{$_POST['celeb']}_{$_POST['photosecret']}_s.jpg";
    echo '<img src="'.$photo_url.'"/>';
        */
        $ret = $wpdb->insert( 
            $wpdb->prefix."flickrcelebs", 
            array( 
                'sport' => isset($_POST['sport']) ? $_POST['sport'] : '',
                'celebname' => isset($_POST['celebname']) ? $_POST['celebname'] : '',
                'imagetitle' => isset($_POST['imagetitle']) ? $_POST['imagetitle'] : '',
                'imagestory' => isset($_POST['imagestory']) ? $_POST['imagestory'] : '',
                'published' => isset($_POST['published']) ? 1 : 0,
                'unicef' => isset($_POST['unicef']) ? 1 : 0,
                'photoid' => $_POST['celeb'],
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
                '%d',
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
if (!$_POST && $_GET && wp_verify_nonce( $_REQUEST['nonce'], 'ajax-afg-twinimg-nonce-'.  $_GET['celeb'] ) ) {
?>
                    <h3>Celeb image</h3>       
<?php
    $photoinfo = $pf->photos_getInfo($_REQUEST['celeb']);
    $photo_url = "http://farm{$photoinfo['photo']['farm']}.static.flickr.com/{$photoinfo['photo']['server']}/{$photoinfo['photo']['id']}_{$photoinfo['photo']['secret']}_s.jpg";
    echo '<img src="'.$photo_url.'"/>';
    $celeb = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}flickrcelebs WHERE photoid ='{$photoinfo['photo']['id']}';");
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
$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrsports WHERE published = 1" );    
foreach ( $results as $row ) 
{
?>
                    <option value="<?php echo $row->id; ?>" <?php echo ($row->id == $celeb->sport)? 'selected="selected"':'';?> ><?php echo $row->sport; ?></option>
<?php      
}
?>
                </select>
            </div>  
            <div class="form-item ">
                <label for="celebname">
                    Name <em class="error the-red" id="build_error"></em>
                </label>
                <input type="text" name="celebname" id="celebname" class="form-input" value="<?php echo stripslashes($celeb->celebname); ?>" />            
            </div> 
            <div class="form-item ">
                <label for="gender">
                    Gender <em class="error the-red" id="gender_error"></em>
                </label>
                <select name="gender" id="gender" class="form-input "  >
                    <option value=""></option>               
                    <option value="m" <?php echo ('m' == $celeb->gender)? 'selected="selected"':'';?>>Male</option>
                    <option value="f" <?php echo ('f' == $celeb->gender)? 'selected="selected"':'';?>>Female</option>
                    <option value="n" <?php echo ('n' == $celeb->gender)? 'selected="selected"':'';?>>Not specified</option>              
                </select>
            </div>                 
            <div class="form-item">
                <label for="imagetitle">
                    The Title of your image <em class="error the-red" id="build_error"></em>
                </label>
                <input type="text" name="imagetitle" id="imagetitle" class="form-input" value="<?php echo stripslashes($celeb->imagetitle); ?>" />
            </div> 
            <div class="form-item">
                <label for="imagestory">
                    The story of your image <em class="error the-red" id="imagestory_error"></em>
                </label>
                <textarea name="imagestory" id="imagestory" class="form-input"><?php echo stripslashes($celeb->imagestory); ?></textarea>
            </div>   
            <?php wp_nonce_field('ajax-afg-editimg-nonce-'. $photoinfo['photo']['id'] , 'editnonce'); ?>
            <input type="hidden" name="celeb" value="<?php echo $photoinfo['photo']['id'];?>" />
            <input type="hidden" name="photosecret" value="<?php echo $photoinfo['photo']['secret'];?>" />
            <input type="hidden" name="farm" value="<?php echo $photoinfo['photo']['farm'];?>" />
            <input type="hidden" name="server" value="<?php echo $photoinfo['photo']['server'];?>" />
            <div class="form-item ">
                <label for="unicef">
                    Unicef Celebrity <em class="error the-red" id="published_error"></em>
                <input type="checkbox" name="unicef" id="unicef" class="form-input" value="1" <?php echo ('1' == $celeb->unicef)? 'checked="checked"':'';?> />
                </label>
            </div>            
            <div class="form-item ">
                <label for="published">
                    Published <em class="error the-red" id="published_error"></em>
                <input type="checkbox" name="published" id="published" class="form-input" value="1" <?php echo ('1' == $celeb->published)? 'checked="checked"':'';?> />
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
                    <h3>Celeb images</h3>       
<?php
    global $pf;
    $extras = 'url_l, description, date_upload, date_taken, owner_name';
    if (trim(get_option('afg_celebs_photoset')) != '') {
        $rsp_obj = $pf->photosets_getPhotos(get_option('afg_celebs_photoset'), $extras, NULL, 500);
        if (!$rsp_obj) echo afg_error();
        else {
            foreach($rsp_obj['photoset']['photo'] as $photo) {
                $photo_url = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
                $linkurl = $url . '&celeb=' . $photo['id'] . '&nonce=' . wp_create_nonce( 'ajax-afg-twinimg-nonce-'. $photo['id'] );
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
    if (DEBUG) {
        print_all_options();
    }
}    
?>    