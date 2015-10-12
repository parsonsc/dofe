<?php
/*
   Plugin Name: Flickr Gallery DOE

 */

/*
I've messed with this thing so much it bears no relation to the original - do NOT update
*/

define('AFGSURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('AFGPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );

add_filter('site_transient_update_plugins', 'afg_remove_update_nag');
function afg_remove_update_nag($value) {
    unset($value->response[ plugin_basename(__FILE__) ]);
    return $value;
}

global $afgflickr_db_version;
$afgflickr_db_version = "1.0";

register_activation_hook( __FILE__, 'afgflickr_install' );

require_once('afgFlickr/afgFlickr.php');
include_once('afg_admin_settings.php');
include_once('afg_libs.php');
include_once('afg_update.php');
if (is_admin()){
    include_once('afg_reported_images.php');
    /*include_once('afg_import_twins.php');
    include_once('afg_import_celebs.php');
    include_once('afg_sports.php');*/
}

$useSMTP = True; //defaults to using local mail if set to false
$smtp_server = "smtp.mailgun.org";
$smtp_helo = "sportfirst.unicef.org.uk";
$smtp_uname = "postmaster@sportfirst.unicef.org.uk";
$smtp_pword = "4p-84mc13zi1";
$mailer_id = "SportFirst";
$marshall = "sportfirst@unicef.org.uk";
$email_from = "sportfirst@sportfirst.unicef.org.uk";
$reply_to = "sportfirst@unicef.org.uk";
$friendly_name = "SportFirst";

function sendemail($email, $name, $vars , $page = 0 ){

	
    global $useSMTP, $smtp_server, $smtp_helo, $smtp_uname, $smtp_pword, $mailer_id;
    global $email_from, $reply_to, $friendly_name, $thisUrl;
    //$emailSubj = sprintf("Can you become a Master in self-control %s?", $vars['firstname']);

    include_once(AFGPATH.'/includes/class.html.mime.mail.inc');
    define('CRLF', "\r\n", TRUE);
    $mail = new html_mime_mail(array("X-Mailer: $mailer_id"));
    if ($smtp_uname != "" || $smtp_pword != "")
        $smtpauth = TRUE;
    else
        $smtpauth = FALSE;
	if (CODEDEBUG){
		//file_put_contents(AFGPATH.'/file.log', AFGPATH.'/'.$vars['email'].'.txt'.AFGPATH.'/'.$vars['email'].'.html', FILE_APPEND); 
	}
    if (file_exists(AFGPATH.'/'.$vars['email'].'.txt') && file_exists(AFGPATH.'/'.$vars['email'].'.html')){
        $email_body = fread($fp = fopen(AFGPATH.'/'.$vars['email'].'.txt', 'r'), filesize(AFGPATH.'/'.$vars['email'].'.txt'));
        fclose($fp);
        $html_email_body = fread($fp = fopen(AFGPATH.'/'.$vars['email'].'.html', 'r'), filesize(AFGPATH.'/'.$vars['email'].'.html'));
        fclose($fp);    
        
        foreach ($vars as $key => $val){
            if(strpos($email_body, '['.trim($key).']')){
                $email_body = preg_replace("/\[$key\]/", stripslashes($val), $email_body);
            }   
        }
        $email_body = preg_replace('/\[(\S.*?)\]/', '', $email_body);    
        if ($html_email_body){
            foreach ($vars as $key => $val){
                if(strpos($html_email_body, '['.trim($key).']')){
                    $html_email_body = preg_replace("/\[$key\]/", stripslashes($val), $html_email_body);
                }
            }
            $html_email_body = preg_replace('/\[(\S.*?)\]/', '', $html_email_body);     
            $mail->add_html($html_email_body, $email_body, AFGPATH.'/emails/');
            //$mail->add_html($html_email_body, $email_body, 'img');
        }
        else{
            $mail->add_text($email_body);
        }

        $mail->build_message();
        $subject = '';
        if ($vars['email'] == 'emails/email-thankyou') $subject = 'Thank you';
        if ($vars['email'] == 'emails/email-thankyou2') $subject = 'Thank you';
        if ($vars['email'] =='emails/email-flag') $subject = 'Flagged image';
        $headers = array(
            "From: \"$friendly_name\" <$email_from>",
            "To: \"$name\" <$email>",	// A To: header is necessary, but does
            "Subject: ". $subject,		// not have to match the recipients list.
            "Reply-To: $reply_to"
        ); 
        if ($useSMTP){
            include_once(AFGPATH.'/includes/class.smtp.inc');
            $params = array(
                'host' => $smtp_server,	// Mail server address
                'port' => 25,		    // Mail server port
                'helo' => $smtp_helo,	// Use your domain here.
                'auth' => $smtpauth,	// Whether to use authentication or not.
                'user' => $smtp_uname,	// Authentication username
                'pass' => $smtp_pword	// Authentication password
            );
            //echo '<PRE>'.htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)).'</PRE>';               
            $smtp =& smtp::connect($params);
            $send_params = array(
                'from'		=> $email_from,	// The return path
                'recipients'	=> $email,	// Can be more than one address in this array.
                'headers'	=> $headers);       
            $mail->smtp_send($smtp, $send_params);
        }
        else{
            $mail->send($name, $email, $friendly_name, $email_from, $emailSubj, $headers);
        }
        /*
		if (CODEDEBUG){	        
			$content = print_r(htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)), true);
			file_put_contents(AFGPATH.'/file.log', $content, FILE_APPEND); 
		}
		*/
    }
}

function afg_enqueue_cbox_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('afg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox-min.js" , array('jquery'),'', true);
    wp_enqueue_script('afg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js" , array('jquery'), '', true);
}

function afg_enqueue_cbox_styles() {
    wp_enqueue_style('afg_colorbox_css', BASE_URL . "/colorbox/colorbox.css");
}

function afg_enqueue_googleimage_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('modernizr');
    wp_enqueue_script('afg_modernizr_js', BASE_URL . "/googleimage/js/modernizr.custom.js", array(),'', true);
    wp_enqueue_script('afg_googleimage_js', BASE_URL . "/googleimage/js/googleimage.js" , array('jquery'), '', true);
    wp_enqueue_script('afg_infinitescroll_js', BASE_URL . "/js/jquery.infinitescroll.js" , array('jquery'), '', true);
}

function afg_enqueue_googleimage_styles() {
    wp_enqueue_style('afg_googleimage_css', BASE_URL . "/googleimage/css/googleimage.css");
}

function afg_enqueue_highslide_scripts() {
    wp_enqueue_script('afg_highslide_js', BASE_URL . "/highslide/highslide-full.min.js", array(),'', true);
}

function afg_enqueue_highslide_styles() {
    wp_enqueue_style('afg_highslide_css', BASE_URL . "/highslide/highslide.css");
}

function afg_enqueue_styles() {
    wp_enqueue_style('afg_css', BASE_URL . "/afg.css");
}

$enable_colorbox = get_option('afg_slideshow_option') == 'colorbox';
$enable_highslide = get_option('afg_slideshow_option') == 'highslide';
$enable_googleimage = get_option('afg_slideshow_option') == 'googleimage';

add_action('wp_ajax_ajaxafg_flickr_send', 'ajaxafg_flickr_send' );   
add_action('wp_ajax_nopriv_ajaxafg_flickr_send', 'ajaxafg_flickr_send' );
add_action('wp_ajax_afg_report_images', 'ajax_afg_report_images');
add_action('wp_ajax_nopriv_afg_report_images', 'ajax_afg_report_images' );

if (!is_admin()) {
    /* Short code to load Awesome Flickr Gallery plugin.  Detects the word
     * [AFG_gallery] in posts or pages and loads the gallery.
     */

    add_shortcode('AFG_gallery', 'afg_display_gallery');
    add_shortcode('AFG_uploader', 'afg_upload_form');
    add_action('wp_enqueue_scripts', 'afgflickr_enqueuescripts');  
    //add_filter('language_attributes', 'add_opengraph_doctype');
    //add_action( 'wp_head', 'insert_fbimage_in_head', 10, 1 );    
    add_filter('widget_text', 'do_shortcode', 11);

    $galleries = get_option('afg_galleries');
    foreach ($galleries as $gallery) {
        if ($gallery['slideshow_option'] == 'colorbox') {
            $enable_colorbox = true;
            break;
        }
    }
    foreach ($galleries as $gallery) {
        if ($gallery['slideshow_option'] == 'highslide') {
            $enable_highslide = true;
            break;
        }
    }
    foreach ($galleries as $gallery) {
        if ($gallery['slideshow_option'] == 'googleimage') {
            $enable_googleimage = true;
            break;
        }
    }
    if ($enable_colorbox) {
        add_action('wp_print_scripts', 'afg_enqueue_cbox_scripts');
        add_action('wp_print_styles', 'afg_enqueue_cbox_styles');
    }
    if ($enable_highslide) {
        add_action('wp_print_scripts', 'afg_enqueue_highslide_scripts');
        add_action('wp_print_styles', 'afg_enqueue_highslide_styles');
    }
    if ($enable_googleimage) {
        add_action('wp_print_scripts', 'afg_enqueue_googleimage_scripts');
        add_action('wp_print_styles', 'afg_enqueue_googleimage_styles');
    }
    add_action('wp_print_styles', 'afg_enqueue_styles');
    add_filter('wpseo_opengraph_url', 'asdgallery_my_og_url');
    add_filter('wpseo_opengraph_image', 'asdgallery_my_og_image');
    add_filter('wpseo_opengraph_desc', 'asdgallery_my_og_desc');
}

function asdgallery_my_og_desc($ogdesc) {
    if (isset($_REQUEST['showtwin']) && $_REQUEST['showtwin'] == 1 && intval($_REQUEST['id']) > 0  ){
        $ogdesc = urlencode("I've added a Sport First moment to be screened at the Commonwealth Games in Glasgow, thanks to UNICEF. Join me - on your marks, get set, upload!");
    }
    return $ogdesc;
}

function asdgallery_my_og_url($url) {
    // this post meta has to be set up and filled by you!
    if ($url != home_url() && strpos($url, home_url()) !== False && isset($_REQUEST['id'])) {
	    //file_put_contents(AFGPATH.'/file.log', esc_attr(esc_url(add_query_arg( array('id' => $_REQUEST['id']), get_home_url() ))), FILE_APPEND); 
        return esc_attr(esc_url(add_query_arg( array('id' => $_REQUEST['id']), get_home_url() )));        
    }    
}

function asdgallery_my_og_image($img) {
    global $wpdb;
    $img = get_template_directory_uri() . '/images/facebook-logo.jpg';
    if ($_REQUEST['showtwin'] == 1 ){
        $row =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}flickruploads  WHERE status <> '-1' AND id='".intval($_REQUEST['id'])."'" );          
        if  ($row->id !== null){
            $img = afg_get_photo_url($row->flatfarm, $row->flatserver,
                    $row->flatid, $row->flatsecret, null); 
        }
    }
    //file_put_contents(AFGPATH.'/file.log', $img, FILE_APPEND); 
    return $img;
}

