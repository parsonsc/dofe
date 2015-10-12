<?php
function rfe_front_end_thanks($atts){
    if(session_id() == '' || !isset($_SESSION)) {
        // session isn't started
        session_start();
    }
    ob_start();
    global $current_user;
    global $wp_roles;
    global $wpdb;
    global $error;	
    global $js_shortcode_on_front;
         
    extract(shortcode_atts(array('template' => '', 'logout' => 0, 'paid' => 0), $atts));
    /*
    if ( trim($_SESSION['userEnc']) == '' || !isset($_REQUEST['team']) ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (jg_check_missing_http($redirectLink))
                $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }
    */
    /*
    $testname = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgteams WHERE `teamshortname`='".$_REQUEST['team']."';");  
    if ($testname == null) {
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (jg_check_missing_http($redirectLink))
                $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }    
    */
    /*
    if ($logout == null) {
        $logout = trim($logout);
        if (intval($logout) != 0)
            $logout = get_permalink($logout);
        else{
            if (jg_check_missing_http($logout))
                $logout = 'http://'. $logout;
        }       
    } 
    */    
    
    $post = get_post($_SESSION['post']);
    $post_meta = get_post_meta($_SESSION['post']);
   // print_R($teams);
    require_once(RFE_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = RFE_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = RFE_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = RFE_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = RFE_PLUGIN_DIR.'/smarty/cache/';

    $smarty->assign('post', $post);    
    $smarty->assign('post_meta', $post_meta);    
    $smarty->assign('pluginurl', RFE_PLUGIN_URL);    
    $smarty->assign('templateurl', get_template_directory_uri()); 
    $smarty->assign('settings', $wpjg_generalSettings);
    $smarty->assign('homepage', get_home_url('/'));
    
    $smarty->assign('logout', $logout);
    if ($template != '') $smarty->display($template);    
    else $smarty->display('thanks.html');       
    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}   


?>