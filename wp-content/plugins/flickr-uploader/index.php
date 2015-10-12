<?php
/*
   Plugin Name: Flickr Uploader
 */

define('BHFSURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('BHFPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );

//Include uplFlickr
require_once("uplFlickr/uplFlickr.php");
include_once('upl_libs.php');
include_once('admin_settings.php');
$error=0;
$f = null;
$token = '';

if (!is_admin()) {
    /* Short code to load Awesome Flickr Gallery plugin.  Detects the word
     * [AFG_gallery] in posts or pages and loads the gallery.
     */
    add_shortcode('FLICKR_uploader', 'uplflickr_shortcode_func');
    add_action('wp_enqueue_scripts', 'uplflickr_enqueuescripts');  
    add_action( 'wp_ajax_nopriv_ajaxupl_flickr_send', 'ajaxupl_flickr_send' );
    add_action( 'wp_ajax_ajaxupl_flickr_send', 'ajaxupl_flickr_send' );    
}

function uplflickr_enqueuescripts(){
    wp_enqueue_script('flickr-uploader', FLICKRSURL.'/js/uplflickr.js', array('jquery'));
    wp_localize_script( 'flickr-uploader', 'ajaxuplflickr', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function uplflickr_shortcode_func( $atts ){
    extract( shortcode_atts( array(
        'id' => '0',
    ), $atts ) );
    
    ob_start();
    if (isset($_FILES["yourfile"]) && $_FILES["yourfile"]){
        $ret = ajaxupl_flickr_send();
        if (trim($ret) ==''){
            upl_display_thanks();
        }
        else{
            upl_display_form($ret);
        }
    }
    else{
        upl_display_form();
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function upl_display_thanks() {
?>
    <div class="flickr-upload">
        <h3>Thanks</h3>
        <p>Thanks for sharing your piccy with us. It will be checked and verified by someone sensible before we can upload it. It won&rsquo;t take long &ndash; so check again here soon.</p>

        <p>Got another photograph? <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Upload it now!</a></p>
    </div>                     
<?php
}

function upl_display_form($err="") {
$username = "";
$email = "";
if (is_user_logged_in()){
    global $current_user;
    get_currentuserinfo();        
    $username = $current_user->user_firstname.' '. $current_user->user_lastname;
    $email = $current_user->user_email;
}
?><form class="flickr-upload" id="ajaxuplflickr" action="" method="post" enctype="multipart/form-data">
        <h3>Send us your photos</h3>
        <p class="error the-red"><?php echo $err; ?></p>
        <!-- <?php echo $_SERVER['REQUEST_URI']; ?> -->
        <div class="grid-50 mobile-grid-100">
            <div class="form-item">
                <label for="yourname">
                Your name as it will appear with the image <em class="error the-red" id="name_error"></em>
                </label>
                <input type="text" name="yourname" id="yourname" class="form-input" value="<?php echo $username; ?>" />
            </div>
            <div class="form-item wider">
                <label for="youremail">
                Your email address <em class="error the-red" id="email_error"></em>
                </label>
                <input type="email" name="youremail" id="youremail" class="form-input " value="<?php echo $email; ?>" />
            </div>
        </div>
        <div class="form-item grid-50 mobile-grid-100 file-input-wrapper">
            <label for="yourfile">
            Your photo (Files must be <?php echo getMaxUploadSizeup();?> or less) <em class="error the-red" id="file_error"></em>
            </label>
            <div class="fileUpload btn btn-primary">
                <!-- span>Choose Photo</span -->
                <input type="file" class="upload" name="yourfile" id="yourfile" multiple onchange="this.style.color = '#000000';" />
            </div>
            <label for="tsandcs" class="tsandcs">
            <input type="checkbox" name="tsandcs" id="tsandcs" value="1" />I agree with the <a href="<?php echo get_permalink(171); ?>#imagespolicy">terms and conditions</a> <em class="error the-red" id="tsandcs_error"></em>
            </label>
            <button type="submit" class="button"><span class="small-red-with-varient-arrow">I&rsquo;m ready to send it</span></button>
        </div>
    </form><?php
}

function ajaxupl_flickr_send(){
    global $bhfpf;
    
    $results = '';
    $error = 0;
    
    /* Check if both name and file are filled in */
    if(!$_POST['yourname'] || !$_FILES["yourfile"]["name"]){
        $error=1;
        $results = "You must enter your name and a file to upload";
    }
    else if(!isset($_POST['tsandcs']) || intval($_POST['tsandcs']) != 1){
        $error=1;
        $results = "You must accept the terms and conditions";           
    }
    else{
        /* Check if there is no file upload error */
        if ($_FILES["yourfile"]["error"] > 0){
            $error=1;
            $results = "Error: " . $_FILES["yourfile"]["error"];
        }else if($_FILES["yourfile"]["type"] != "image/jpg" && $_FILES["yourfile"]["type"] != "image/jpeg" && $_FILES["yourfile"]["type"] != "image/png" && $_FILES["yourfile"]["type"] != "image/gif"){
            /* Filter all bad file types */
            $error = 1;
            $results = "Not an image file " . $_FILES["yourfile"]["type"];
        }else if(intval($_FILES["yourfile"]["size"]) > getMaxUploadSizeRawup()){
            $error = 1;
            $results = "File too big";
        }else{
            $name = $_POST['yourname'];
            $email = $_POST['youremail'];
            $tsandcs = $_POST['tsandcs'];        
            $dir= dirname($_FILES["yourfile"]["tmp_name"]);
            $newpath=$dir."/".$_FILES["yourfile"]["name"];
            rename($_FILES["yourfile"]["tmp_name"],$newpath);
            $description = $name ."\n". $email ."\n". $tsandcs;
            /* Call uploadPhoto on success to upload photo to flickr */
            $status = uploadPhotoUP($newpath, $name, $description);
            if(!$status) {
                $error = 1;
                $results = "Something has gone wrong";
            }
        }
    }
    return $results;
}
  
if(!function_exists('getMaxUploadSizeup')){ 
function getMaxUploadSizeup(){
    $max_upload    = (ini_get('upload_max_filesize'));
    $max_post      = (ini_get('post_max_size'));
    $memory_limit  = (ini_get('memory_limit'));
    $size = min($max_upload, $max_post, $memory_limit);
    if (preg_match('/^(\d+)(.)$/', $size, $matches)) {
        if ($matches[2] == 'M') {
            $size = $matches[1] .'MB'; // nnnM -> nnn MB
        } else if ($matches[2] == 'K') {
            $size = $matches[1] .'kB'; // nnnK -> nnn KB
        }
    }   
    return $size;  
}
}
if(!function_exists('getMaxUploadSizeRawup')){ 
function getMaxUploadSizeRawup(){
    $max_upload    = (ini_get('upload_max_filesize'));
    $max_post      = (ini_get('post_max_size'));
    $memory_limit  = (ini_get('memory_limit'));
    $size = min($max_upload, $max_post, $memory_limit);
    if (preg_match('/^(\d+)(.)$/', $size, $matches)) {
        if ($matches[2] == 'M') {
            $size = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
        } else if ($matches[2] == 'K') {
            $size = $matches[1] * 1024; // nnnK -> nnn KB
        }
    }   
    return $size;
}
}
if(!function_exists('uploadPhotoUP')){ 
function uploadPhotoUP($path, $title, $description) {   
    global $bhfpf ;
    create_uplFlickr_obj();
    return $bhfpf->async_upload($path, $title, $description, null, 0, 0, 0, 2);
}
}