function ajax_afg_report_images() {
    global $pf, $wpdb, $marshall;
    // First, check the nonce to make sure it matches what we created when displaying the message.
    // If not, we won't do anything.
    
    $ret = $wpdb->update( 
        $wpdb->prefix."flickruploads", 
        array( 
            'status' => 1
        ), 
        array( 'id' => $_REQUEST['report'] ), 
        array( 
            '%d'
        ),
        array( 
            '%s'
        )
    );
    $vars = array(
    	'email' => 'emails/email-flag',
    	'subject' => 'Complained',
        'reportedurl' => add_query_arg( array('id' => $_REQUEST['report']), get_home_url() )
	);
    sendemail($marshall, 'The Referee', $vars, 0);       
    if ($ret === False ){
        die( 'failed to update image' . $ret  );
    }
    die( '1' );
} // end hide_admin_notification

//Adding the Open Graph in the Language Attributes

function add_opengraph_doctype( $output ) {
    return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}

//Lets add Open Graph Meta Info
function insert_fbimage_in_head($img) {

    //echo '<meta property="og:image" content="' . esc_attr( $img ) . '"/>';
    /*
    global $post;
    if ( !is_singular()) //if it is not a post or a page
        return;
    echo '<meta property="fb:admins" content="YOUR USER ID"/>';
    echo '<meta property="og:title" content="' . get_the_title() . '"/>';
    echo '<meta property="og:type" content="article"/>';
    echo '<meta property="og:url" content="' . get_permalink() . '"/>';
    echo '<meta property="og:site_name" content="Your Site NAME Goes HERE"/>';
    if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
        $default_image="http://example.com/image.jpg"; //replace this with a default image on your server or an image in your media library
        echo '<meta property="og:image" content="' . $default_image . '"/>';
    }
    else{
        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
    }
    */
    //echo "\n";    
}

function afg_display_thanks($ret) {
    global $wpdb;

    $photo_size='_o';
    $row = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix . "flickruploads WHERE id = '" .$ret['insertid']. "'");
    $twin = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix . "flickrtwins WHERE id = '" .$ret['match']. "'");                    
    $imageyour = afg_get_photo_url(
        $ret['photouploaded']['farm'], 
        $ret['photouploaded']['server'], 
        $ret['photouploaded']['id'], 
        $ret['photouploaded']['secret'], '_n');
    //do_action( 'insert_fbimage_in_head',  $imageyour );
    $imagetwin = afg_get_photo_url(
        $ret['matchedwith']['farm'], 
        $ret['matchedwith']['server'], 
        $ret['matchedwith']['id'], 
        $ret['matchedwith']['secret'], '_n');   
    $twinimg = afg_get_photo_url($row->flatfarm, $row->flatserver,
                    $row->flatid, $row->flatsecret, null);         
?>
<!-- Facebook Conversion Code for CWG Sportfirst -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6014046149022', {'value':'0.01','currency':'GBP'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6014046149022&amp;cd[value]=0.01&amp;cd[currency]=GBP&amp;noscript=1" /></noscript>

<script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
<script type="text/javascript">
twttr.conversion.trackPid('l4mu5');
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=l4mu5&p_id=Twitter" />
<img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=l4mu5&p_id=Twitter" />
</noscript>


<script type="application/javascript">
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : '801984773168530',                            
      status     : true,                                 
      xfbml      : true                                  
    });
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));

function FBShareOp(){
/*
	var product_name   = 	'UNICEF - SportFirst';
	var description	   =	"I've added a Sport First moment to be screened at the Commonwealth Games in Glasgow, thanks to UNICEF. Join me - on your marks, get set, upload!";
	var share_image	   =	'<?php echo $twinimg; ?>';
	var share_url	   =	'<?php echo add_query_arg( array('id' => $ret['insertid'],'showtwin' => '1'), get_home_url() );?>';	
	var home_url	   =	'<?php echo add_query_arg( array(), get_home_url() );?>';	
    var share_capt     =    'UNICEF - SportFirst';
    FB.ui({
        method: 'stream.publish',
        display: 'popup',
        attachment: {
            name: product_name,
            caption: share_capt,
            description: description,
            href: share_url,
            media:[{"type":"image", "src":share_image, width: '528', height: '340', "href": share_url}],
        },
        action_links: [{ text: 'Upload a Sport First', href: home_url }]        
    }, function(response) {
        if(response && response.post_id){}
        else{}
    });
*/    

	var product_name   = 	'UNICEF - SportFirst';
	var description	   =	"I've added a Sport First moment to be screened at the Commonwealth Games in Glasgow, thanks to UNICEF. Join me - on your marks, get set, upload!";
	var share_image	   =	'<?php echo $twinimg; ?>';
	var share_url	   =	'<?php echo add_query_arg( array('id' => $ret['insertid'],'showtwin' => '1'), get_home_url() );?>';	
    var share_capt     =    'UNICEF - SportFirst';
    FB.ui({
        method: 'feed',
        name: product_name,
        link: share_url,
        picture: share_image,
        caption: share_capt,
        description: description

    }, function(response) {
        if(response && response.post_id){}
        else{}
    });

}

</script>
    <div class="form-items">
		<div class="thank-you">
			<h3>Well played! Your Sport First has raced into our gallery. Now we'd like to introduce your Sport First teammate.</h3>
			<p>Every Sport First is momentous, which is why we can't wait to celebrate them on a big screen at the Glasgow 2014 Commonwealth Games. Before your photo has a starring role, we'd like to introduce you to someone very special &ndash; your Sport First teammate. You'll see just what a Sport First can achieve around the world.</p>
			<div class="framed"> 
				<div class="left">
					<img src="<?php echo $imageyour ?>" />
				</div>
				<div class="right">
					<img src="<?php echo $imagetwin ?>" />
				</div>
				<div class="clear"><!-- --></div>
				<a href="#">#SPORTFIRST</a>
				<img class="unicef-logo-smaller" src="/wp-content/themes/twentythirteen-child/images/unicef_logo.jpg" alt="SportsFirst" />
				<div class="clear"><!-- --></div>
			</div>
			<div class="clear"><!-- --></div>
<?php
$notext = 0;
if (trim(stripslashes($row->imagetitle)) == '' && trim(stripslashes($row->imagestory)) == ''){
    $notext = 1;
?>
<style type="text/css">
.form-items .thank-you .no-text .left-desc {
    display: none;
}
.form-items .thank-you .no-text .right-desc {
    width: 100%;
    float: none;
}
</style>
<div class="no-text">
<?php
}    
?>            
			<div class="left-desc">
				<h4><?php echo stripslashes($row->imagetitle) ?></h4>
				<?php echo stripslashes($row->imagestory) ?>
			</div>
			<div class="right-desc">
				<h4><?php echo stripslashes($twin->imagetitle) ?></h4>
				<?php echo stripslashes($twin->imagestory) ?>            
			</div>
<?php
if ($notext == 1){
?>
</div>
<?php
}
?>
			<div class="clear"><!-- --></div>
			<p class="share-this"><span class="share-this-text">Share with your friends and family</span> <a data-provider="facebook" target="_blank" rel="nofollow" title="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(add_query_arg( array('id' => $ret['insertid'],'showtwin' => '1'), get_home_url() ));?>" onclick="FBShareOp(); return false;"><img class="unicef-logo-smaller" src="/wp-content/themes/twentythirteen-child/images/facebook-icon.png" alt="Facebook" />Facebook</a> <a data-provider="twitter" target="_blank" rel="nofollow" title="Share on Twitter" href="http://twitter.com/share?url=<?php echo urlencode(add_query_arg( array('id' => $ret['insertid'],'showtwin' => '1'), get_home_url() ));?>&amp;text=I%E2%80%99ve%20added%20a%20%23SportFirst%20moment%20to%20be%20screened%20at%20the%20Commonwealth%20Games%20with%20%40UNICEF_UK.%20Join%20me%20%E2%80%93%20get%20set%2C%20upload!"><img class="unicef-logo-smaller" src="/wp-content/themes/twentythirteen-child/images/twitter-icon.png" alt="Twitter" />Twitter</a></p>
			<!-- <p>Got another photograph? <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Upload it now!</a></p>-->
        </div>
		<div class="more">
	        <p><strong>You can discover more about the work that UNICEF does with children like your team mate on our Youtube channel.</strong></p>
            <p><a href="https://www.youtube.com/channel/UCUO3_RiI6mcWEklrD1FkPMQ" target="_blank" style="color: #ff0099; font-weight: bold;">Watch UNICEF videos</a></p>
            <!--
		    <img src="<?php echo get_bloginfo('template_directory');?>/images/how_UNICEF_make_a_difference.jpg" alt="How UNICEF make a difference" />
		    <h5>Let's bring more Sport First moments to children around the world</h5>
		    <p>Sport has the power to transform children's lives, as you can see from your own teammate above. Through sport, UNICEF can provide emotional and physical support to a child in conflict, or encourage a child to go to school where they can also learn to read and write or attract them to a community health day to be immunised against life-threatening diseases.</p>
		    <p>We work in every corner of the world, to help children get an education, stay healthy, escape poverty and have a childhood – and sport plays a vital part in achieving that.</p>
		    <p>If you would like to support children around the world, it couldn't be easier to donate now.</p>
		    <p class="donate"><span>Text DONATE to 70020 to give &pound;5*</span> or <a href="http://www.unicef.org.uk/landing-pages/donate-commonwealth-games/" class="donate-button">DONATE ONLINE NOW</a></p>
		    <p class="smallprint">*UK only. You will be charged &pound;5, plus one message at your standard network rate. A minimum of &pound;4.97, depending on your service provider, will be received by UNICEF UK. To discuss this mobile payment, please call 0844 801 2414.</p>
            -->
		    <p class="return-link"><a href="/">Return to the gallery</a></p>
	    </div>
    </div>
<?php  
}

