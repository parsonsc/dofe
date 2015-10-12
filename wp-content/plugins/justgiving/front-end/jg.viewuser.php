<?php
function jg_front_end_view_user($atts){
    if(session_id() == '' || !isset($_SESSION)) {
        // session isn't started
        session_start();
    }

    global $current_user;
    global $wp_roles;
    global $wpdb;
    global $error;	
    global $js_shortcode_on_front;
           
    extract(shortcode_atts(array('forgot'=> 0, 'display' => true, 'redirect' => '', 'teampage' => '', 'submit' => 'page', 'create' => '', 'thanks' => '', 'template' => ''), $atts));

    if (!isset($_SESSION['userEnc']) || trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }
        //echo $redirectLink ;
        wp_redirect( $redirectLink ); exit;
    }
    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true);
    
    $user = $client->Account->GetUser(trim($_SESSION['userEnc']));
    $pages = '';
    $teams = '';
    $events = '';
    if ($user){
        //print_R($user);
        $userRows = $wpdb->get_row(" SELECT * FROM {$wpdb->prefix}jgusers WHERE `email`='{$user->email}';", ARRAY_A);
        //print_R($userRows);
        $pages = $client->Page->ListAll(trim($_SESSION['userEnc']));
        
        $teams = $client->Team->Search();
        
        $pagecount = 0;
        foreach ($pages as $page){
            //print_R($page);
            if ($page->charityId == $wpjg_generalSettings['Charity']) 
            {
                if (strlen(trim($wpjg_generalSettings['Event'])) > 0 
                    && $page->eventId == $wpjg_generalSettings['Event'] ) 
                {
                    $pagecount++;
                    if (!$teams)
                    {       
                        $uniqueId = uniqid();            
                        $request = array();
                        $request['teamShortName'] = "team".$uniqueId ;
                        $request['name'] = "team".$uniqueId;
                        $request['story'] = "story".$uniqueId;
                        $request['targetType'] = "Aggregate";
                        $request['teamType'] = "ByInvitationOnly";
                        $request['teamMembers'] = array(array(
                            'pageShortName' => $page->pageShortName
                        ));
                        $response = $client->Team->Create($request, trim($_SESSION['userEnc']));
                        if ($response == 1){
                            $teams = $client->Team->Get($request['teamShortName']);
                        }                        
                    }             
                }
            }            
        }
        $eventRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgevents WHERE `owner`='{$userRows['id']}';", ARRAY_A);
        foreach ($eventRows as $eventRow){
            $events[] = $client->Event->Retrieve($eventRows['jgeventid']);
        }
        require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
        $smarty = new Smarty();
        $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
        $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
        $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
        $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
       
        $formurl = jg_curpageurl();

        $smarty->assign('settings', $wpjg_generalSettings);        
        $smarty->assign('formurl', $formurl);
        $smarty->assign('user', $user);
        $smarty->assign('pages', $pages);
        $smarty->assign('teams', $teams);        
        $smarty->assign('events', $events);        
        
        if ($template != '') $smarty->display($template);    
        else $smarty->display('viewuser.html');
     
    }
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}