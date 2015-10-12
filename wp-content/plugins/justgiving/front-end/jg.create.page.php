<?php

function jg_autocomplete_suggestions(){
    // Query for suggestions
    $suggestions = array();
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']); 
    if (isset($_REQUEST['q'])) $pages = $client->Page->SuggestPageShortNames($_REQUEST['q']);
    else $pages = $client->Page->SuggestPageShortNames($_REQUEST['term']);

    foreach ($pages->Names as $post):
        $suggestion = array();
        $suggestion['label'] = esc_html($post);
 
        $suggestions[]= $suggestion;
    endforeach;
    
    $response ='';
    // JSON encode and echo
    if (isset($_REQUEST['q']))
    {
        foreach ($suggestions as $line) $response .= $line['label'] . "\n";
    }    
    else $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
    echo $response;
    exit;
}

function jg_front_end_create_page($atts){
    wp_enqueue_script('jg_pagesearch', JG_PLUGIN_URL.'/js/jgacsearch.js', array('jquery','jquery-ui-autocomplete'), '1', true);
    wp_localize_script('jg_pagesearch', 'JGSearch', array('url' => admin_url('admin-ajax.php')));
    
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
           
    extract(shortcode_atts(array('paid'=> 0, 'forgot'=> 0, 'logout'=> 0, 'display' => true, 'redirect' => '', 'teampage' => '', 'submit' => 'page', 'create' => '', 'thanks' => '','advocate' => '', 'template' => ''), $atts));
    $user = '';
    $pass = '';
    $errors = array();
    
    //print_r($_SESSION);


    if ( trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim($forgot);
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink))
                $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }
    $wpjg_generalSettings = get_option('jg_general_settings');    
    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A);  
    
    if ( trim($result['pageurl']) != '' && intval(trim($result['pageurl'])) != 0 ){
        if ((!isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1) || ($result['paidaccess'] == 0 && $wpjg_generalSettings['paidaccess'] == 1))
        {
            $redirectLink = trim($paid);
            if (intval($redirectLink) != 0)
                $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink))
                    $redirectLink = 'http://'. $redirectLink;
            }
        }
        elseif (trim($teampage) !== ''){
            $redirectLink = trim($teampage);
            if (intval($redirectLink) != 0)
                $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink))
                    $redirectLink = 'http://'. $redirectLink;
            }
            $redirectLink = (parse_url($redirectLink, PHP_URL_QUERY)) ? $redirectLink . '&team='.$_POST['jointeam'] : rtrim($redirectLink, '?') . '?team='.$_POST['jointeam'];
        }                
        elseif (trim($redirect) !== ''){
            $redirectLink = trim($redirect);
            if (intval($redirectLink) != 0)
                $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink))
                    $redirectLink = 'http://'. $redirectLink;
            }
        }
        elseif (trim($advocate) !== '' && $_POST['advocate'] == 1){
            $redirectLink = trim($advocate);
            if (intval($redirectLink) != 0){
                $redirectLink = get_permalink($redirectLink);
            }
            else{
                if (!jg_check_missing_http($redirectLink))
                    $redirectLink = 'http://'. $redirectLink;
            }                        
        }
        else{
            $redirectLink = trim($thanks);
            if (intval($redirectLink) != 0){
                $redirectLink = get_permalink($redirectLink);
            }
            else{
                if (!jg_check_missing_http($redirectLink))
                    $redirectLink = 'http://'. $redirectLink;
            }
        }     
        wp_redirect( $redirectLink ); exit;
    }    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);  
    $result = $wpdb->get_results (
        "SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='". trim($_SESSION['userEnc'])  ."'"
    );
    //if (intval($wpjg_generalSettings['Event']) == $wpjg_generalSettings['Event']) echo 'a';
    $suggestions = array();
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] &&
            !empty( $_POST['action'] ) &&
            $_POST['action'] == 'createpage' &&
            wp_verify_nonce($_POST['createpage_nonce_field'],'verify_true_create') &&
            ($_POST['formName'] == 'createpage') ) {

        include_once(JG_PLUGIN_DIR.'/lib/functions.php');
        $results = array(
            'jointeam' => '',
            'pagetitle' => '',
            'tsandcs' => '',
            'pageshortname' => '',
            'packbypost' => '',
            'heardabout' => '',
            'work' => '',
            'advocate' =>  '',
            'dob' => ''
        );	
        $rules = array(
            'pagetitle' => 'notEmpty',
            'tsandcs' => 'notEmpty',
            'pageshortname' => 'url'
        );
        $messages = array(
            'jointeam' => 'Please choose your team',
            'pagetitle' => 'Please enter your page title',
            'tsandcs' => 'You must accept the terms and conditions',
            'pageshortname' => 'What is the address of your JustGiving fundraising page'
        );    
        foreach ($results as $key => $value){
                $results[$key] = $_POST[$key];
        }
        
        $errors = validateJGInputs($results, $rules, $messages);
        if (count($errors) != 0) $foundError = true;        

        if (!$founderror)
        {
            $pageExists = $client->Page->IsShortNameRegistered($_POST['pageshortname']);  
            //file_put_contents('/var/www/html/test6/PHP_errors.log', (int)$pageExists , FILE_APPEND);
            /*create page*/
            if (!$pageExists){
                //file_put_contents('/var/www/html/test6/PHP_errors.log', 'in - no page' , FILE_APPEND);
                $targetAmount = $wpjg_generalSettings['targetAmount'];
                if (trim($_POST['targetch']) == 'var' && isset($_POST['target']) && is_numeric($_POST['target'])) $targetAmount = $_POST['target'];
                elseif (is_numeric($_POST['targetch']))  $targetAmount = $_POST['targetch'];
                /*
                $ordate = $_POST['dob'];
                $pos = strpos($_POST['dob'], '-');
                if (MyCheckDate($_POST['dob'])){
                    //american or off a date field
                    list( $year , $month , $day ) = explode('-',$_POST['dob']);
                    $_POST['dob'] = date('d-m-Y', mktime(0, 0, 0, $month, $day, $year));
                }
                
                $cc5 = array(
                    $_POST['dob'],$_POST['heardabout'],$_POST['work'],($_POST['advocate'] == 1)? 'y':'n','y'
                );
                
                $dto = array(
                    'currency' => ($_SESSION['country'] == 'Ireland') ? 'EUR' : 'GBP',
                    'pageShortName' => $_POST['pageshortname'],
                    'charityId' =>  $wpjg_generalSettings['Charity'],
                    'eventId' => $wpjg_generalSettings['Event'],
                    'justGivingOptIn' => ((bool) $_POST['jgoptin']),
                    'charityOptIn' => ((bool) $_POST['charityoptin']),
                    'pageTitle' => stripslashes($_POST['pagetitle']),
                    'targetAmount' => $targetAmount  ,
                    'charityFunded' => false,
                    "customCodes" => array( 
                        "customCode5" => implode('|', $cc5),
                        "customCode6" => (strpos($wpjg_generalSettings['cc6'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc6'] )]) : stripslashes($wpjg_generalSettings['cc6'])
                    )
                ); 
                */
                $dto = array(
                    'currency' => ($_SESSION['country'] == 'Ireland') ? 'EUR' : 'GBP',
                    'pageShortName' => $_POST['pageshortname'],
                    'charityId' =>  $wpjg_generalSettings['Charity'],
                    'justGivingOptIn' => ((bool) $_POST['jgoptin']),
                    'charityOptIn' => ((bool) $_POST['charityoptin']),
                    'pageTitle' => stripslashes($_POST['pagetitle']),
                    'targetAmount' => $targetAmount  ,
                    'charityFunded' => false,
                    "customCodes" => array( 
                        "customCode1" => (strpos($wpjg_generalSettings['cc1'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc1'] )]) : stripslashes($wpjg_generalSettings['cc1']),
                        "customCode2" => (strpos($wpjg_generalSettings['cc2'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc2'] )]) : stripslashes($wpjg_generalSettings['cc2']),
                        "customCode3" => (strpos($wpjg_generalSettings['cc3'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc3'] )]) : stripslashes($wpjg_generalSettings['cc3']),
                        "customCode4" => (strpos($wpjg_generalSettings['cc4'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc4'] )]) : stripslashes($wpjg_generalSettings['cc4']),
                        "customCode5" => (strpos($wpjg_generalSettings['cc5'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc5'] )]) : stripslashes($wpjg_generalSettings['cc5']),
                        "customCode6" => (strpos($wpjg_generalSettings['cc6'],'(data)')  !== false) ? stripslashes($_POST[str_replace('(data)','',$wpjg_generalSettings['cc6'] )]) : stripslashes($wpjg_generalSettings['cc6'])
                    )
                );
                if (trim($wpjg_generalSettings['Event']) != '' && intval($wpjg_generalSettings['Event']) == $wpjg_generalSettings['Event'])
                {
                    $dto['eventId'] = $wpjg_generalSettings['Event'];
                }
                elseif(trim($wpjg_generalSettings['Event']) != '')
                {
                    //Birthday Wedding OtherCelebration InMemory                    
                    $dto['activityType'] = $wpjg_generalSettings['Event'];
                    date_default_timezone_set( "UTC" );                
                    $_POST['eventDate'] = strtotime($_POST['eventDate-date'].' '.$_POST['eventDate-time']);
                    $dto['eventDate'] = "\/Date(".$_POST['eventDate']."\/";
                    $dto['eventName'] = $_POST['eventName'];
                }
                else
                {
                    $dto['activityType'] = $_POST['eventType']; 
                    date_default_timezone_set( "UTC" );                
                    $_POST['eventDate'] = strtotime($_POST['eventDate-date'].' '.$_POST['eventDate-time']);
                    $dto['eventDate'] = "\/Date(".$_POST['eventDate']."\/";
                    $dto['eventName'] = $_POST['eventName'];                    
                }
                
                
                if (strlen($wpjg_generalSettings['imageurl']) > 0){
                    $url = (!jg_check_missing_http($wpjg_generalSettings['imageurl'])) ? home_url() . $wpjg_generalSettings['imageurl'] : $wpjg_generalSettings['imageurl'];
                    $dto['images'] = array(
                        array(
                            "caption" => get_bloginfo( 'name' ),                  
                            "isDefault" => true,
                            "url" => $url
                        )
                    ); 
                }
                if (strlen($wpjg_generalSettings['pageStory']) > 0) $dto['pageStory'] = $wpjg_generalSettings['pageStory'];
                if (strlen($wpjg_generalSettings['pageSummaryWhat']) > 0) $dto['pageSummaryWhat'] = $wpjg_generalSettings['pageSummaryWhat'];
                if (strlen($wpjg_generalSettings['pageSummaryWhy']) > 0) $dto['pageSummaryWhy'] = $wpjg_generalSettings['pageSummaryWhy'];
                
                
                //$cntent  = print_R($_SESSION, true);
                //file_put_contents( '/var/www/html/test6/PHP_errors.log' , $cntent, FILE_APPEND );    
                
                //$cntent  = print_R($dto, true);
                //file_put_contents('/var/www/html/test6/PHP_errors.log', $cntent , FILE_APPEND);
                
                $page = $client->Page->Create(trim($_SESSION['userEnc']), $dto);
                /*update user with url*/
                if (!$page) $errors['shortname']['message'] = 'Could not create page at JustGiving';
                //$cntent  = print_R($page, true);
                //file_put_contents( '/var/www/html/test6/PHP_errors.log' , $cntent, FILE_APPEND );    
                //$cntent  = print_R($_SESSION, true);
                //file_put_contents( '/var/www/html/test6/PHP_errors.log' , $cntent, FILE_APPEND );    
                $uid = 0;
                if ($page){
                    $result = $wpdb->get_row (
                        "SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='". trim($_SESSION['userEnc'])  ."'", ARRAY_A
                    ); 
                    if (count($result) > 0) {
                        //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , 'update', FILE_APPEND );    
                        $wpdb->update(
                            $wpdb->prefix . "jgusers",
                            array(
                                'pageurl' => $page->next->uri,
                                'pageid' => $page->pageId,
                                'optin' => $_POST['charityoptin'],
                                'tsandcs' => $_POST['tandcs'],
                                'packbypost' =>  $_POST['packbypost'],
                                'heardabout' =>  $_POST['heardabout'],
                                'eventstart' =>  $_POST['eventstart'],
                                'eventend' =>  $_POST['eventend'],
                                'work' =>  $_POST['work'],
                                'worktown' =>  $_POST['worktown'],
                                'workcountry' =>  $_POST['workcountry'],
                                'workpostcode' =>  $_POST['workpostcode'],
                                'workwhere' =>  $_POST['workwhere'],
                                'dofereln' =>  $_POST['dofereln'],
                                'dofegold' =>  $_POST['dofegold'],
                                'dofeevent' =>  $_POST['dofeevent'],
                                'tshirt' =>  $_POST['tshirt'],
                                'discountcode' =>  $_POST['discountcode'],
                                'region' =>  $_POST['region'],
                                'signoff' =>  $_POST['signoff'],
                                'dob' =>   $_POST['dob']
                            ),
                            array(                 
                                'userEnc' => trim($_SESSION['userEnc'])
                            )
                        );
                        //$cntent  = print_R($wpdb->queries , true);  
                        //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , $cntent, FILE_APPEND );
                        $uid = $result['id'];
                    }
                    else{
                        //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , 'insert', FILE_APPEND );    
                        $wpdb->insert( 
                            $wpdb->prefix . "jgusers", 
                            array(  
                                'email' => trim($_SESSION['email']),
                                'userEnc' => trim($_SESSION['userEnc']),
                                'pageurl' => $page->next->uri,
                                'pageid' => $page->pageId,
                                'signupdate' => time(),
                                'optin' => $_POST['charityoptin'],
                                'tsandcs' => $_POST['tandcs'],
                                'packbypost' =>  $_POST['packbypost'],
                                'eventstart' =>  $_POST['eventstart'],
                                'eventend' =>  $_POST['eventend'],
                                'work' =>  $_POST['work'],
                                'worktown' =>  $_POST['worktown'],
                                'workcountry' =>  $_POST['workcountry'],
                                'workpostcode' =>  $_POST['workpostcode'],
                                'workwhere' =>  $_POST['workwhere'],
                                'dofereln' =>  $_POST['dofereln'],
                                'dofegold' =>  $_POST['dofegold'],
                                'dofeevent' =>  $_POST['dofeevent'],
                                'tshirt' =>  $_POST['tshirt'],
                                'discountcode' =>  $_POST['discountcode'],
                                'region' =>  $_POST['region'],
                                'signoff' =>  $_POST['signoff'],
                                'heardabout' =>  $_POST['heardabout'],
                                'advocate' =>   $_POST['advocate'],
                                'dob' =>   $_POST['dob']
                            )
                        ); 
                        $uid = $wpdb->insert_id;    
                    }                     
                    
                    
                    //$sql = "INSERT INTO {$wpdb->prefix}jgpages (pageid,userid,next_rel,next_uri,next_type,short,signOnUrl) VALUES (%s,%s,%s,%s,%s,%s,%s) ON DUPLICATE KEY UPDATE userid = %s, next_rel = %s, next_uri = %s, next_type = %s, short = %s, signOnUrl = %s";
                    //var_dump($sql); // debug
                    //$sql = $wpdb->prepare($sql,$page->pageId,$result['id'],$page->next->rel,$page->next->uri,$page->next->type,$short,$page->signOnUrl,$result['id'],$page->next->rel,$page->next->uri,$page->next->type,$short,$page->signOnUrl);
                    //var_dump($sql); // debug
                    //$wpdb->query($sql);

                    //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' ,  "SELECT * FROM wp_jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."'", FILE_APPEND );    
                    //$cntent  = print_R($result, true);
                    //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , $cntent, FILE_APPEND );
                    
                    
                    // echo 'b';   
                    // add to team if chosen

                    // $cntent  = print_R($_POST, true);
                    // file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , $cntent, FILE_APPEND );    
                    $vars = array();
                    $rsgeneralSettings = get_option('jg_general_settings');
                    if (isset($_POST['jointeam']) && trim($_POST['jointeam']) !== ''){
                        //echo 'b';   
                        $user = array();
                        $user['pageShortName'] = $dto['pageShortName'];
                        $client->Team->Join($_POST['jointeam'], trim($_SESSION['userEnc']), $user);
                        $teamqs = $wpdb->get_row (
                                "SELECT * FROM {$wpdb->prefix}jgteams WHERE `teamshortname`='". trim($_POST['jointeam'])  ."' "
                        ); 
                        $rsgeneralSettings = get_option('jg_general_settings');  
                        $vars = array(
                            'firstname' => $result['firstname'],
                            'url' => $page->next->uri,
                            'editurl' => $page->signOnUrl,
                            'donateurl' => $page->next->uri ."/4w350m3/donate/?amount=5.00&reference=undie",
                            'teamid' => $teamqs->id,
                            'teamname' => $teamqs->teamname,
                            'teamshortname' => $teamqs->teamshortname,
                            'teamstory' => $teamqs->teamstory,
                            'teamfbpage' => $teamqs->teamfbpage,
                            'teamtwpage' => $teamqs->teamtwpage,
                            'website' => get_home_url('/'),
                            'fbappid' => $rsgeneralSettings['fbappid']
                        );
                        $members = array();
                        $members = json_decode($teamqs->teammembers, true);
                        $members[] = array(
                            'id' => $uid,
                            'numberOfDonations' => 0,
                            'pageShortName' => $dto['pageShortName'],
                            'pageTitle' => $dto['pageTitle'],
                            'ref' => '',
                            'totalAmountRaised' => 0                   
                        );
                        $wpdb->update(
                            $wpdb->prefix . "jgteams",
                            array(
                                'teammembers' => json_encode($members)
                            ),
                            array(                 
                                'teamshortname' => trim($_POST['jointeam'])
                            )
                        );                     

                    } 
                    else
                    {
                        $vars = array(
                            'firstname' => $result['firstname'],
                            'url' => $page->next->uri,
                            'editurl' => $page->signOnUrl,
                            'donateurl' => $page->next->uri ."/4w350m3/donate/?amount=5.00&reference=undie",
                            'website' => get_home_url('/'),
                            'fbappid' => $rsgeneralSettings['fbappid']
                        );                 
                    }
                    $vars['website'] = get_home_url();
                    $vars['website_enc'] = urlencode($vars['website']);
                    $useracc = $client->Account->GetUser(trim($_SESSION['userEnc']));
                    if (trim($vars['firstname']) == ''){
                        $vars['firstname'] =  $useracc->firstName;
                        $result['firstname'] =  $useracc->firstName;
                        $result['lastname'] =  $useracc->lastName;
                    }
                    $email = $_SESSION['email'];
                    if (trim($email) == ''){
                        $email = $useracc->email;
                    }
                    
                    if (trim($advocate) !== '' && $_POST['advocate'] == 1){
                        $ba = sendadvocate(trim($email), $result['firstname']. ' '. $result['lastname'], $vars, 1);  
                    } else $ba = sendthanks(trim($email), $result['firstname']. ' '. $result['lastname'], $vars, 1);  

                    // -> send straight to thanks
                    $redirectLink = '';
                    if ((!isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1) || ($result['paidaccess'] == 0 && $wpjg_generalSettings['paidaccess'] == 1))
                    {
                        $redirectLink = trim($paid);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink))
                                $redirectLink = 'http://'. $redirectLink;
                        }
                    }
                    elseif (trim($teampage) !== ''){
                        $redirectLink = trim($teampage);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink))
                                $redirectLink = 'http://'. $redirectLink;
                        }
                        $redirectLink = (parse_url($redirectLink, PHP_URL_QUERY)) ? $redirectLink . '&team='.$_POST['jointeam'] : rtrim($redirectLink, '?') . '?team='.$_POST['jointeam'];
                    }                
                    elseif (trim($redirect) !== ''){
                        $redirectLink = trim($redirect);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink))
                                $redirectLink = 'http://'. $redirectLink;
                        }
                    }
                    elseif (trim($advocate) !== '' && $_POST['advocate'] == 1){
                        $redirectLink = trim($advocate);
                        if (intval($redirectLink) != 0){
                            $redirectLink = get_permalink($redirectLink);
                        }
                        else{
                            if (!jg_check_missing_http($redirectLink))
                                $redirectLink = 'http://'. $redirectLink;
                        }                        
                    }
                    else{
                        $redirectLink = trim($thanks);
                        if (intval($redirectLink) != 0){
                            $redirectLink = get_permalink($redirectLink);
                        }
                        else{
                            if (!jg_check_missing_http($redirectLink))
                                $redirectLink = 'http://'. $redirectLink;
                        }
                    }
                    
                    $redirectLink = (parse_url($redirectLink, PHP_URL_QUERY)) ? $redirectLink . '&nexturl='.urlencode($page->next->uri) : rtrim($redirectLink, '?') . '?nexturl='.urlencode($page->next->uri);
                    //echo $redirectlink; exit;
                    //$cntent  = print_R(array('redir'=>$redirect,'thanks'=>$thanks,'page'=>$page,'redired'=>$redirectlink), true);
                    //file_put_contents( '/var/www/html/test6/PHP_errors.log' ,$redirectLink, FILE_APPEND );    
                    //file_put_contents('curldata.txt', $redirectLink , FILE_APPEND);  
                    wp_redirect($redirectLink);
                    exit;
                }
            }
            else{ 
          
                if (isset($_POST['pageshortname'])){
                    $pages = $client->Page->SuggestPageShortNames($_POST['pageshortname']);     

                    foreach ($pages->Names as $post)
                    {
                        $suggestion = array();
                        $suggestion['label'] = esc_html($post);
                        $suggestions[]= $suggestion;
                    }            
                }
            }
            $errors['shortname']['message'] = "Someone&rsquo;s already set sail with that name. Try another.";    
        }
    }

    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
    
    //$teams = $client->Team->Search();    
    //print_R($teams);
/*
    $teamRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgteams ;");    
    $items_list = array(
        '' => array(
            'label' => ""
        )
    );
    foreach ($teamRows as $team){
        $items_list[$team->teamshortname] = array('label' =>  $team->teamname);
    }    
    $smarty->assign('teams', $items_list);
*/
    $formurl = jg_curpageurl();
    /*
    $redirectLink = 'http://test6.thegoodagencydigital.co.uk/thank-you?team=wibble';
    $nexturl = 'http://v3-sandbox.justgiving.com/h2onttt20152016';    
    $redirectlink = (parse_url($redirectLink, PHP_URL_QUERY)) ? $redirectLink . '&nexturl='.$nexturl : rtrim($redirectLink, '?') . '?nexturl='.$nexturl;    
    $smarty->assign('redirecturl',$redirectlink);
    */
    $smarty->assign('pageshortname',stripslashes($_POST['pageshortname']));
    $smarty->assign('errorshortname',$errors['shortname']['message']); 
    $smarty->assign('pagetitle', stripslashes($_POST['pagetitle']));
    $smarty->assign('errorpagetitle', $errors['pagetitle']['message']); 
    $smarty->assign('target', (isset($_POST['target']) && is_numeric($_POST['target'])) ? $_POST['target'] : $wpjg_generalSettings['targetAmount']);
    $smarty->assign('errortargetAmount', $errors['target']['message']); 
    $smarty->assign('nonce', wp_nonce_field('verify_true_create','createpage_nonce_field', true, false)); 
    $smarty->assign('jgoptinyes', ($_POST['jgoptin'] =='1' || !isset($_REQUEST['jgoptin'])) ? 'checked="checked"':'');
    $smarty->assign('jgoptinno', ($_POST['jgoptin'] =='0') ? 'checked="checked"':'');
    $smarty->assign('choptinyes', ($_POST['charityoptin']=='1' || ($_SESSION['optin'] == 1 && $_POST['charityoptin'] != 0)  || (!isset($_REQUEST['charityoptin']) && (!isset($_SESSION['optin']) || $_SESSION['optin'] != 0))) ? 'checked="checked"':'');
    $smarty->assign('choptinno', ($_POST['charityoptin'] =='0') ? 'checked="checked"':'');
    $smarty->assign('formurl', $formurl);
    $smarty->assign('templateurl', get_template_directory_uri()); 
    $smarty->assign('suggestions', $suggestions);
    $smarty->assign('logout', $logout);
    $data = array();
    for ($i = date('Y') - 18; $i >= date('Y')-98; $i--) {
        $data[] = $i;
    }
    $smarty->assign('years', $data);
    
    $smarty->assign('maxdate', date('Y-m-d'));
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('Errors', $errors);
    $smarty->assign('Session', $_SESSION);
    
    if ($template != '') $smarty->display($template);    
    else $smarty->display('create-page.html'); 
    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}