function afg_display_form($err="") {
    global $wpdb;
    $username = "";
    $email = "";
    if (is_user_logged_in()){
        global $current_user;
        get_currentuserinfo();        
        $username = $current_user->user_firstname.' '. $current_user->user_lastname;
        $email = $current_user->user_email;
    }
?>

        <p class="error the-red"><?php echo $err; ?></p>
        <!-- <?php echo $_SERVER['REQUEST_URI']; ?> -->
        <div class="form-items">
			<div class="fieldset">
				<h3>Submit your Sport First image</h3>
				<p>Ready with your Sport First? Let's go!</p>
                <form name="upload_big" id="upload_big" class="uploaderForm" method="post" enctype="multipart/form-data" action="<?php echo AFGSURL;?>/upload.php?act=upload" target="upload_target">
				<div class="form-item">
					<label for="yourfile">
						Upload your image <span class="required">*</span> <em class="error the-red" id="file_error"></em>
					</label>
                    <div class="file-upload btn btn-primary">
                        <span class="fileButton">BROWSE</span>
                        <input type="file" class="upload" name="afgyourfile" id="yourfile" />
                        <input type="hidden" name="width" value="<?php echo 530 * 3; ?>" />
                        <input type="hidden" name="height" value="<?php echo 530 * 3; ?>" />
                    </div>
					<input id="uploadFile" placeholder="File location" disabled="disabled" />
					<script type="text/javascript">document.getElementById("yourfile").onchange = function () { 
					var fpath = this.value;
					fpath = fpath.replace('c:\\fakepath\\','');
					fpath = fpath.replace('/fakepath','');
					document.getElementById("uploadFile").value = fpath; };</script>
					<p class="clear">Please do not upload potentially sensitive images of children. We take child protection very seriously so we cannot accept naked images of children.</p>
					<?php if (isset($_POST['img_src']) && strpos($_POST['img_src'], 'uploads/') !== False){?>
					<div class="img-loaded show">
					<?php }else{?>
					<div class="img-loaded">
					<?php }?>
						<label>Crop your image</label>
						<p>Drag the cursor to find the best crop. Then you'll be ready for a starring role.</p>
						<img class="cropimage" id="uploadPreview" alt="" src="<?php echo (isset($_POST['img_src']))? $_POST['img_src'] : '';?>" cropwidth="530" cropheight="530" style="background: #d3d3d3;" />
            	        <!-- hidden iframe begin -->
            	    </div>
					<?php if (!isset($_POST['img_src']) || strpos($_POST['img_src'], 'uploads/') === False){?>
            	    <div class="noimg-loaded">
            	    	<p class="error"></p>
            	   	</div>
					<?php }?>
                    <iframe id="upload_target" name="upload_target" src=""></iframe>
                    <!-- hidden iframe end -->
				</div>
				</form>
                <form class="flickr-upload" id="ajaxafgflickr" action="" method="post">
                <input type="hidden" name="img_src" id="img_src" class="img_src" value="<?php echo (isset($_POST['img_src']))? $_POST['img_src'] : '';?>" /> 
                <input type="hidden" id="x" class="x" name="afgx" <?php echo (isset($_POST['afgx']))? 'value="'.$_POST['afgx'].'"' : '';?> />
                <input type="hidden" id="y" class="y" name="afgy" <?php echo (isset($_POST['afgy']))? 'value="'.$_POST['afgy'].'"' : '';?> />
                <input type="hidden" id="w" class="w" name="afgw" <?php echo (isset($_POST['afgw']))? 'value="'.$_POST['afgw'].'"' : '';?> />
                <input type="hidden" id="h" class="h" name="afgh" <?php echo (isset($_POST['afgh']))? 'value="'.$_POST['afgh'].'"' : '';?> />
				<div class="form-item">
					<label for="imagetitle">
						Name your Sport First <em class="error the-red" id="build_error"></em>
					</label>
					<input type="text" name="afgimagetitle" id="imagetitle" class="form-input"  maxlength="80" value="<?php echo (isset($_POST['afgimagetitle']))? $_POST['afgimagetitle'] : '';?>" />
				</div>
				
				<div class="form-item">
					<label for="imagestory">
						Tell us about your Sport First <em class="error the-red" id="imagestory_error"></em>
						<p style="margin-bottom: 0;">Who's the sporting superstar? Where did this Sport First happen? We'd love to hear the whole story.</p>
					</label>
					<textarea name="afgimagestory" id="imagestory"  maxlength="200" class="form-input"><?php echo (isset($_POST['afgimagetitle']))? $_POST['afgimagestory'] : '';?></textarea>
				</div>
				
				<div class="select-block">
					<div class="form-item">
						<label for="sport">
						Sport being played <em class="error the-red" id="sport_error"></em>
						</label>
						<div class="styled-select">
							<label for="sport">
								<select name="afgsport" id="sport" class="form-input " />
									<option class="disabled" selected="">Select a sport</option>
				<?php      
				$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."flickrsports WHERE published = 1" );    
				foreach ( $results as $row ) 
				{
				?>
									<option value="<?php echo $row->id; ?>" <?php echo (isset($_POST['afgsport']) && $_POST['afgsport'] == $row->sport) ? 'selected="selected"':'';?> ><?php echo $row->sport; ?></option>
				<?php     
				}
				?>
								</select>
							</label>
						</div>
					</div>

					<div class="form-item">
						<label for="age">
							Age <em class="error the-red" id="age_error"></em>
						</label>
						<div class="styled-select">
							<label for="age">
								<select name="afgage" id="age" class="form-input ">
									<option class="disabled" selected="">Select an age</option>
									<option value="1" <?php echo (isset($_POST['afgage']) && $_POST['afgage'] == 1) ? 'selected="selected"':'';?>>0-3</option>
									<option value="2" <?php echo (isset($_POST['afgage']) && $_POST['afgage'] == 2) ? 'selected="selected"':'';?>>4-7</option>
									<option value="3" <?php echo (isset($_POST['afgage']) && $_POST['afgage'] == 3) ? 'selected="selected"':'';?>>8-12</option>
									<option value="4" <?php echo (isset($_POST['afgage']) && $_POST['afgage'] == 4) ? 'selected="selected"':'';?>>13-15</option>
									<option value="5" <?php echo (isset($_POST['afgage']) && $_POST['afgage'] == 5) ? 'selected="selected"':'';?>>16+</option>
								</select>
							</label>
						</div>
					</div>

					<div class="form-item">
						<label for="gender">
							Gender <em class="error the-red" id="gender_error"></em>
						</label>
						<div class="styled-select">
							<label for="gender">
								<select name="afggender" id="gender" class="form-input "  >
									<option class="" <?php echo (!isset($_POST['afggender'])) ? 'selected=""': '';?>>Select a gender</option>
				<?php
				$v = mt_rand(0,100);     
				if( ($v % 2) == 0 )  {
				?>               
									<option value="m" <?php echo (isset($_POST['afggender']) && $_POST['afggender'] == 'm') ? 'selected="selected"':'';?>>Male</option>
									<option value="f" <?php echo (isset($_POST['afggender']) && $_POST['afggender'] == 'f') ? 'selected="selected"':'';?>>Female</option>
				<?php
				}
				else{
				?>
									<option value="f" <?php echo (isset($_POST['afggender']) && $_POST['afggender'] == 'f') ? 'selected="selected"':'';?>>Female</option>
									<option value="m" <?php echo (isset($_POST['afggender']) && $_POST['afggender'] == 'm') ? 'selected="selected"':'';?>>Male</option>
				<?php
				}
				?>                       
								</select>
							</label>
						</div>
					</div>
				</div>

				<div class="clear divider"><!-- --></div>

				<h3>YOUR DETAILS</h3>
				
				<div class="form-item">
					<label for="firstname">
						First name <span class="required">*</span> <em class="error the-red" id="firstname_error"></em>
					</label>
					<input type="text" name="afgfirstname" id="firstname" class="form-input" value="<?php echo (isset($_POST['afgfirstname']))? $_POST['afgfirstname'] : '';?>" validate="required:true" />
				</div>
				
				<div class="form-item">
					<label for="lastname">
						Surname <span class="required">*</span> <em class="error the-red" id="lastname_error"></em>
					</label>
					<input type="text" name="afglastname" id="lastname" class="form-input" value="<?php echo (isset($_POST['afglastname']))? $_POST['afglastname'] : '';?>" validate="required:true" />
				</div>          
				    
				<div class="form-item">
					<label for="youremail">
						Email address <span class="required">*</span> <em class="error the-red" id="email_error"></em>
					</label>
					<input type="email" name="afgyouremail" id="youremail" class="form-input " value="<?php echo (isset($_POST['afgyouremail']))? $_POST['afgyouremail'] : '';?>" validate="required:true" />
				</div>
				
				<div class="form-item">
					<label for="phonenumber">
						Mobile telephone number <em class="error the-red" id="phonenumber_error"></em>
						
					</label>
					<input type="tel" name="afgphonenumber" id="phonenumber" class="form-input " value="<?php echo (isset($_POST['afgphonenumber']))? $_POST['afgphonenumber'] : '';?>" />
				</div>
				
				<p class="required-msg">* Please fill in the required fields</p>
				
				<div class="form-item">
					<label for="tsandcs" class="tsandcs">
						<input type="checkbox" name="afgtsandcs" id="tsandcs" value="1" <?php echo (isset($_POST['afgtsandcs']) && $_POST['afgtsandcs'] == '1') ? 'checked="checked"':'';?> />I have read and agree with the <a href="http://www.unicef.org.uk/Terms-and-conditions/" target="_blank">terms and conditions</a> <em class="error the-red" id="tsandcs_error"></em>
					</label>
				</div>
				
				<div class="form-item">
					<label for="consent" class="consent">
						<input type="checkbox" name="afgconsent" id="consent" value="1" <?php echo (isset($_POST['afgconsent']) && $_POST['afgconsent'] == '1') ? 'checked="checked"':'';?> />I have the right to upload this image and I give consent to it being displayed on this website and in other UNICEF communications. <em class="error the-red" id="comms_error"></em>
					</label>
				</div>
				
				<div class="form-item">
					<p style="margin-bottom:0">We&rsquo;d love to tell you more about our work with children all over the world.</p>
					<label for="commsphone" class="comms">
						<input type="checkbox" name="afgcommsphone" id="commsphone" value="1" <?php echo (isset($_POST['afgcommsphone']) && $_POST['afgcommsphone'] == '1') ? 'checked="checked"':'';?> /> I&rsquo;m happy to hear from UNICEF UK by phone. <em class="error the-red" id="commsphone_error"></em>
					</label>
					<label for="commsemail" class="comms">
						<input type="checkbox" name="afgcommsemail" id="commsemail" value="1" <?php echo (isset($_POST['afgcommsemail']) && $_POST['afgcommsemail'] == '1') ? 'checked="checked"':'';?> /> I&rsquo;m happy to hear from UNICEF UK by email. <em class="error the-red" id="commsemail_error"></em>
					</label>					
				</div>
				
				<div class="form-item sf-upload-button-container">
					<input type="submit" class="button sf-upload-button" value="SUBMIT YOUR SPORT FIRST" />
                    
                    <div class="submitGif" style="display: none"><img src="http://sportfirst.unicef.org.uk/wp-content/themes/twentythirteen-child/images/UNICEF_loading.gif" alt="Loading image" /></div>
                    
<?php
/*
						<button type="submit" class="button sf-upload-button"><span>SUBMIT YOUR SPORT FIRST</span></button>
						*/
?>					
				</div>
            </form>
			</div>

			<p class="cancel-link"><a href="/">Cancel and go to the gallery</a></p>
			
        </div>
