<?php

function jg_front_end_eventdetail($atts){
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
           
    extract(shortcode_atts(array('list'=> 0, 'detail'=> 0, 'display' => true, 'submit' => 'page', 'template' => '', 'pagesize' => 25), $atts));
    if ( trim($_SESSION['userEnc']) == '' || !isset($_GET['evid']) ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }    
    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true); 
    
    
    $url=$_SERVER['REQUEST_URI'];
    
    if (trim($template) == '') $template = 'create-event.html'; 
    
    $events = array();
    
    $eventPages = $client->Event->RetrievePages($_GET['evid'],$_GET['page'],$pagesize);  
    $eventPages = (array)$eventPages->fundraisingPages;
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
    
    $formurl = jg_curpageurl();
    
    $smarty->assign('nonce', wp_nonce_field('verify_true_eventadd','eventadd_nonce_field', true, false)); 

    $smarty->assign('pages', $eventPages);
    $smarty->assign('detailpage', get_permalink($detail)); // pageShortName is needed to call the detail
    $smarty->assign('templateurl', get_template_directory_uri()); 
    
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('Errors', $errors);
    $smarty->assign('Session', $_SESSION);
    
    $smarty->display($template);

    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}