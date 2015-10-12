<?php
function jg_front_end_eventlist($atts){
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
           
    extract(shortcode_atts(array('detail'=> 0, 'display' => true, 'submit' => 'page', 'template' => '', 'pagesize' => 25), $atts));
    if ( trim($_SESSION['userEnc']) == '' ){
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
    if (trim($template) == '') $template = 'list-event.html';
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jgevents;");
    $events = array();
    $mainEvent = $client->Event->Retrieve($wpjg_generalSettings['Event']);
    
    $events[] = array(
        'eventname' => $mainEvent->name,
        'eventdescr' => $mainEvent->description,
        'jgeventid' => $mainEvent->id,
        'eventcomplete' => date('d-m-Y H:i', strtotime($mainEvent->completionDate)),
        'eventexpiry' => date('d-m-Y H:i', strtotime($mainEvent->expiryDate)),
        'eventstart' => date('d-m-Y H:i', strtotime($mainEvent->startDate)),
        'eventtype' => $mainEvent->eventType,
        'location' => $mainEvent->location
    );
    foreach ($results as $result)
    {        
        $events[] = (array)$result;
    }    
    
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
    
    $formurl = jg_curpageurl();

    $smarty->assign('nonce', wp_nonce_field('verify_true_eventadd','eventadd_nonce_field', true, false)); 
    $smarty->assign('detailpage', get_permalink($detail));
    $smarty->assign('events', $events);
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