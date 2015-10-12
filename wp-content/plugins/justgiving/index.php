<?php
/**
 * @package JustGiving
 * @version 1.6
 */
/*
Plugin Name: JustGiving 
Description: An evolving plugin to interface with JustGiving and other APIs to allow users to register justgiving fundraising pages, teams and events
Author: David Gurney
Version: 1.6
*/

function obsafe_print_r($var, $return = false, $html = false, $level = 0) {
    $spaces = "";
    $space = $html ? "&nbsp;" : " ";
    $newline = $html ? "<br />" : "\n";
    for ($i = 1; $i <= 6; $i++) {
        $spaces .= $space;
    }
    $tabs = $spaces;
    for ($i = 1; $i <= $level; $i++) {
        $tabs .= $spaces;
    }
    if (is_array($var)) {
        $title = "Array";
    } elseif (is_object($var)) {
        $title = get_class($var)." Object";
    }
    $output = $title . $newline . $newline;
    foreach($var as $key => $value) {
        if (is_array($value) || is_object($value)) {
            $level++;
            $value = obsafe_print_r($value, true, $html, $level);
            $level--;
        }
        $output .= $tabs . "[" . $key . "] => " . $value . $newline;
    }
    if ($return) return $output;
    else echo $output;
}
function jgcallback($buffer) {
	$wpjg_generalSettings = get_option('jg_general_settings');
	if ((int)$wpjg_generalSettings['lolagrove'] == 1){
		$urlparms = parse_url(jg_curpageurl());
		parse_str($urlparms['query'], $get_array);
		if (isset($get_array['from']) && $get_array['from'] == 'lolagrove'){
			if(session_id() == '' || !isset($_SESSION)) {
				// session isn't started
				session_start();				
			}
			$_SESSION['lolagrove'] = json_encode($get_array);
		}
		//error_log(obsafe_print_r($_SESSION,true));		
	}
    return str_replace('replacing','width',$buffer);
}

function jgbuffer_start() {ob_start("jgcallback"); }
function jgbuffer_end() { ob_end_flush(); }

