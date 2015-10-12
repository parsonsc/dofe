<?php
function jg_front_end_thankyou($atts){
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
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
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
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
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
            if (!jg_check_missing_http($logout)) $logout = 'http://'. $logout;
        }       
    } 
    */    
    $wpjg_generalSettings = get_option('jg_general_settings'); 

    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A);     
    if ((!isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1) || ($result['paidaccess'] == 0 && $wpjg_generalSettings['paidaccess'] == 1))
    {
        $redirectLink = trim($paid);
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }    
    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);    
    

    // get teams for pages
    $team = get_transient('justgiving_team_'.$_REQUEST['team'])  ;
    if (DEBUG){
        $team = NULL;
    }  
    if (!$team){
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgteams WHERE `teamshortname`='".$_REQUEST['team']."';", ARRAY_A);  

        $res = $client->Team->Get($_REQUEST['team']);
        $vars = array();
        if ($res) {
            $vars = array_merge(get_object_vars($res), $result);
            $vars['numMembers'] = count($res->teamMembers);
        }
        else $vars = array_merge(get_object_vars($result));
        $team = $vars ; 
        unset($team['teamMembers']);
    }
    if (!DEBUG && (!isset($_GET['id']) || intval($_GET['id']) == 0) ){
        set_transient('justgiving_team_'.$_REQUEST['team'], $team, 60 * 60 * 12); // 1/2 day storage
    } 
    $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A); 
    $event = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgevents WHERE `owner`='".trim($user['id'])."';", ARRAY_A); 
   // print_R($teams);
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';

    if (isset($user['pageurl'])) $user['donationurl'] = $user['pageurl'] . '/4w350m3/donate/?amount=5.00&reference=undie';
    //echo (!jg_check_missing_http($wpjg_generalSettings['imageurl'])) ? home_url() . $wpjg_generalSettings['imageurl'] : $wpjg_generalSettings['imageurl'];
    $smarty->assign('team', $team);    
    $smarty->assign('user', $user);    
    $smarty->assign('event', $event); 
    $smarty->assign('pluginurl', JG_PLUGIN_URL);    
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