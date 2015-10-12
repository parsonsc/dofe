<?php


function jg_front_end_eventadd($atts){
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
    
    wp_enqueue_script('modernizr', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', 'jquery', false);
    wp_enqueue_script('yepnope', 'https://cdnjs.cloudflare.com/ajax/libs/yepnope/1.5.4/yepnope.min.js', 'jquery', true);
    wp_register_script('justgiving-gaddress', ('https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places'), false, null, true);
	wp_enqueue_script('justgiving-gaddress');
    wp_enqueue_script('justgiving-gplacej', JG_PLUGIN_URL.'/js/geocode.js', array('jquery', 'justgiving-gaddress'), '1', true);
    wp_enqueue_script('justgiving-gplace', JG_PLUGIN_URL.'/js/googleaddress.js', array('jquery', 'justgiving-gplacej'), '1', true);
           
    extract(shortcode_atts(array('paid'=> 0, 'display' => true, 'redirect' => '','thanks'=> 0, 'submit' => 'page', 'template' => ''), $atts));
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

    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A);     
    if ((!isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1) || ($result['paidaccess'] == 0 && $wpjg_generalSettings['paidaccess'] == 1))
    {
        $redirectLink = trim($paid);
        if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }    
        
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true); 
    
        
    if (trim($template) == '') $template = 'create-event.html'; 
    //error_log(print_R($_POST, true));
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] &&
            !empty( $_POST['action'] ) &&
            $_POST['action'] == 'eventadd' &&
            wp_verify_nonce($_POST['eventadd_nonce_field'],'verify_true_eventadd') &&
            ($_POST['formName'] == 'eventadd') ) 
    {
        include_once(JG_PLUGIN_DIR.'/lib/functions.php');
        $results = array(
            'eventname' => '',
            'description' => '',
            'eventend' => '',
            'eventstart' => '',
            'eventtype' => '',
            'eventlocn' => ''
        );	
        $rules = array(
            'eventname' => 'notEmpty',
            'description' => 'notEmpty',
            'eventend' => 'notEmpty',
            'eventstart' => 'notEmpty',
            'eventtype' => 'notEmpty'
        );
        date_default_timezone_set( "UTC" );        
        $_POST['eventend'] = strtotime($_POST['eventend-date'].' '.$_POST['eventend-time']);
        $_POST['eventstart'] = strtotime($_POST['eventstart-date'].' '.$_POST['eventstart-time']);
        //error_log(print_R($_POST, true));        
        $messages = array(
            'eventname' => 'Please enter your event name',
            'description' => 'Please enter your event description',
            'eventend' => 'Please choose your event end date',
            'eventstart' => 'Please choose your event start date',
            'eventtype' => 'Please choose your event type'
        );    
        foreach ($results as $key => $value){
            $results[$key] = $_POST[$key];
        }
        //error_log(print_R($errors, true));
        $foundError = false;  
        
        $errors = validateJGInputs($results, $rules, $messages);
        if (count($errors) != 0) $foundError = true;        

        if (!$founderror)
        {  
            //error_log('here'); 
            $newEvent = array(
                "name" => $results['eventname'],
                "description" => $results['eventdescr'],
                "completionDate" => gmdate("Y-m-d\TH:i:s",$results['eventend']+date("Z",$results['eventend'])) ,
                "expiryDate" => gmdate("Y-m-d\TH:i:s",strtotime("+1 day", $results['eventend'])),
                "startDate" => gmdate("Y-m-d\TH:i:s",$results['eventstart']+date("Z",$results['eventstart'])),
                "eventType" => $results['eventtype'],
                "location" => $results['eventlocn']
            );
            $user = $wpdb->get_row (
                "SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='". trim($_SESSION['userEnc'])  ."'  ORDER BY id DESC LIMIT 1"
            ); 
            //error_log(print_R($user, true)); 
            $uid = 0;
            if (count($user) > 0) {            
                $event = $client->Event->Create($newEvent);
                //error_log(print_R($event, true)); 
                if (!$event) $errors['eventname']['message'] = 'Could not create event at JustGiving';
                else{   
                    $wpdb->insert(
                        $wpdb->prefix . "jgevents",
                        array(
                            'owner' => $user->id,
                            'jgeventid' => $event->id,
                            'eventname' => $results['eventname'],
                            'eventdescr' => $results['eventdescr'],
                            'eventcomplete' => date('d-m-Y H:i', $results['eventend']),
                            'eventexpiry' => date('d-m-Y H:i', strtotime("+1 day", $results['eventend'])),
                            'eventstart' => date('d-m-Y H:i', $results['eventstart']),
                            'eventtype' => $results['eventtype'],
                            'location' => $results['eventlocn'],
                            'street_number' => $_POST['street_number'],
                            'street_name' => $_POST['street_name'],
                            'city' => $_POST['city'],
                            'state' => $_POST['administrative_area_level_1'],
                            'postcode' => $_POST['postcode'],
                            'country' => $_POST['country'],    
                            'lat' => $_POST['lat'],    
                            'lng' => $_POST['lng'],    
                            'submittedtime' => date('Y-m-d G:i:s'),
                            'lastmodified' => date('Y-m-d G:i:s')
                        )
                    );
                    //$cntent  = print_R($wpdb->queries , true);  
                    //file_put_contents( '/var/www/html/doe/doe_error.log' , $cntent, FILE_APPEND );
                    $uid = $result['id'];
                    if (trim($redirect) !== ''){
                        $redirectLink = trim($redirect);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                    }
                    else{
                        $redirectLink = trim($thanks);
                        if (intval($redirectLink) != 0){
                            $redirectLink = get_permalink($redirectLink);
                        }
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                    }                    
                }
            }
        }
    }
     
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
    
    $formurl = jg_curpageurl();
    
    
    
    $eventtypes = array();
    $row = $wpdb->get_row("SHOW COLUMNS FROM {$wpdb->prefix}jgevents WHERE field='eventtype'");
    //print_R($row);    
    
    preg_match_all("/'(.*?)'/", $row->Type, $categories);
    //print_R($categories);
    foreach ($categories[1] as $k){
            preg_match_all('/((?:^|[A-Z])[a-z]+)/',$k,$matches);
            $eventtypes[$k] = implode(' ', $matches[1]);
    }

    $smarty->assign('nonce', wp_nonce_field('verify_true_eventadd','eventadd_nonce_field', true, false)); 

    $smarty->assign('eventtypes', $eventtypes);
    $smarty->assign('formurl', $formurl);
    $smarty->assign('templateurl', get_template_directory_uri()); 
    $smarty->assign('suggestions', $suggestions);
    $data = array();
    for ($i = date('Y') - 18; $i >= date('Y')-98; $i--) {
        $data[] = $i;
    }
    $smarty->assign('years', $data);
    
    $smarty->assign('maxdate', date('Y'));
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('Errors', $errors);
    $smarty->assign('Session', $_SESSION);
    //print_R($_SESSION);
    //print_R($eventtypes);
    $smarty->display($template);
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}