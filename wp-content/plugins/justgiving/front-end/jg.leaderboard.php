<?php

function jg_front_end_leaderboard($atts){
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
         
    extract(shortcode_atts(array('redirectPaid'=> 0, 'display' => true, 'template' => '', 'orderby'=>'', 'order' => '', 'limit'=>0), $atts));
    //get all pages
    $teams = array();
    $wpjg_generalSettings = get_option('jg_general_settings');    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);    
    

    // get teams for pages    
    $teams = get_transient('justgiving_teams')  ;
    if (DEBUG){
        $teams = NULL;
    }  
    $players = 0;
    if (!$teams){
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jgteams WHERE `teamshortname`<>'';");  
        //print_r($results);
        foreach ($results as $result)
        {        
            $res = $client->Team->Get($result->teamshortname);
            //print_R($res);
            $vars = array();
            if ($res) {
                $vars = array_merge(get_object_vars($res), get_object_vars($result));
                $vars['numMembers'] = count($res->teamMembers);
                $players += count($res->teamMembers);              
            }
            else $vars = array_merge(get_object_vars($result));
            $teams[$res->id] = $vars ; 
        }
    }
    else{
        foreach ($teams as $team) $players += $team['numMembers'];
    }
    
    if (!DEBUG && (!isset($_GET['id']) || intval($_GET['id']) == 0) ){
        set_transient('justgiving_teams', $teams, 60 * 10); // 1/2 day storage
    } 
    include_once(JG_PLUGIN_DIR.'/lib/functions.php');
    $teams = unstrip_array($teams);
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
    //print_R($teams);
    if ($orderby !=''){
        if ($order == 'asc') array_sort_by_column($teams, $orderby, SORT_ASC);
        else array_sort_by_column($teams, $orderby, SORT_DESC);
    }
    if ($limit  != 0 ) $teams = array_slice($teams, 0, $limit);
    $selteam = array();
    if (isset($_REQUEST['selteam']) && $_REQUEST['selteam']){
        foreach($teams as $team){
            if ($team['teamshortname'] == $_REQUEST['selteam']){
                $selteam = $team;
                break;
            }
        }
    }   
    if(isset($selteam['dateCreated'])){
        $smarty->assign('sel', $selteam); 
    }
    $smarty->assign('teams', $teams);    
    $smarty->assign('pluginurl', JG_PLUGIN_URL);    
    $smarty->assign('templateurl', get_template_directory_uri()); 
    $smarty->assign('players', $players);
    $smarty->assign('settings', $wpjg_generalSettings);
    if ($template != '') $smarty->display($template);    
    else $smarty->display('leaderboard.html');       
    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}   