if (!is_admin()) {
    add_action('after_setup_theme', 'jgbuffer_start');
    add_action('shutdown', 'jgbuffer_end');  
}
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define( 'JUSTGIVING_VERSION', '1.0.0' );
define( 'JG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'JG_PLUGIN_URL', str_replace("http:","",plugins_url( 'justgiving' ) ) );

include_once(JG_PLUGIN_DIR . '/admin/jg_admin_settings.php');


function sendadvocate($email, $name, $vars , $page = 0 ){
    global $thisUrl;
    $emailSubj = "Thank you!";

    include_once(JG_PLUGIN_DIR.'/lib/class.html.mime.mail.inc');
    define('CRLF', "\r\n", TRUE);
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    
    $mail = new html_mime_mail(array("X-Mailer: ".$wpjg_generalSettings['mailer_id']));
    if ($wpjg_generalSettings['smtp_uname'] != "" || $wpjg_generalSettings['smtp_pword'] != "")
        $smtpauth = TRUE;
    else
        $smtpauth = FALSE;

    $tosend = 'advocate_page'  ;    

    if (file_exists(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt') && file_exists(JG_PLUGIN_DIR.'/email/'.$tosend.'.html')){
        $email_body = fread($fp = fopen(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt', 'r'), filesize(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt'));
        fclose($fp);
        $html_email_body = fread($fp = fopen(JG_PLUGIN_DIR.'/email/'.$tosend.'.html', 'r'), filesize(JG_PLUGIN_DIR.'/email/'.$tosend.'.html'));
        fclose($fp);    
        
        foreach ($vars as $key => $val){
            if(strpos($email_body, '['.trim($key).']')){
                $email_body = preg_replace("/\[$key\]/", $val, $email_body);
            }   
        }
        $email_body = preg_replace('/\[(\S.*?)\]/', '', $email_body);    
        if ($html_email_body){
            foreach ($vars as $key => $val){
                if(strpos($html_email_body, '['.trim($key).']')){
                    $html_email_body = preg_replace("/\[$key\]/", $val, $html_email_body);
                }
            }
            $html_email_body = preg_replace('/\[(\S.*?)\]/', '', $html_email_body);     
            $mail->add_html($html_email_body, $email_body, JG_PLUGIN_DIR.'/email/');
            //$mail->add_html($html_email_body, $email_body, 'img');
        }
        else{
            $mail->add_text($email_body);
        }			
        $mail->build_message();
        $headers = array(
            'From: "'.$wpjg_generalSettings['friendly_name'].'" <'.$wpjg_generalSettings['email_from'].'>',
            'To: "'. $name .'" <'. $email .'>',
            'Subject: '.$emailSubj,		// not have to match the recipients list.
            'Reply-To: '.$wpjg_generalSettings['reply_to']
        );
   
        if ($wpjg_generalSettings['useSMTP']){
            include_once(JG_PLUGIN_DIR.'/lib/class.smtp.inc');
            $params = array(
                'host' => $wpjg_generalSettings['smtp_server'],	// Mail server address
                'port' => $wpjg_generalSettings['smtp_port'],		// Mail server port
                'helo' => $wpjg_generalSettings['smtp_helo'],	// Use your domain here.
                'auth' => $smtpauth,	// Whether to use authentication or not.
                'user' => $wpjg_generalSettings['smtp_uname'],	// Authentication username
                'pass' => $wpjg_generalSettings['smtp_pword']	// Authentication password
            );
            //echo '<PRE>'.htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)).'</PRE>';               
            $smtp =& smtp::connect($params);
            $send_params = array(
                'from'		=> $wpjg_generalSettings['email_from'],	// The return path
                'recipients'	=> $email,	// Can be more than one address in this array.
                'headers'	=> $headers);       
            $mail->smtp_send($smtp, $send_params);
        }
        else{
            $mail->send($name, $email, $wpjg_generalSettings['friendly_name'], $wpjg_generalSettings['email_from'], $emailSubj, $headers);
        }
        //file_put_contents( '/var/www/html/test6/PHP_errors.log' ,print_R('advocate'. htmlentities($mail->get_rfc822($name, $email, $wpjg_generalSettings['friendly_name'], $wpjg_generalSettings['email_from'], $emailSubj, $headers)), true), FILE_APPEND );                 
        //echo '<PRE>'.htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)).'</PRE>';               
    }
}

function sendthanks($email, $name, $vars , $page = 0 ){
    global $thisUrl;
    $emailSubj = "Thank you!";

    include_once(JG_PLUGIN_DIR.'/lib/class.html.mime.mail.inc');
    define('CRLF', "\r\n", TRUE);
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    
    $mail = new html_mime_mail(array("X-Mailer: ".$wpjg_generalSettings['mailer_id']));
    if ($wpjg_generalSettings['smtp_uname'] != "" || $wpjg_generalSettings['smtp_pword'] != "")
        $smtpauth = TRUE;
    else
        $smtpauth = FALSE;

    $tosend = 'thanks_page'  ;    

    if (file_exists(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt') && file_exists(JG_PLUGIN_DIR.'/email/'.$tosend.'.html')){
        $email_body = fread($fp = fopen(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt', 'r'), filesize(JG_PLUGIN_DIR.'/email/'.$tosend.'.txt'));
        fclose($fp);
        $html_email_body = fread($fp = fopen(JG_PLUGIN_DIR.'/email/'.$tosend.'.html', 'r'), filesize(JG_PLUGIN_DIR.'/email/'.$tosend.'.html'));
        fclose($fp);    
        
        foreach ($vars as $key => $val){
            if(strpos($email_body, '['.trim($key).']')){
                $email_body = preg_replace("/\[$key\]/", $val, $email_body);
            }   
        }
        $email_body = preg_replace('/\[(\S.*?)\]/', '', $email_body);    
        if ($html_email_body){
            foreach ($vars as $key => $val){
                if(strpos($html_email_body, '['.trim($key).']')){
                    $html_email_body = preg_replace("/\[$key\]/", $val, $html_email_body);
                }
            }
            $html_email_body = preg_replace('/\[(\S.*?)\]/', '', $html_email_body);     
            $mail->add_html($html_email_body, $email_body, JG_PLUGIN_DIR.'/email/');
            //$mail->add_html($html_email_body, $email_body, 'img');
        }
        else{
            $mail->add_text($email_body);
        }			
        $mail->build_message();
        $headers = array(
            'From: "'.$wpjg_generalSettings['friendly_name'].'" <'.$wpjg_generalSettings['email_from'].'>',
            'To: "'. $name .'" <'. $email .'>',
            'Subject: '.$emailSubj,		// not have to match the recipients list.
            'Reply-To: '.$wpjg_generalSettings['reply_to']
        );
   
        if ($wpjg_generalSettings['useSMTP']){
            include_once(JG_PLUGIN_DIR.'/lib/class.smtp.inc');
            $params = array(
                'host' => $wpjg_generalSettings['smtp_server'],	// Mail server address
                'port' => $wpjg_generalSettings['smtp_port'],		// Mail server port
                'helo' => $wpjg_generalSettings['smtp_helo'],	// Use your domain here.
                'auth' => $smtpauth,	// Whether to use authentication or not.
                'user' => $wpjg_generalSettings['smtp_uname'],	// Authentication username
                'pass' => $wpjg_generalSettings['smtp_pword']	// Authentication password
            );
            //echo '<PRE>'.htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)).'</PRE>';               
            $smtp =& smtp::connect($params);
            $send_params = array(
                'from'		=> $wpjg_generalSettings['email_from'],	// The return path
                'recipients'	=> $email,	// Can be more than one address in this array.
                'headers'	=> $headers);       
            $mail->smtp_send($smtp, $send_params);
        }
        else{
            $mail->send($name, $email, $wpjg_generalSettings['friendly_name'], $wpjg_generalSettings['email_from'], $emailSubj, $headers);
        }
        //file_put_contents( '/var/www/html/test6/PHP_errors.log' ,print_R('thanksemail'. htmlentities($mail->get_rfc822($name, $email, $wpjg_generalSettings['friendly_name'], $wpjg_generalSettings['email_from'], $emailSubj, $headers)), true), FILE_APPEND );         
        //echo '<PRE>'.htmlentities($mail->get_rfc822($name, $email, $friendly_name, $email_from, $email_subj, $headers)).'</PRE>';               
    }
}

add_action('activated_plugin','save_error');
function save_error(){
    update_option('plugin_error',  ob_get_contents());
}

global $justgiving_db_version;
$justgiving_db_version = "1.0";

register_activation_hook( __FILE__, 'justgiving_install' );
include_once(JG_PLUGIN_DIR.'/front-end/jg.login.php');       
include_once(JG_PLUGIN_DIR.'/front-end/jg.register.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.recover.password.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.create.page.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.viewuser.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.paypal.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.stripe.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.sagepay.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.justgiving.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.leaderboard.php');        		
include_once(JG_PLUGIN_DIR.'/front-end/jg.thankyou.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.teadmadd.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.teamedit.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.accountchoose.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.eventadd.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.eventlist.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.eventdetail.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.pagedetail.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.pagecomplete.php');
include_once(JG_PLUGIN_DIR.'/front-end/jg.eventdate.php');

if (!is_admin()) {    
    add_shortcode('jg-login', 'jg_front_end_login');
    add_shortcode('jg-logout', 'jg_front_end_logout');
    add_shortcode('jg-register', 'jg_front_end_register');
    add_shortcode('jg-recover-password', 'jg_front_end_password_recovery');
    add_shortcode('jg-create-page', 'jg_front_end_create_page');
    add_shortcode('jg-view-user', 'jg_front_end_view_user');
    add_shortcode('jg-paypal', 'jg_front_end_paypal');   
    add_shortcode('jg-stripe', 'jg_front_end_stripe');   
    add_shortcode('jg-sagepay', 'jg_front_end_sagepay');   
    add_shortcode('jg-justgiving', 'jg_front_end_justgiving');   
    add_shortcode('jg-leaderboard', 'jg_front_end_leaderboard');     
    add_shortcode('jg-thankyou', 'jg_front_end_thankyou');
    add_shortcode('jg-teamlist', 'jg_teamlist'); 
    add_shortcode('jg-teamadd', 'jg_teamadd');   
    add_shortcode('jg-account', 'jg_front_end_choose');
    add_shortcode('jg-eventadd', 'jg_front_end_eventadd');
    add_shortcode('jg-eventlist', 'jg_front_end_eventlist');
    add_shortcode('jg-eventdetail', 'jg_front_end_eventdetail');
    add_shortcode('jg-pagedetail', 'jg_front_end_pagedetail');
    add_shortcode('jg-pagecomplete', 'jg_front_end_pagecomplete');
    add_shortcode('jg-eventdate', 'jg_front_end_eventdate');
    
    // Stick in the scripts and localize them if necessary    
    add_action('wp_enqueue_scripts', 'justgiving_enqueuescripts'); 
}
add_action( 'plugins_loaded', 'teamstuff' );    
add_action( 'wp_ajax_nopriv_ajaxjustgiving_login', 'ajaxjustgiving_login' );
add_action( 'wp_ajax_ajaxjustgiving_login', 'ajaxjustgiving_login' );     
add_action( 'wp_ajax_nopriv_ajaxjustgiving_register', 'ajaxjustgiving_register' );
add_action( 'wp_ajax_ajaxjustgiving_register', 'ajaxjustgiving_register' );    
add_action( 'wp_ajax_nopriv_ajaxjustgiving_recover', 'ajaxjustgiving_recover' );
add_action( 'wp_ajax_ajaxjustgiving_recover', 'ajaxjustgiving_recover' );
add_action( 'wp_ajax_nopriv_ajaxjustgiving_createpage', 'ajaxjustgiving_createpage' );
add_action( 'wp_ajax_ajaxjustgiving_createpage', 'ajaxjustgiving_createpage' );
add_action( 'wp_ajax_jg_autocompletesearch', 'jg_autocomplete_suggestions' );
add_action( 'wp_ajax_nopriv_jg_autocompletesearch', 'jg_autocomplete_suggestions' );
add_action( 'wp_ajax_jg_listpages', 'jg_listpages' );
//add_action( 'wp_ajax_nopriv_jg_listpages', 'jg_listpages' );

$wpjg_admin = JG_PLUGIN_DIR . '/admin/';	

if (file_exists ( $wpjg_admin.'class.admin.php' ))	require_once($wpjg_admin.'class.admin.php');
$JG_Admin = new JG_Admin();

register_activation_hook( __FILE__, array( $JG_Admin, 'justgiving_activate' ) );
register_deactivation_hook( __FILE__, array( $JG_Admin, 'justgiving_deactivate' ) );
add_action( 'admin_init', array( $JG_Admin, 'justgiving_initialize' ) );
add_action( 'admin_menu', array( $JG_Admin, 'justgiving_admin' ) );

if(!function_exists('MyCheckDate')){
    function MyCheckDate( $postedDate ) {
        if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $postedDate)){
            list( $year , $month , $day ) = explode('-',$postedDate);
            return( checkdate( $month , $day , $year ) );
        } else {
            return( false );
        }
    }
}

if(!function_exists('jg_listpages')){
    function jg_listpages() {
        $pages = array();
        $pages[] = array('ID'=>'', 'title'=> 'Please select');
        $the_query = new WP_Query( array('post_type' => 'page', 'post_status' => 'publish', 'posts_per_page'=>-1 ) );
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $pages[] = array('ID' => get_the_ID() , 'title' => get_the_title());
            }
        }       
        echo ltrim(trim(json_encode($pages)));
        wp_die(); // ajax call must die to avoid trailing 0 in your response
    }
}        

if(!function_exists('jg_curpageurl')){
    function jg_curpageurl() {
        $pageURL = 'http';
        if ($_SERVER["SERVER_PORT"] == "443")
			$pageURL .= "s";
        $pageURL .= "://";
        $pageURL .= $_SERVER["HTTP_HOST"];
        /*
        if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
			$pageURL .= ":".$_SERVER["SERVER_PORT"];        
        */
        $pageURL .= $_SERVER["REQUEST_URI"];
        return $pageURL;
    }
}

if(!function_exists('jg_generate_random_username')){
    function jg_generate_random_username($sentEmail){
        $email = '';    
        for($i=0; $i<strlen($sentEmail); $i++){
            if (($sentEmail[$i] === '@') || ($sentEmail[$i] === '_') || ($sentEmail[$i] === '-') || ($sentEmail[$i] === '.'))
                break;
            else $email .= $sentEmail[$i];
        }
        $username = 'pbUser'.$email.mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
        while (username_exists($username)){
            $username = 'pbUser'.$email.mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
        }
        return $username;
    }
}

if(!function_exists('jg_check_missing_http')){
    function jg_check_missing_http($redirectLink) {
        //#^(?:[a-z\d]+(?:-+[a-z\d]+)*\.)+[a-z]+(?::\d+)?(?:/|$)#i

        $br = preg_match('#^(https?:\/\/)#i', $redirectLink);        
        return $br;
    }
}

function justgiving_enqueuescripts(){
    
    wp_enqueue_style( 'justgiving', JG_PLUGIN_URL.'/css/justgiving.css' );    
    wp_enqueue_script('justgiving-raised', JG_PLUGIN_URL.'/js/justgiving.js', array('jquery', 'modernizr'), '1', true);
    wp_localize_script('justgiving-raised', 'ajaxjustgiving', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('justgiving-raised', 'ajaxsiteroot', array('url' => get_home_url('/')));
    $wpjg_generalSettings = get_option('jg_general_settings');
    if (trim($wpjg_generalSettings['stripe_key']) != '' && trim($wpjg_generalSettings['stripe_pkey']) != ''){
        wp_enqueue_script('justgiving-stripe', 'https://js.stripe.com/v2/', array(), null, false );
    }
}

function justgiving_install() {
    global $wpdb;
    global $justgiving_db_version;
    $table_name = $wpdb->prefix . "jgusers";
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
      `country` varchar(40) NOT NUll,
      `packbypost` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `cpage` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `hasaccount` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `userEnc` varchar(255) NOT NULL,  
      `pageurl` varchar(255) NOT NULL,
      `pageid` varchar(255) NOT NULL,
      `signupdate` int(11) unsigned NOT NULL,
      `optin` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `heardabout` varchar(255) NOT NUll,
      `eventstart` bigint(20) unsigned NOT NULL,
      `eventend` bigint(20) unsigned NOT NULL,
      `work` varchar(255) NOT NUll,
      `worktown` varchar(255) NOT NUll,
      `workcountry` varchar(255) NOT NUll,
      `workpostcode` varchar(255) NOT NUll,
      `workwhere` varchar(255) NOT NUll,
      `dofereln` varchar(255) NOT NUll,
      `dofegold` varchar(255) NOT NUll,
      `dofeevent` varchar(255) NOT NUll,
      `tshirt` varchar(255) NOT NUll,
      `discountcode` varchar(255) NOT NUll,
      `region` varchar(255) NOT NUll,
      `charityoptin` varchar(255) NOT NUll,
      `signoff` varchar(255) NOT NUll,
      `paidaccess` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `paytoken` varchar(300) NOT NULL,
      `txn_id` varchar(600) NOT NULL,
      `tsandcs` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      `advocate` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
      UNIQUE (`email`),
      PRIMARY KEY (`id`)  
    );";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    $table_nameb = $wpdb->prefix . "jgteams";   
    $sqlb = "CREATE TABLE IF NOT EXISTS $table_nameb (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `owner` bigint(20) unsigned NOT NULL DEFAULT 0,
        `teamstartpage` text,
        `teamname` varchar(255),
        `teamfriendly` varchar(255),
        `teamshortname` varchar(255),
        `teamstory` text,
        `teamtargettype` enum('Aggregate','Fixed'),
        `teamtarget` varchar(255),
        `teamfbpage` text,
        `teamtwpage` text,
        `teammembers` text,
        `teamtype` enum('Open', 'Closed', 'ByInvitationOnly'),        
        `submittedtime` timestamp default '0000-00-00 00:00:00',
        `lastmodified` timestamp default now() on update now(),
        PRIMARY KEY (`id`)
    );";    
    dbDelta( $sqlb );  

    $table_namec = $wpdb->prefix . "jgjustgiving";   
    $sqlc = "CREATE TABLE IF NOT EXISTS $table_namec (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `owner` bigint(20) unsigned NOT NULL DEFAULT 0,
		`reference` varchar(255),
		`extref` varchar(255),
		`amount` decimal(19,2),
        `paid` smallint(3) UNSIGNED DEFAULT 0 NOT NUll,
		`txn_id` varchar(255),
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        PRIMARY KEY (`id`)
    );";    
    dbDelta( $sqlc );  
    
    
    $table_named = $wpdb->prefix . "jgevents";   
    $sqld = "CREATE TABLE IF NOT EXISTS $table_named (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `owner` bigint(20) unsigned NOT NULL DEFAULT 0,
        `jgeventid` bigint(20) unsigned NOT NULL DEFAULT 0,
        `eventname` text,
        `eventdescr` text,
        `eventcomplete` varchar(255),
        `eventexpiry` varchar(255),
        `eventstart` varchar(255),
        `eventtype` enum('Running_Marathons', 'Treks', 'Walks', 'Cycling', 'Swimming', 'Triathlons', 'Parachuting_Skydives', 'OtherSportingEvents', 'Birthday', 'Wedding', 'OtherCelebration', 'Christening', 'InMemory', 'Anniversaries', 'NewYearsResolutions', 'Christmas', 'OtherPersonalChallenge', 'CharityAppeal', 'IndividualAppeal', 'CompanyAppeal', 'PersonalRunning_Marathons', 'PersonalTreks', 'PersonalWalks', 'PersonalCycling', 'PersonalSwimming', 'PersonalTriathlons', 'PersonalParachuting_Skydives') DEFAULT 'OtherCelebration',
        `location` text,
        `street_number` varchar(255),
        `street_name` varchar(255),
        `city` varchar(255),
        `state` varchar(255),
        `postcode` varchar(255),
        `country` varchar(255),  
        `lat` varchar(255),
        `lng` varchar(255),        
        `submittedtime` timestamp default '0000-00-00 00:00:00' ,
        `lastmodified` timestamp default now() on update now() ,
        PRIMARY KEY (`id`)
    );";    
    dbDelta( $sqld );      
    
    add_option( "justgiving_db_version", $justgiving_db_version );
}