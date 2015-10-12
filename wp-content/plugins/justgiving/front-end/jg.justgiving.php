<?php
function jg_front_end_justgiving($atts){
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
    extract(shortcode_atts(array('redirectpaid'=> 0, 'display' => true, 'template' => '', 'loggedin' => true, 'pageurl' => ''), $atts));

    if ($loggedin && trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }
        wp_redirect( $redirectLink ); exit;
    }
    $wpjg_generalSettings = get_option('jg_general_settings');    
    $result = array();
    if ($loggedin){
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A);  

        if (isset($result['paidaccess']) && $result['paidaccess'] == 1)
        {
            $redirectLink = trim($redirectpaid);
            if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink))  $redirectLink = 'http://'. $redirectLink;
            } 
            //echo $redirectLink;
            wp_redirect( $redirectLink ); exit;
        }
        if (!isset($result['pageurl']) || $result['pageurl'] == 0)
        {
            $redirectLink = trim($redirectpaid);
            if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
            } 
            //echo $redirectLink;
            wp_redirect( $redirectLink ); exit;
        }        
    }
    else
    {
        $result['pageurl'] = $pageurl;
    }

    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']); 
    //http://v3-sandbox.justgiving.com/test2964
    //echo $pageShortName ;
    //exit;
    
        
    //print_r($_REQUEST);
    if (isset($_REQUEST["action"]))
    {
        $action = $_REQUEST["action"];
        switch($action)
        {
            case "process": // case process insert the form data in DB and process to the paypal
                    $pageShortName = trim(parse_url($result['pageurl'], PHP_URL_PATH), "/");
            
                    if ($client->Page->IsShortNameRegistered($pageShortName) != 200){
                        //no page or page is rubbish
                        $redirectLink = trim(home_url());
                        if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                        wp_redirect( $redirectLink ); exit;        
                    } 
                    if ($loggedin){
                        $wpdb->update(
                            $wpdb->prefix . "jgusers",
                            array(
                                'paytoken' => $_POST["invoice"]
                            ),
                            array(                 
                                'userEnc' => trim($_SESSION['userEnc'])
                            ));
                    }
                    $this_script  = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
                    $this_scriptq = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    $extref = 'urteam-'.rand(1234, 9632).'-'.time();
                    if ($loggedin){
                        $wpdb->insert(
                            $wpdb->prefix . "jgjustgiving",
                            array(
                                'owner' => $result['id'],
                                'reference' => $_POST["invoice"],
                                'extref' => $extref,
                                'amount' => $_POST["product_amount"],
                                'submittedtime' => date('Y-m-d H:i:s')
                            ));
                    }
                    if ( get_option('permalink_structure') != '' )
                        $jgurl = $result['pageurl'] ."/4w350m3/donate/?amount=". $_POST["product_amount"] .
                            "&reference=". $_POST["invoice"] ."&exitUrl=".urlencode($this_script.'?action=ipn&shrtref='.$extref.'&donationId=JUSTGIVING-DONATION-ID');
                    else
                        $jgurl = $result['pageurl'] ."/4w350m3/donate/?amount=". $_POST["product_amount"] .
                            "&reference=". $_POST["invoice"] ."&exitUrl=".urlencode($this_scriptq.'&action=ipn&shrtref='.$extref.'&donationId=JUSTGIVING-DONATION-ID');
                    wp_redirect( $jgurl ); exit;                        
                break;  

            case "ipn": 
                    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgjustgiving WHERE `extref`='".trim($_REQUEST['shrtref'])."';", ARRAY_A);     

                    if (isset($result['id'])){
                        $donation = $client->Donation->RetrieveStatus($_REQUEST['donationId']);
                        //$donation = $client->Donation->Retrieve($_REQUEST['donationId'], trim($_SESSION['userEnc']) );

                        if ($donation && $donation->donationId !== Null){
                            // money in here
                            if (trim($donation->status) == 'Accepted' || trim($donation->status) == 'Pending'){
                                if ($donation->ref !== null){
                                    $wpdb->update(
                                        $wpdb->prefix . "jgjustgiving",
                                        array(
                                            'paid' => 1,
                                            'txn_id' => $donation->donationId
                                        ),
                                        array(                 
                                            'id' => $result['id'],
                                            'reference' => $donation->ref
                                        )); 
                                }
                                else{
                                    $wpdb->update(
                                        $wpdb->prefix . "jgjustgiving",
                                        array(
                                            'paid' => 1,
                                            'txn_id' => $donation->donationId
                                        ),
                                        array(                 
                                            'id' => $result['id']
                                        ));                            
                                }
                                $wpdb->update(
                                    $wpdb->prefix . "jgusers",
                                    array(
                                        'paidaccess' => 1
                                    ),
                                    array(                 
                                        'userEnc' => trim($_SESSION['userEnc'])
                                    ));                                
                                $redirectLink = trim($redirectPaid);
                                if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                                else{
                                    if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                                }       
                                wp_redirect( $redirectLink ); exit;
                            }
                        }
                        else{
                            //redirect to failure page ?
                        }
                    } 
                    elseif(!$loggedin)
                    {
                        $donation = $client->Donation->RetrieveStatus($_REQUEST['donationId']);
                        if ($donation && $donation->donationId !== Null){                    
                            $redirectLink = trim($redirectPaid);
                            if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                            else{
                                if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                            }       
                            wp_redirect( $redirectLink ); exit;  
                        }
                    }
                break;                
        }
    }
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
   
    $smarty->assign('formurl', jg_curpageurl());
    $smarty->assign('Errors', $errors);
    
    $smarty->assign('Settings', $wpjg_generalSettings);
    $invoice = array(
        'invoiceid' => date("His").rand(1234, 9632).'-'.time()
    );
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('User', $_SESSION);
    $smarty->assign('Invoice', $invoice);
    
    if ($template != '') $smarty->display($template);    
    else $smarty->display('justgiving.html');     

    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}