<?php
}
function afg_upload_form( $atts ){
    extract( shortcode_atts( array(
        'id' => '0',
    ), $atts ) );
    
//$content = print_r($_POST, true);
//file_put_contents(AFGPATH.'/file.log', $content);    

    ob_start();
    if (isset($_POST["img_src"]) && strlen($_POST["img_src"]) > 0){
        $ret = ajaxafg_flickr_send();
//file_put_contents(AFGPATH.'/file.log', $ret, FILE_APPEND); 
        if ( (array) $ret === $ret ){
            afg_display_thanks($ret);
        }
        else{
            afg_display_form($ret);
        }
    }
    else{
        afg_display_form();
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function afgflickr_enqueuescripts(){
    //wp_enqueue_script('afg_imgareselect_js', AFGSURL . "/js/jquery.imgareaselect.min.js", array('jquery'));
    //wp_enqueue_style('afg_imgareselect_css', AFGSURL . "/css/imgareaselect.css");
    wp_enqueue_style('afg_cropbox', AFGSURL . "/css/jquery.cropbox.css");
    wp_enqueue_script('hammer', AFGSURL.'/js/hammer.js', array('jquery'), '', true);
    wp_enqueue_script('touchwheel', AFGSURL.'/js/jquery.mousewheel.js', array('jquery'), '', true);
    wp_enqueue_script('cropbox', AFGSURL.'/js/jquery.cropbox.js', array('jquery'), '', true);   
    wp_enqueue_script('flickr-uploader', AFGSURL.'/js/uplflickr.js', array('jquery'), '', true);
    wp_register_script( 'ajax-afg_report_scripts', AFGSURL . '/js/imagereporter.js', array(), '', true );
    wp_enqueue_script( 'ajax-afg_report_scripts' );   
    wp_localize_script('flickr-uploader', 'ajaxafgflickr', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function validateInputs($inputs,$rules,$default){
	$errors = array();
	foreach ($inputs as $key => $value){
        foreach ($rules[$key] as $rule){
            switch($rule){
                case 'notEmpty':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'length2':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'numbers':
                    if(!preg_match("/[0-9]+$/", $value)) {
                        echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                   
                case 'invalidChars':
                    if(preg_match("/[&,;\\\"#\(\)\'*+:<=>?{}~£\$@!%\[\]]+$/", $value)) {
                        //echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'invalidCharsOrg':
                    if(preg_match("/[&,;\\\"#\'*<=>?{}\$%\[\]]+$/", $value)) {
                        ///echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;    
                case 'letters2f':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(preg_match("/[&,;\\\"#\(\)\'*+:<=>?{}~£\$@!%\[\]0-9]+$/", $value)) {
                        echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2 && trim($value) != '.') {
                        echo 'failed short '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                    
                case 'letters2':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(preg_match("/[0-9]+$/", $value)) {
                        //echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break; 
                case 'letters2amp':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(!preg_match("/^[a-zA-Z\-'&]+$/", $value)) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                 
                case 'notDefault':
                   if ($value == $default[$key] || trim($value) == "" ){

                       $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'postCode':
                    $value = strtoupper(str_replace(' ','',$value));
                    if (!(preg_match("/^[A-Z]{1,2}[0-9]{2,3}[A-Z]{2}$/",$value) || preg_match("/^[A-Z]{1,2}[0-9]{1}[A-Z]{1}[0-9]{1}[A-Z]{2}$/",$value) || preg_match("/^GIR0[A-Z]{2}$/",$value))){
                        $errors[$key]['message'] = $default[$key];					
                    }
                    //$errors[$key] = 1;
                    break;			
                case 'email':
                    if (!(preg_match("/^[^@]*@[^@]*\.[^@]*$/", $value))){
                        $errors[$key]['message'] = $default[$key];					
                    }
                    break;
                case 'date':
                    if (date('d-m-Y', strtotime($value)) != $value) {	
                        $errors[$key]['message'] = $default[$key];					
                    }
                    break;
            }
        }
	}
	return $errors;
}

function ajaxafg_flickr_send(){
    global $pf, $wpdb;
   
    $results = '';
    $error = 0;

    /* Check if both name and file are filled in */
    if(!$_POST['afgfirstname'] || !$_POST["img_src"]){
        $error=1;
        $results = "You must enter your name and a file to upload";
        if (file_exists(AFGPATH.'/'.$_POST["img_src"])) unlink(AFGPATH.'/'.$_POST["img_src"]);
    }
    else if(!isset($_POST['afgtsandcs']) || intval($_POST['afgtsandcs']) != 1){
        $error=1;
        $results = "You must accept the terms and conditions";          
        if (file_exists(AFGPATH.'/'.$_POST["img_src"])) unlink(AFGPATH.'/'.$_POST["img_src"]);
    }
    else{
    	if(CODEDEBUG){
			//file_put_contents(AFGPATH.'/file.log', 'upload-afg_flickr_send');    
			$content = print_r($_POST, true);
			file_put_contents(AFGPATH.'/file.log', $content, FILE_APPEND);     
		}
        $fname = $_POST['afgfirstname'];
        $email = $_POST['afgyouremail'];
        $tsandcs = $_POST['afgtsandcs'];       
        $newpath = AFGPATH.'/'.$_POST["img_src"];
        include('lib/wideimage-11.02.19/WideImage.php');
        $wideImage = new WideImage();
        $w = (int) $_POST['afgw'] ? $_POST['afgw'] : 530;
        $h = (int) $_POST['afgh'] ? $_POST['afgh'] : 530;        
        $workingImg = $wideImage->load($newpath);
        if (isset($_POST['afgx']) && isset($_POST['afgy'])){
            $x = (int) $_POST['afgx'];
            $y = (int) $_POST['afgy'];          
            $workingImg = $workingImg->crop($x, $y, $w, $h);
        } 
        else{                             
            $workingImg = $workingImg->crop(0, 0, $w, $h);
        }
        $workingImg->saveToFile($newpath);                     
        $description = $fname ."\n". $email ."\n". $tsandcs;
        /* Call uploadPhoto on success to upload photo to flickr */
        $photoid = uploadPhoto($newpath, $name, $description);
		if (CODEDEBUG){
			file_put_contents(AFGPATH.'/file.log', $photoid, FILE_APPEND);     
		}
        if(!$photoid) {
            $error = 1;
            $results = "Something has gone wrong";
            unlink($newpath);
        }
        else{
            $photoinfo = $pf->photos_getInfo($photoid);
            //$getTwin = $pf->photos_search(array()) ;
            $results = array(
                'firstname' => '',
                'lastname' => '',
                'youremail' => '',
                'phonenumber' => '',
                'sport' => '',
                'age' => '',
                'gender' => '',
                'imagetitle' => '',
                'imagestory' => '',
                'yourfile' => '',
                'tsandcs' => '',
                'consent' => '',
                'commsphone' => '',
                'commsemail' => ''                
            );	
            $rules = array(
                'firstname' => 'notEmpty',
                'lastname' => 'notEmpty',
                'youremail' => 'email'
            );
            $messages = array(
                'firstname' => "Please enter your first name",
                'lastname' => "Please enter your surname",
                'youremail' => "Please enter your email address"
            );   
            foreach ($results as $key => $value){
                $results[$key] = $_POST['afg'.$key];
            }  
            $errors = validateInputs($results, $rules, $messages);
            if (count($errors) == 0) $error = False;
            $age = intval($results['age']);
            //echo "SELECT *, 10 AS relevance FROM ". $wpdb->prefix ."flickrtwins WHERE age='". . "' OR  age='" . intval($results['age']) - 1 . "' ";
            
            $sqlbits = array();
            $sqlbits[] = isset($results['sport']) ? "SELECT *, 50 AS relevance FROM ".$wpdb->prefix."flickrtwins WHERE sport='". $results['sport'] ."' "  : '';
            $sqlbits[] = isset($results['age']) ? "SELECT *, 30 AS relevance FROM ".$wpdb->prefix."flickrtwins WHERE age='". $age ."' "  : '';
            $sqlbits[] = isset($results['age']) ? "SELECT *, 10 AS relevance FROM ".$wpdb->prefix."flickrtwins WHERE age='". strval($age + 1) . "' OR  age='" . strval($age - 1) . "' "  : '';
            $sqlbits[] = isset($results['gender']) ? "SELECT *, 20 AS relevance FROM ".$wpdb->prefix."flickrtwins WHERE gender='".$results['gender']."' "  : '';
            $sql2 = implode(' UNION ', $sqlbits);
            $sql  = "SELECT *, sum(relevance) as rel FROM ({$sql2}) results ORDER BY sum(relevance) desc LIMIT 0,1;";
            //echo $sql;
            $match = $wpdb->get_row($sql);
            //var_dump( $wpdb->last_query );
            if ($match->id ==  null){
                $match = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}flickrtwins ORDER BY RAND() LIMIT 0,1 ");
            }
            /*make flat*/
            //echo afg_get_photo_url($photoinfo['photo']['farm'], $photoinfo['photo']['server'], $photoinfo['photo']['id'], $photoinfo['photo']['secret'], '_n');
            $user = $workingImg->resize(255,255);
            unlink($newpath);
            $twin = $wideImage->load(afg_get_photo_url($match->farm, $match->server, $match->photoid, $match->photosecret, '_n'))->resize(255,255);
            $frame = $wideImage->load(AFGPATH.'/images/frame.png')->merge($user,9, 7)->merge($twin, 264, 7);                 

            $pathinfo = pathinfo($_POST["img_src"]);
            $newpath = AFGPATH.'/uploads/twins/'. $pathinfo['filename'] . '_framed.' . $pathinfo['extension'] ;
            $frame->saveToFile($newpath);

            $twinoid = uploadPhoto($newpath, 'twin '.$name, $description);
            unlink($newpath);
            $twininfo = $pf->photos_getInfo($twinoid);
            $wpdb->insert(
                $wpdb->prefix . "flickruploads",
                array(
                    'fname' => $results['firstname'],
                    'lname' => $results['lastname'],
                    'email' => $results['youremail'],
                    'phone' => isset($results['phonenumber']) ? strip_tags($results['phonenumber']) : '',
                    'sport' => isset($results['sport']) ? strip_tags($results['sport']) : 0,
                    'age' => isset($results['age']) ? strip_tags($results['age']) : '',
                    'gender' => isset($results['gender']) ? strip_tags($results['gender']) : '',
                    'imagetitle' => isset($results['imagetitle']) ? substr(strip_tags($results['imagetitle']), 0, 80) : '',
                    'imagestory' => isset($results['imagestory']) ? substr(strip_tags($results['imagestory']), 0, 200) : '',
                    'photoid' => $photoinfo['photo']['id'],
                    'photosecret' => $photoinfo['photo']['secret'],
                    'farm' => $photoinfo['photo']['farm'],
                    'server' => $photoinfo['photo']['server'],   
                    'twinlid' => $match->id,
                    'flatid'  =>  $twininfo['photo']['id'],
                    'flatsecret'  => $twininfo['photo']['secret'],
                    'flatfarm' => $twininfo['photo']['farm'],
                    'flatserver' => $twininfo['photo']['server'],
                	'tsandcs' => isset($results['tsandcs']) ? 1 : 0,
	                'consent' => isset($results['consent']) ? 1 : 0,
	                'commsphone' => isset($results['commsphone']) ? 1 : 0,                 
	                'commsemail' => isset($results['commsemail']) ? 1 : 0,                 
                    'submittedtime' => date('Y-m-d H:i:s')
                )
            );
            
			if (CODEDEBUG){
				$content = print_r($wpdb->queries, true);
				file_put_contents(AFGPATH.'/file.log', $content, FILE_APPEND);   			
				file_put_contents(AFGPATH.'/file.log', $wpdb->print_error(), FILE_APPEND);   			
			}            
            $results['photouploaded'] = array(
                'id' => $photoinfo['photo']['id'],
                'secret' => $photoinfo['photo']['secret'] ,
                'farm' => $photoinfo['photo']['farm'],
                'server' => $photoinfo['photo']['server']
            );
            $results['phototwin'] = array(
                'id' => $twininfo['photo']['id'],
                'secret' => $twininfo['photo']['secret'] ,
                'farm' => $twininfo['photo']['farm'],
                'server' => $twininfo['photo']['server']
            );
            $results['match'] = $match->id;
            $results['matchedwith'] = array(
                'id' => $match->photoid,
                'secret' => $match->photosecret,
                'farm' => $match->farm,
                'server' => $match->server
            );
            $results['insertid'] = $wpdb->insert_id;
            $template = 'emails/email-thankyou';
            if (trim(stripslashes(substr(strip_tags($results['imagetitle']), 0, 80))) =='' && 
                trim(stripslashes(substr(strip_tags($results['imagestory']), 0, 200))) ==''){
                    $template = 'emails/email-thankyou2';
            }

		    $vars = array(            
		    	'email' => $template,
		    	'subject' => 'Thank you',
		        'firstname' => $results['firstname'],
		        'image' => afg_get_photo_url(
		        	$results['photouploaded']['farm'], 
		        	$results['photouploaded']['server'], 
		        	$results['photouploaded']['id'], 
		        	$results['photouploaded']['secret'], '_n'),
		        'twin' => afg_get_photo_url(
		        	$match->farm, 
		        	$match->server, 
		        	$match->photoid, 
		        	$match->photosecret, '_n'),
		        'usertitle' =>  stripslashes(substr(strip_tags($results['imagetitle']), 0, 80)),
		        'userstory' => stripslashes(substr(strip_tags($results['imagestory']), 0, 200)),
		        'uniceftitle' => stripslashes($match->imagetitle),
		        'unicefstory' => stripslashes($match->imagestory),
		        'shareurl' => urlencode(add_query_arg( array('id' => $results['insertid'],'showtwin' => '1'), get_home_url() )),
                'sharepic' => afg_get_photo_url(
                    $twininfo['photo']['farm'],                
                    $twininfo['photo']['server'],               
                    $twininfo['photo']['id'],
                    $twininfo['photo']['secret'] , null)
			);
			if (CODEDEBUG){
				$content = print_r($vars, true);
				file_put_contents(AFGPATH.'/file.log', $content, FILE_APPEND);   			
			}
		    sendemail($results['youremail'], $results['firstname']. ' '. $results['lastname'], $vars, 0);             
        }
    }
    if (CODEDEBUG){
		$content = print_r($results, true);
		file_put_contents(AFGPATH.'/file.log', $content, FILE_APPEND);       
	}  
    return $results;
}

if(!function_exists('getMaxUploadSize')){ 
function getMaxUploadSize(){
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
if(!function_exists('getMaxUploadSizeRaw')){ 
function getMaxUploadSizeRaw(){
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
if(!function_exists('uploadPhoto')){
function uploadPhoto($path, $title, $description, $tags = null) {  
    global $pf ;
    create_afgFlickr_obj();
    return $pf->sync_upload($path, $title, $description, $tags, 0, 0, 0, 2, 1, 2);
}
}
add_action('wp_head', 'add_afg_headers');

function add_afg_headers() {
    global $enable_highslide;
    if ($enable_highslide) {
        echo "<script type='text/javascript'>
            hs.graphicsDir = '" . BASE_URL . "/highslide/graphics/';
        hs.align = 'center';
        hs.transitions = ['expand', 'crossfade'];
        hs.fadeInOut = true;
        hs.dimmingOpacity = 0.85;
        hs.outlineType = 'rounded-white';
        hs.captionEval = 'this.thumb.alt';
        hs.marginBottom = 115; // make room for the thumbstrip and the controls
        hs.numberPosition = 'caption';
        // Add the slideshow providing the controlbar and the thumbstrip
        hs.addSlideshow({
            //slideshowGroup: 'group1',
            interval: 3500,
                repeat: false,
                useControls: true,
                overlayOptions: {
                    className: 'text-controls',
                        position: 'bottom center',
                        relativeTo: 'viewport',
                        offsetY: -60
    },
    thumbstrip: {
        position: 'bottom center',
            mode: 'horizontal',
            relativeTo: 'viewport'
    }
    });
         </script>";
    }
    if (trim(get_option('afg_custom_css')) != '' && trim(preg_replace('~\/\*[^*]*\*+([^*][^*]*\*+)*\/~i', '',get_option('afg_custom_css') )) != '') echo "<style type=\"text/css\">" . get_option('afg_custom_css') . "</style>";
}

function afg_return_error_code($rsp) {
    return $rsp['message'];
}

function flickrDecode($num){
	$alphabet = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";

	$decoded = 0;
	$multi = 1;
	while (strlen($num) > 0) {
		$digit = $num[strlen($num)-1];
		$decoded += $multi * strpos($alphabet, $digit);
		$multi = $multi * strlen($alphabet);
		$num = substr($num, 0, -1);
	}

	return $decoded;
}

function rando($rset){
    if (isset($rset['used'])) $used = (array)$rset['used'];
    else $used = array();
    $min = $rset['min'];
    $max = $rset['max'];
    $n = 0;
    do {  
        $n = rand($min,$max);
    } while(in_array($n, $used));
   
    $rset = array(
        'chose' => $n,
        'used' => array_push($used, $n),
        'min' => $min,
        'max' => $max
    );   
    return $rset;
}

/* Main function that loads the gallery. */
function afg_display_gallery($atts) {
    global $size_heading_map, $afg_text_color_map, $pf, $wpdb;
    wp_enqueue_script( 'ajax-afg_report_scripts' );
    if (!get_option('afg_pagination')) update_option('afg_pagination', 'on');
    $rset = array(
        'used' => array(),
        'min' => 1,
        'max' => 8
    );
    extract( shortcode_atts( array(
        'id' => '0',
    ), $atts ) );
    $disp_singleuser = array();
    if (isset($_REQUEST['id'])){
        $did = intval($_REQUEST['id']);
        $dtype = '';
        if ($_REQUEST['type'] == 'c'){
            $dtype = 'c';       
            $row =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' AND id='".intval($_REQUEST['id'])."'" );          
        }
        elseif ($_REQUEST['type'] == 't'){
            $dtype = 't';       
            $row =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}flickrtwins  WHERE published = '1' AND id='".intval($_REQUEST['id'])."'" );          
        }
        else{
            $dtype = 'u';        
            $row =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}flickruploads  WHERE status <> '-1' AND id='".intval($_REQUEST['id'])."'" );          
        }
        if  ($row !== null){
            $owner = get_option('afg_user_id');
            if(isset($_REQUEST['showtwin'])){
                $twin =  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}flickrtwins  WHERE published = '1' AND id='".intval($row->twinlid)."'" );          
                $disp_singleuser[0] = array(
                    'id' => $row->photoid ,
                    'owner' => $owner,
                    'secret' => $row->photosecret,
                    'server' => $row->server,
                    'farm' => $row->farm,
                    'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                    'description' => Array(
                        '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                    ),               
                    'twin' => $row->twinlid,
                    'twinid' => $twin->photoid,
                    'twinsecret' => $twin->photosecret,
                    'twinfarm' => $twin->farm,
                    'twinserver' => $twin->server,
                    'twintitle' => stripslashes(substr(strip_tags($twin->imagetitle), 0, 80)),
                    'twindescription' => Array(
                        '_content' => stripslashes(substr(strip_tags($twin->imagestory), 0, 200)),
                    ),                  
                    'flatid' => $row->flatid,
                    'flatsecret' => $row->flatsecret,
                    'flatfarm' => $row->flatfarm,
                    'flatserver' => $row->flatserver,
                    'ispublic' => 0,
                    'isfriend' => 0,
                    'isfamily' => 0,
                    'did' => $row->id,
                    'dtype' => $dtype,
                    'dateupload' => strtotime($row->lastmodified),
                    'datetaken' => $row->submittedtime
                );
            }
            else{
                $disp_singleuser[0] = array(
                    'id' => $row->photoid ,
                    'owner' => $owner,
                    'secret' => $row->photosecret,
                    'server' => $row->server,
                    'farm' => $row->farm,
                    'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                    'description' => Array(
                        '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                    ),               
                    'ispublic' => 0,
                    'isfriend' => 0,
                    'isfamily' => 0,
                    'did' => $row->id,
                    'dtype' => $dtype,

                    'dateupload' => strtotime($row->lastmodified),
                    'datetaken' => $row->submittedtime
                );           
            }
        }
        else{
            //klaxon - this image has been banned or does not exist
        }
    }
    $cur_page = 1;
    $cur_page_url = afg_get_cur_url();

    preg_match("/afg{$id}_page_id=(?P<page_id>\d+)/", $cur_page_url, $matches);

    if ($matches) {
        $cur_page = ($matches['page_id']);
        $match_pos = strpos($cur_page_url, "afg{$id}_page_id=$cur_page") - 1;
        $cur_page_url = substr($cur_page_url, 0, $match_pos);
        if(function_exists('qtrans_convertURL')) {
            $cur_page_url = qtrans_convertURL($cur_page_url);
        }
    }

    if (strpos($cur_page_url,'?') === false) $url_separator = '?';
    else $url_separator = '&';

    $galleries = get_option('afg_galleries');
    $gallery = $galleries[$id];

    $api_key = get_option('afg_api_key');
    $user_id = get_option('afg_user_id');
    $disable_slideshow = (get_afg_option($gallery, 'slideshow_option') == 'disable');
    $slideshow_option = get_afg_option($gallery, 'slideshow_option');

    $per_page = get_afg_option($gallery, 'per_page');
    $sort_order = get_afg_option($gallery, 'sort_order');
    $photo_size = get_afg_option($gallery, 'photo_size');
    $photo_title = get_afg_option($gallery, 'captions');
    $photo_descr = get_afg_option($gallery, 'descr');
    $bg_color = get_afg_option($gallery, 'bg_color');
    $columns = get_afg_option($gallery, 'columns');
    $credit_note = get_afg_option($gallery, 'credit_note');
    $gallery_width = get_afg_option($gallery, 'width');
    $pagination = get_afg_option($gallery, 'pagination');

    if ($photo_size == 'custom') {
        $custom_size = get_afg_option($gallery, 'custom_size');
        $custom_size_square = get_afg_option($gallery, 'custom_size_square');

        if ($custom_size <= 70) $photo_size = '_s';
        else if ($custom_size <= 90) $photo_size = '_t';
        else if ($custom_size <= 220) $photo_size = '_m';
        else if ($custom_size <= 500) $photo_size = 'NULL';
    }
    else {
        $custom_size = 0;
        $custom_size_square = 'false';
    }

    $photoset_id = NULL;
    $gallery_id = NULL;
    $group_id = NULL;
    $tags = NULL;
    $popular = false;
    $users = false;
    $celebs = false;
    $userscelebs = false;
    $disp_gallery = '';
    $extras = 'url_l, description, date_upload, date_taken, owner_name';

    $total_photos =  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickruploads  WHERE status <> '-1' " );
    $total_photos +=  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' " );
    $total_photos +=  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickrtwins  WHERE published = '1' " );
    if ((isset($_REQUEST['sport']) && intval($_REQUEST['sport']) != 0  )|| ( isset($_REQUEST['category'])  && $_REQUEST['category'] != ''  ) ){
        $total_photos = 0;       
        if (isset($_REQUEST['category']) && $_REQUEST['category'] != ''){
            $whereit = '';
            if ($_REQUEST['sport'] != '' && intval($_REQUEST['sport']) != 0){
                $whereit = " AND sport='". intval($_REQUEST['sport'])."' ";
            }
            if ($_REQUEST['category'] == 'child'){               
                $total_photos +=  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickrtwins  WHERE published = '1'".$whereit );
            }
            if ($_REQUEST['category'] == 'ambassador'){               
                $total_photos +=  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' AND unicef='1'".$whereit );
            }   
            if ($_REQUEST['category'] == 'supporter'){               
                $total_photos +=  $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' AND unicef='0'".$whereit );
            }            
        }
        else{
            $whereit = '';
            if ($_REQUEST['sport'] != '' && intval($_REQUEST['sport']) != 0){
                $whereit = " AND sport='". intval($_REQUEST['sport'])."' ";
            }       
            $total_photos = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}flickruploads  WHERE status <> '-1' ".$whereit );
        }
    }
    else{
        if (isset($disp_singleuser['id']) && (!isset($_REQUEST['page_id']) || $_REQUEST['page_id'] == 1 ) ){
            $total_photos += 1;
        }
    }

    $photos = get_transient('afg_id_' . $id);
    if (DEBUG  || (isset($_GET['id']) && intval($_GET['id']) > 0) ){
        $photos = NULL;
    }
    $flickr_api = 'photos';
    $rsp_obj_total[$flickr_api] = array();
    $rsp_obj_total[$flickr_api]['photo'] = array();
    $twins = array();
    if ($photos == false || $total_photos != count($photos)) {
        $photos = array();
        for($i=1; $i<($total_photos/500)+1; $i++) { 
            $flickr_api = 'photos';
            if (( isset($_REQUEST['category'])  && $_REQUEST['category'] != ''  ) ){            

                $owner = get_option('afg_user_id');
                $kids = array();
                $celebs = array();
                $whereit = '';
                if ($_REQUEST['sport'] != '' && intval($_REQUEST['sport']) != 0){
                    $whereit = " AND sport='". intval($_REQUEST['sport'])."' ";
                }
                $rows = null;
                if ($_REQUEST['category'] =='ambassador' ){
                    $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' AND unicef='1'".$whereit." ORDER BY RAND()  " );          
                }
                else{
                    $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1' AND unicef='0'".$whereit." ORDER BY RAND()  " );          
                }
                if ($_REQUEST['category'] != 'child'){
                    foreach ($rows as $row){
                        if (isset($disp_singleuser[0]['did'])
                            && ($disp_singleuser[0]['did'] == $row->id
                            && $disp_singleuser[0]['dtype'] == 'c')){
                            continue;
                        }                
                        $celebs[] = array(                       
                           'id' => $row->photoid ,
                            'owner' => $owner,
                            'secret' => $row->photosecret,
                            'server' => $row->server,
                            'farm' => $row->farm,
                            'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                            'ispublic' => 0,
                            'isfriend' => 0,
                            'isfamily' => 0,
                            'description' => Array(
                                '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                            ),
                            'did' => $row->id,
                            'dtype' => 'c',
                            'dateupload' => strtotime($row->lastmodified),
                            'datetaken' => $row->submittedtime
                        );
                    }
                }

                if ($_REQUEST['category'] == 'child'){
                    $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrtwins  WHERE published = '1'".$whereit." ORDER BY RAND()  " );          

                    foreach ($rows as $row){
                        if (isset($disp_singleuser[0]['did'])
                            && ($disp_singleuser[0]['did'] == $row->id
                            && $disp_singleuser[0]['dtype'] == 't')){
                            continue;
                        }                
                        $twins[$row->id] = $row;
                        $kids[] = array(
                            'id' => $row->photoid ,
                            'owner' => $owner,
                            'secret' => $row->photosecret,
                            'server' => $row->server,
                            'farm' => $row->farm,
                            'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                            'ispublic' => 0,
                            'isfriend' => 0,
                            'isfamily' => 0,
                            'description' => Array(
                                '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                            ),
                            'did' => $row->id,
                            'dtype' => 't',                       
                            'dateupload' => strtotime($row->lastmodified),
                            'datetaken' => $row->submittedtime
                        );
                    }  
                }
                
                if (count($celebs) > 0){
                    $rset = rando($rset);                   
                    if (count($celebs) > 1){
                        $count = 1;
                        foreach ($celebs as $key => $celeb){
                            if ($count > 2){
                                $rset['min'] = 8;
                                $rset['max'] = count($rsp_obj_total[$flickr_api]);
                            }
                            $rset = rando($rset);
	                        array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($celeb) );
                            unset($celebs[$key]);
                        }
                    }else{
                        array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($celebs[0]) );
                        unset($celebs[0]);                    
                    }
                }
                if (count($kids) > 0){
                    $rset['min'] = 1;
                    $rset['max'] = 8;
         
                    $rset = rando($rset);                   
                    if (count($kids) > 1){
                        $count = 1;
                        foreach ($kids as $key => $kid){
                            if ($count > 2){
                                $rset['min'] = 8;
                                $rset['max'] = count($rsp_obj_total[$flickr_api]);
                            }
                            $rset = rando($rset);
	                        array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($kid) );
                            unset($kids[$key]);
                        }
                    }
                }
                //print_R($rsp_obj_total[$flickr_api]);
            }
            else {
                $owner = get_option('afg_user_id');
                $kids = array();
                $celebs = array();
                $whereit = '';
                if ($_REQUEST['sport'] != '' && intval($_REQUEST['sport']) != 0){
                    $whereit = " AND sport='". intval($_REQUEST['sport'])."' ";
                }               
                $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrcelebs  WHERE published = '1'".$whereit." ORDER BY RAND()  " );          
                foreach ($rows as $row){
                    if (isset($disp_singleuser[0]['did'])
                        && ($disp_singleuser[0]['did'] == $row->id
                        && $disp_singleuser[0]['dtype'] == 'c')){
                        continue;
                    }                
                    $celebs[] = array(
                        'id' => $row->photoid ,
                        'owner' => $owner,
                        'secret' => $row->photosecret,
                        'server' => $row->server,
                        'farm' => $row->farm,
                        'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                        'ispublic' => 0,
                        'isfriend' => 0,
                        'isfamily' => 0,
                        'description' => Array(
                            '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                        ),
                        'did' => $row->id,
                        'dtype' => 'c',                         
                        'dateupload' => strtotime($row->lastmodified),
                        'datetaken' => $row->submittedtime
                    );
                }
                //print_R($rsp_obj_total[$flickr_api]);
                $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickruploads  WHERE status <> '-1'".$whereit." ORDER BY submittedtime DESC" );          
                foreach ($rows as $row){
                    /*
                    echo $disp_singleuser[0]['did'] .'|'.
                        $row->id  .'|'.
                        $disp_singleuser[0]['dtype'] ."\n";
                    */
                    if (isset($disp_singleuser[0]['did'])
                        && ($disp_singleuser[0]['did'] == $row->id
                        && $disp_singleuser[0]['dtype'] == 'u')){
                        continue;
                    }                   
                    $rsp_obj_total[$flickr_api]['photo'][] = array(
                        'id' => $row->photoid ,
                        'owner' => $owner,
                        'secret' => $row->photosecret,
                        'server' => $row->server,
                        'farm' => $row->farm,
                        'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                        'ispublic' => 0,
                        'isfriend' => 0,
                        'isfamily' => 0,
                        'description' => Array(
                            '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                        ),
                        'did' => $row->id,
                        'dtype' => 'u',                         
                        'dateupload' => strtotime($row->lastmodified),
                        'datetaken' => $row->submittedtime,
                        'twinned' => $row->twinlid
                        
                    );
                }
                $rows =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrtwins  WHERE published = '1'".$whereit." ORDER BY RAND()  " );          
                foreach ($rows as $row){
                    if (isset($disp_singleuser[0]['did'])
                        && ($disp_singleuser[0]['did'] == $row->id
                        && $disp_singleuser[0]['dtype'] == 't')){
                        continue;
                    }               
                    $twins[$row->id] = $row;
                    $kids[] = array(
                        'id' => $row->photoid ,
                        'owner' => $owner,
                        'secret' => $row->photosecret,
                        'server' => $row->server,
                        'farm' => $row->farm,
                        'title' => stripslashes(substr(strip_tags($row->imagetitle), 0, 80)),
                        'ispublic' => 0,
                        'isfriend' => 0,
                        'isfamily' => 0,
                        'description' => Array(
                            '_content' => stripslashes(substr(strip_tags($row->imagestory), 0, 200)),
                        ),
                        'did' => $row->id,
                        'dtype' => 't',                         
                        'dateupload' => strtotime($row->lastmodified),
                        'datetaken' => $row->submittedtime
                    );
                }
                if (count($celebs) > 0){
                    $rset = rando($rset);                   
                    if (count($celebs) > 1){
                        $count = 1;
                        foreach ($celebs as $key => $celeb){
                            if ($count > 2){
                                $rset['min'] = 8;
                                $rset['max'] = count($rsp_obj_total[$flickr_api]);
                            }
                            $rset = rando($rset);
                            if (count($rsp_obj_total[$flickr_api]['photo']) > 0){
								array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($celeb) );
                            }
                            else{
	                            array_splice($rsp_obj_total[$flickr_api], $rset['chose'], 0, $celeb );
	                        }                            
                            unset($celebs[$key]);
                        }
                    }
                    else{
                        array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($celebs[0]) );
                        unset($celebs[0]);                    
                    }
                }
                if (count($kids) > 0){
                    $rset['min'] = 1;
                    $rset['max'] = 8;
                    $rset = rando($rset);                   
                    if (count($kids) > 1){
                        $count = 1;
                        foreach ($kids as $key => $kid){
                            if ($count > 2){
                                $rset['min'] = 8;
                                $rset['max'] = count($rsp_obj_total[$flickr_api]);
                            }
                            $rset = rando($rset);
                            if (count($rsp_obj_total[$flickr_api]['photo']) > 0){
                            	array_splice($rsp_obj_total[$flickr_api]['photo'], $rset['chose'], 0, array($kid) );
                            }
                            else{
	                            array_splice($rsp_obj_total[$flickr_api], $rset['chose'], 0, $kid );
	                        }
                            unset($kids[$key]);
                        }
                    }
                }                   
                //print_R($rsp_obj_total[$flickr_api]);
                //$shuffled = shuffle($rsp_obj_total[$flickr_api]['photo']);
                //print_R($rsp_obj_total[$flickr_api]);
            }
            $photos = array_merge($photos, $rsp_obj_total[$flickr_api]['photo']);
        }
        //print_R($photos);
        if (isset($disp_singleuser[0]['id']) ){
            array_splice($photos, 0, 0, array($disp_singleuser[0]) );
        }
        if (!DEBUG && (!isset($_GET['id']) || intval($_GET['id']) == 0) ){
            set_transient('afg_id_' . $id, $photos, 60 * 60 * 24);
        }
    }
    $ids  = array();
    $tmpphotos = array ();
    foreach ($photos as $photo) {
        if (!in_array($photo['id'],$ids)){ 
            $ids[] = $photo['id'];
            $tmpphotos[] =  $photo;
        }
    }
    $photos = $tmpphotos;    
    //print_R($photos);
    if (($total_photos % $per_page) == 0) $total_pages = (int)($total_photos / $per_page);
    else $total_pages = (int)($total_photos / $per_page) + 1;
    if ($gallery_width == 'auto') $gallery_width = 100;
    $text_color = isset($afg_text_color_map[$bg_color])? $afg_text_color_map[$bg_color]: '';
	$disp_gallery .= '<form class="filters">';
    $disp_gallery .= '<label for="image-filters" class="image-filters">Sort the Sport Firsts</label>';
    $disp_gallery .= '<div class="styled-select styled-select-1">';
    $disp_gallery .= '<label for="filter-sport">';  
    $disp_gallery .= '<select name="filter-sport" class="filter-sport" id="image-filters">';
    $disp_gallery .= '<option class="disabled" value="" ';
    $disp_gallery .= ($_REQUEST['sport'] == '') ? ' selected="selected"': '';
    $disp_gallery .= '>Sort by sport</option>';
	$sporresults = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."flickrsports WHERE published = 1" );   
	foreach ( $sporresults as $sprow )
	{
        $disp_gallery .= '<option value="'. $sprow->id.'"';
        $disp_gallery .= ($sprow->id == $_REQUEST['sport'])? ' selected="selected"' : '';
        $disp_gallery .= '>'.$sprow->sport.'</option>&gt';
    }
    $disp_gallery .= '<option value="">Show All</option>';
    $disp_gallery .= '</select>';
    $disp_gallery .= '</label>';
    $disp_gallery .= '</div>';
    $disp_gallery .= '<div class="styled-select styled-select-2">';
    $disp_gallery .= '<label for="filter-category">';    
    $disp_gallery .= '<select name="filter-category" class="filter-category">';
    $disp_gallery .= '<option class="disabled" value=""' ;
    $disp_gallery .= ($_REQUEST['category'] == '') ? ' selected="selected"': '';
    $disp_gallery .= '>Sort by category</option>';
    $disp_gallery .= '<option value="ambassador" ';
    $disp_gallery .= ('ambassador' == $_REQUEST['category'])? ' selected="selected"' : '';
    $disp_gallery .= '>UNICEF Celebrity Supporters</option>';
    
    $disp_gallery .= '<option value="supporter" ';
    $disp_gallery .= ('supporter' == $_REQUEST['category'])? ' selected="selected"' : '';
    $disp_gallery .= '>Personalities</option>';        

    $disp_gallery .= '<option value="child" ';
    $disp_gallery .= ('child' == $_REQUEST['category'])? ' selected="selected"' : '';
    $disp_gallery .= '>UNICEF stories</option>';
    $disp_gallery .= '<option value="">Show All</option>';
    $disp_gallery .= '</select>';
    $disp_gallery .= '</label>';   
    $disp_gallery .= '</div>';
    $disp_gallery .= '</form>';   
    $disp_gallery .= '<div class="afg-gallery custom-gallery-'. $id .'" id="afg-'.$id.'" style="background-color:'.$bg_color.'; width:'.$gallery_width.'%; color:'.$text_color.'; border-color:'.$bg_color.';">';
    if ($slideshow_option == 'highslide')
        $disp_gallery .= '<div class="highslide-gallery">';
    if ($slideshow_option == 'googleimage'){
        $disp_gallery .= '<div class="og-grid">';
    }
    else $disp_gallery .= '<div class="afg-table" style="width:100%">';   
    $photo_count = 1;
    $column_width = (int)($gallery_width/$columns);

    if ($disable_slideshow) {
        $class = '';
        $rel = '';
        $click_event = '';
    }
    else {
        if ($slideshow_option == 'colorbox') {
            $class = 'class="afgcolorbox"';
            $rel = 'rel="example4'.$id.'"';
            $click_event = "";
        }
        else if ($slideshow_option == 'highslide') {
            $class = 'class="highslide"';
            $rel = "";
            $click_event = 'onclick="return hs.expand(this, {slideshowGroup: '.$id.' })"';
        }
        else if ($slideshow_option == 'flickr') {
            $class = "";
            $rel = "";
            $click_event = 'target="_blank"';
        }
        else if ($slideshow_option == 'googleimage') {
            $class = '';
            $rel = "";
            $click_event = "";
        }
    }
    if ($photo_size == '_s') {
        $photo_width = "width='75'";
        $photo_height = "height='75'";
    }
    else {
        $photo_width = '';
        $photo_height = '';
    }
    $cur_col = 0;
    //print_R($photos);
    if ($total_photos == 0){
        $disp_gallery .=  'Sorry &ndash; no images available';
    }
    foreach($photos as $pid => $photo) {
        $p_title = esc_attr($photo['title']);
        $p_description = esc_attr($photo['description']['_content']);
        $p_description = preg_replace("/\n/", "<br />", $p_description);
        $photo_url = afg_get_photo_url($photo['farm'], $photo['server'],
            $photo['id'], $photo['secret'], $photo_size);
        if ($slideshow_option != 'none') {
            if (isset($photo['url_l'])? $photo['url_l']: '') {
                $photo_page_url = $photo['url_l'];
            }
            else {
                $photo_page_url = afg_get_photo_url($photo['farm'], $photo['server'],
                    $photo['id'], $photo['secret'], '_z');
            }
            if ($photoset_id)
                $photo['owner'] = $user_id;
            $photo_title_text = $p_title;
            if ($slideshow_option == 'highslide' && $p_description) {
                $photo_title_text .= '<br /><span style="font-size:0.8em;">' . $p_description . '</span>';
            }
            //$photo_title_text .= ' • <a style="font-size:0.8em;" href="http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/" target="_blank">View on Flickr</a>';
            $photo_title_text = esc_attr($photo_title_text);
            if ($slideshow_option == 'flickr') {
                $photo_page_url = "http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'];
            }
        }
        else $photo_title_text = '';
        //if ($cur_col % $columns == 0) $disp_gallery .= "<div class='afg-row'>";
        if ( ($photo_count <= $per_page * $cur_page) && ($photo_count > $per_page * ($cur_page - 1)) ) {
        	$percentpercolumn = floor( 100 / $columns) ;
            $mobile = $percentpercolumn;
            if ($percentpercolumn * 2 <= 100) $mobile = $percentpercolumn * 2;
            $extraclass = '';
            if ($slideshow_option == 'googleimage') $extraclass = 'og-child';
            else $extraclass = 'afg-cell';
            if ($cur_col % $columns == 0){
                $disp_gallery .= "<div class='grid-".$percentpercolumn." ".$extraclass." mobile-grid-50 first' >";
            }
            elseif($cur_col % $columns == $columns - 1 ){
                $disp_gallery .= "<div class='grid-".$percentpercolumn." ".$extraclass." mobile-grid-50 last'>";
            }           
            else{
                $disp_gallery .= "<div class='grid-".$percentpercolumn." ".$extraclass." mobile-grid-50' >";
            }
            $pid_len = strlen($photo['id']);
            //print_R($kids[$photo['twinned']]);
            if ($slideshow_option == 'googleimage'){
                $lrg_photo_url = afg_get_photo_url($photo['farm'], $photo['server'],
                        $photo['id'], $photo['secret'], null);
                if (isset($photo['twin']) && $_REQUEST['id'] == $photo['did'] && $_REQUEST['showtwin'] == 1 ){ 
                    $twn_photo_url = afg_get_photo_url($photo['twinfarm'], $photo['twinserver'],
                        $photo['twinid'], $photo['twinsecret'], null);  
                        
                    $flat = afg_get_photo_url(
                            $photo['flatfarm'], 
                            $photo['flatserver'],
                            $photo['flatid'], 
                            $photo['flatsecret'], null);
                   
                    $disp_gallery .= '<a ' . $class . $rel . $click_event . ' href="'.
                        $photo_page_url
                        . '" title="'. htmlspecialchars($photo['title'])
                        . '" data-did="'. $photo['did']                         
                        . '" data-dtype="'. $photo['dtype']                         
                        . '" data-largesrc="'. $lrg_photo_url
                        . '" data-title="'. htmlspecialchars(substr(strip_tags($photo['title']), 0, 80))
                        . '" data-description="'. htmlspecialchars(substr(strip_tags($photo['description']['_content']), 0, 200))
                        . '" data-twin="'. $twn_photo_url
                        . '" data-twintitle="'. htmlspecialchars(substr(strip_tags($photo['twintitle']), 0, 80))
                        . '" data-twindesc="'. htmlspecialchars(substr(strip_tags($photo['twindescription']['_content']), 0, 200)) 
                        . '" data-flat="'. $flat
                        . '" >';
                }
                else{
                    $disp_gallery .= '<a ' . $class . $rel . $click_event . ' href="'.
                        $photo_page_url
                        . '" title="'. htmlspecialchars($photo['title'])
                        . '" data-did="'. $photo['did'] 
                        . '" data-dtype="'. $photo['dtype']                         
                        . '" data-largesrc="'. $lrg_photo_url
                        . '" data-title="'. htmlspecialchars(substr(strip_tags($photo['title']), 0, 80))
                        . '" data-description="'. htmlspecialchars(substr(strip_tags($photo['description']['_content']), 0, 200)) . '" >';               
                }
            }
            elseif ($slideshow_option != 'none')
                $disp_gallery .= "<a $class $rel $click_event href='{$photo_page_url}' title='{$photo['title']}'>";
            if ($custom_size) {
                $timthumb_script = BASE_URL . "/afg_img_rsz.php?src=";
                if($photo['width_l'] > $photo['height_l']) {
                    $timthumb_params = "&q=100&w=$custom_size";
                    if ($custom_size_square == 'true')  $timthumb_params .= "&h=$custom_size";
                }
                else {
                    $timthumb_params = "&q=100&h=$custom_size";
                    if ($custom_size_square == 'true')  $timthumb_params .= "&w=$custom_size";
                }
            }
            else {
                $timthumb_script = "";
                $timthumb_params = "";
            }
            $disp_gallery .= '<img class="afg-img" title="'. htmlspecialchars($photo['title']) .'" src="'.$timthumb_script.$photo_url.$timthumb_params.'" alt="'. htmlspecialchars($photo_title_text) .'" />';
            if ($slideshow_option != 'none')
                $disp_gallery .= "</a>";
            if ($size_heading_map[$photo_size] && $photo_title == 'on') {
                if ($group_id || $gallery_id)
                    $owner_title = "- by <a href='http://www.flickr.com/photos/{$photo['owner']}/' target='_blank'>{$photo['ownername']}</a>";
                else
                    $owner_title = '';
                $disp_gallery .= "<div class='afg-title' style='font-size:{$size_heading_map[$photo_size]}'>{$p_title} $owner_title</div>";
            }
            if($photo_descr == 'on' && $photo_size != '_s' && $photo_size != '_t') {
                $disp_gallery .= "<div class='afg-description'>" .
                    $photo['description']['_content'] . "</div>";
            }
            $cur_col += 1;
            $disp_gallery .= '</div>';
        }
        else {
            if ($pagination == 'on' && $slideshow_option != 'none') {
                if ($slideshow_option == 'highslide') {
                    $photo_url = afg_get_photo_url($photo['farm'], $photo['server'],
                        $photo['id'], $photo['secret'], '_s');
                }
                else
                    $photo_url = '';
                if ($slideshow_option == 'highslide')
                    $photo_src_text = "src='$photo_url'";
                else
                    $photo_src_text = "";
                if ($slideshow_option != 'googleimage'){
                    $disp_gallery .= "<a style='display:none' $class $rel $click_event href='$photo_page_url'" .
                        " title='{$photo['title']}'>" .
                        ' <img class="afg-img" alt="'.$photo_title_text.'" '.$photo_src_text.' width="75" height="75"></a> ';
                }
            }
        }
        //if ($cur_col % $columns == 0) $disp_gallery .= '</div>';
        $photo_count += 1;
    }
    if ($cur_col % $columns != 0) $disp_gallery .= '</div>';
    if ($slideshow_option !== 'googleimage')$disp_gallery .= '</div>';
    if ($slideshow_option == 'highslide') $disp_gallery .= "</div>";
    // Pagination
    if ($pagination == 'on' && $total_pages > 1) {
        $disp_gallery .= "<div class='afg-pagination'>";
        if ($cur_page == 1) {
            $disp_gallery .="<span class='afg-page afg-prev'>&nbsp;&#171; previous&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .="<span class='afg-cur-page afg-pagin'> 1 </span>&nbsp;";
        }
        else {
            $prev_page = $cur_page - 1;
            $disp_gallery .= "<a class='afg-page afg-prev' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$prev_page#afg-{$id}' title='Prev Page'>&nbsp;&#171; previous </a>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .= "<a class='afg-page afg-pagin' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=1#afg-{$id}' title='Page 1'> 1 </a>&nbsp;";
        }
        if ($cur_page - 2 > 2) {
            $start_page = $cur_page - 2;
            $end_page = $cur_page + 2;
            $disp_gallery .= " ... ";
        }
        else {
            $start_page = 2;
            $end_page = 6;
        }
        for ($count = $start_page; $count <= $end_page; $count += 1) {
            if ($count > $total_pages) break;
            if ($cur_page == $count)
                $disp_gallery .= "<span class='afg-cur-page afg-pagin'>&nbsp;{$count}&nbsp;</span>&nbsp;";
            else
                $disp_gallery .= "<a class='afg-page afg-pagin' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$count}#afg-{$id}' title='Page {$count}'>&nbsp;{$count} </a>&nbsp;";
        }
        if ($count < $total_pages) $disp_gallery .= " ... ";
        if ($count <= $total_pages)
            $disp_gallery .= "<a class='afg-page afg-pagin' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$total_pages}#afg-{$id}' title='Page {$total_pages}'>&nbsp;{$total_pages} </a>&nbsp;";
        if ($cur_page == $total_pages) $disp_gallery .= "&nbsp;&nbsp;&nbsp;<span class='afg-page afg-next'>&nbsp;next &#187;&nbsp;</span>";
        else {
            $next_page = $cur_page + 1;
            $disp_gallery .= "&nbsp;&nbsp;&nbsp;<a class='afg-page afg-next' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$next_page#afg-{$id}' title='Next Page'> next &#187; </a>&nbsp;";
        }
        $disp_gallery .= "</div>";
    }
    if ($credit_note == 'on') {
        $disp_gallery .= "<br />";
        $disp_gallery .= "<div class='afg-credit'>Powered by " .
            "<a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin'" .
            "title='Awesome Flickr Gallery by Ronak Gandhi'/>AFG</a>";
        $disp_gallery .= "</div>";
    }
    $disp_gallery .= "</div>";
    preg_match("/afg_pic_id=((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s\"]*))/", urldecode($cur_page_url), $pmatches);
    $photoInfo = array();
    $displayImage = False;
    if ($pmatches) {
        $cur_pic = ($pmatches[1]);
        //echo $cur_pic;
        //$match_pos = strpos($cur_page_url, "afg{$id}_pic_id=$cur_page") - 1;
        $regexstr = '~https?:\/\/(?:[\w]+\.)*(?:flic\.kr|flickr\.com|staticflickr\.com)(?:\/photos)?\/[^\/]+\/([0-9a-zA-Z]+)[^\s]*~ix';
        preg_match_all($regexstr, $cur_pic, $jmatches, PREG_SET_ORDER);
        //print_R($jmatches);
        $photoID = $jmatches[0][1];
        if (strpos($jmatches[0][0], "flic.kr") !== false) {
            $photoID = $flickrDecode($photoID);
        }  
        //echo $photoID;
        $displayImage = True;
        $photoInfo = $pf->photos_getInfo($photoID);
        //echo $photoInfo['photo']['title']['_content'];
    }
    if ($displayImage && $slideshow_option != 'googleimage'){
        $disp_gallery .= '<script type="text/javascript"> '."\n";
        $disp_gallery .= 'jQuery(document).ready(function(){'."\n";
        $disp_gallery .= '  jQuery.afgcolorbox({'."\n";
        $disp_gallery .= '      title:"'.$photoInfo['photo']['title']['_content'].'", '."\n";
        $disp_gallery .= '      href:"'. $cur_pic .'",'."\n";
        $disp_gallery .= '      maxWidth: "70%",'."\n";
        $disp_gallery .= '      maxHeight: "70%",'."\n";
        $disp_gallery .= '      onComplete:function(){'."\n";
        $disp_gallery .= '          var imsrc = jQuery(\'#afgcolorbox\').find(\'img\').attr(\'src\');'."\n";
        $disp_gallery .= '          var hhref = \'\';'."\n";
        $disp_gallery .= '          if (window.location.href.indexOf("?") != -1){'."\n";
        $disp_gallery .= '              hhref = window.location.href +\'&\' + \'afg_pic_id=\'+ encodeURIComponent(imsrc);'."\n";
        $disp_gallery .= '          }'."\n";
        $disp_gallery .= '          else{'."\n";
        $disp_gallery .= '              hhref = window.location.href +\'?\' + \'afg_pic_id=\'+ encodeURIComponent(imsrc);'."\n";
        $disp_gallery .= '          }'."\n";
        $disp_gallery .= '          jQuery(\'#cboxTitle\').append(\'<div id="extra-info"><ul class="gallery-social"><li class="twitter"><a href="http://www.twitter.com/share?url=\'+ hhref +\'&text=%23RAMPUP"><span>Twitter</span></a></li><li class="facebook"><a href="http://www.facebook.com/sharer/sharer.php?u=\'+ hhref +\'"><span>Facebook</span></a></li></ul></div>\');'."\n";
        $disp_gallery .= '      },'."\n";     
        $disp_gallery .= '  });'."\n";
        $disp_gallery .= '}); '."\n";
        $disp_gallery .= '</script>';     
    }
    if ($slideshow_option == 'googleimage'){
        $disp_gallery.= "\n".'<script type="text/javascript">';
        $disp_gallery.= "\n\t".'$jq = jQuery.noConflict();';
        $disp_gallery.= "\n\t".'$jq(function() {';
        $disp_gallery.= "\n\t\t".'Grid.init();';
        $disp_gallery.= "\n\t".'});';    
/*       
        $disp_gallery.= "\n".'$jq(window).load(function() {(';
        $disp_gallery.= "\n".'function() {';
        $disp_gallery.= "\n".'Grid.init();})';
        $disp_gallery.= "\n".'});';
*/
        $disp_gallery.= "\n".'</script>';
    }
    return $disp_gallery;
}
function afgflickr_install(){
    global $wpdb;
    global $afgflickr_db_version;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $table_namea = $wpdb->prefix . "flickruploads";
    $sqla = "CREATE TABLE IF NOT EXISTS $table_namea (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `fname` varchar(50) NOT NULL,
        `lname` varchar(50) NOT NULL,
        `email` varchar(250) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `sport` bigint(20) unsigned DEFAULT 0 NOT NULL ,
        `age` varchar(20) NOT NULL,
        `gender` varchar(5) NOT NULL,
        `imagetitle` varchar(250) NOT NULL,
        `imagestory` text,
        `tsandcs` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `consent` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `comms` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `commsphone` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `commsemail` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        `photoid` varchar(50) NOT NULL,
        `photosecret` varchar(50) NOT NULL,
        `farm` varchar(50) NOT NULL,
        `server` varchar(50) NOT NULL,
        `twinlid` bigint(20) unsigned DEFAULT 0 NOT NULL,
        `flatid` varchar(50) NOT NULL,
        `flatsecret` varchar(50) NOT NULL,
        `flatfarm` varchar(50) NOT NULL,
        `flatserver` varchar(50) NOT NULL,
        `status` smallint(3) DEFAULT 0 NOT NULL,
        PRIMARY KEY (`id`)
    );";
    dbDelta( $sqla );
    /*
    $table_nameb = $wpdb->prefix . "flickrsports";   
    $sqlb = "CREATE TABLE IF NOT EXISTS $table_nameb (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `sport` varchar(50) NOT NULL,
        `published` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        PRIMARY KEY (`id`)
    );";    
    dbDelta( $sqlb );
    $table_namec = $wpdb->prefix . "flickrtwins";   
    $sqlc = "CREATE TABLE IF NOT EXISTS $table_namec (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `sport` varchar(20) NOT NULL,
        `age` varchar(20) NOT NULL,
        `gender` varchar(5) NOT NULL,
        `imagetitle` varchar(250) NOT NULL,
        `imagestory` text,
        `published` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        `photoid` varchar(50) NOT NULL,
        `photosecret` varchar(50) NOT NULL,   
        `farm` varchar(50) NOT NULL,
        `server` varchar(50) NOT NULL,               
        PRIMARY KEY (`id`)
    );"; 
    dbDelta( $sqlc );
    $table_named = $wpdb->prefix . "flickrkids";   
    $sqld = "CREATE TABLE IF NOT EXISTS $table_named (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `sport` varchar(20) NOT NULL,
        `age` varchar(20) NOT NULL,
        `gender` varchar(5) NOT NULL,
        `imagetitle` varchar(250) NOT NULL,
        `imagestory` text,
        `published` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        `photoid` varchar(50) NOT NULL,
        `photosecret` varchar(50) NOT NULL,   
        `farm` varchar(50) NOT NULL,
        `server` varchar(50) NOT NULL,               
        PRIMARY KEY (`id`)
    );"; 
    dbDelta( $sqld ); 
    $table_namee = $wpdb->prefix . "flickrcelebs";   
    $sqle = "CREATE TABLE IF NOT EXISTS $table_namee (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `sport` varchar(20) NOT NULL,
        `gender` varchar(5) NOT NULL,
        `celebname` varchar(250) NOT NULL,
        `imagetitle` varchar(250) NOT NULL,
        `imagestory` text,
        `published` smallint(3) unsigned DEFAULT 0 NOT NULL,
        `unicef` smallint(3) unsigned DEFAULT 0 NOT NULL,        
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        `photoid` varchar(50) NOT NULL,
        `photosecret` varchar(50) NOT NULL,    
        `farm` varchar(50) NOT NULL,
        `server` varchar(50) NOT NULL,                
        PRIMARY KEY (`id`)
    );";  
    dbDelta( $sqle );    */
    add_option( "afgflickr_db_version", $afgflickr_db_version );   
}