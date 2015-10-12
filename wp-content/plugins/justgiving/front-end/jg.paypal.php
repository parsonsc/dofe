<?php

function jg_front_end_paypal($atts){
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
           
    extract(shortcode_atts(array('redirectPaid'=> 0, 'display' => true, 'template' => ''), $atts));
/*
    if ( trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }
        wp_redirect( $redirectLink ); exit;
    }
 */
    $wpjg_generalSettings = get_option('jg_general_settings');    
    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}jgusers WHERE `userEnc`='".trim($_SESSION['userEnc'])."';", ARRAY_A);     
    if (isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1 && $result['paidaccess'] == 1)
    {
        $redirectLink = trim($redirectPaid);
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        }       
        wp_redirect( $redirectLink ); exit;
    }
    if (isset($_REQUEST["action"]))
    {
        require_once(JG_PLUGIN_DIR."/paypal/paypal_class.php");
        $p 				= new paypal_class(); // paypal class
        $p->admin_mail 	= $wpjg_generalSettings['paypal_email']; // set notification email
        $action 		= $_REQUEST["action"];

        switch($action)
        {
            case "process": // case process insert the form data in DB and process to the paypal
                    $wpdb->update(
                        $wpdb->prefix . "jgusers",
                        array(
                            'paytoken' => $_POST["invoice"]
                        ),
                        array(                 
                            'userEnc' => trim($_SESSION['userEnc'])
                        ));
                    $this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
                    $p->add_field('business', $wpjg_generalSettings['paypal_femail']); // Call the facilitator eaccount
                    $p->add_field('cmd', $_POST["cmd"]); // cmd should be _cart for cart checkout
                    $p->add_field('upload', '1');
                    $p->add_field('return', $this_script.'?action=success'); // return URL after the transaction got over
                    $p->add_field('cancel_return', $this_script.'?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
                    $p->add_field('notify_url', $this_script.'?action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
                    $p->add_field('currency_code', $_POST["currency_code"]);
                    $p->add_field('invoice', $_POST["invoice"]);
                    $p->add_field('item_name_1', $_POST["product_name"]);
                    $p->add_field('item_number_1', $_POST["product_id"]);
                    $p->add_field('quantity_1', $_POST["product_quantity"]);
                    $p->add_field('amount_1', $wpjg_generalSettings['payamount']);
                    $p->add_field('first_name', $_POST["payer_fname"]);
                    $p->add_field('last_name', $_POST["payer_lname"]);
                    $p->add_field('address1', $_POST["payer_address"]);
                    $p->add_field('city', $_POST["payer_city"]);
                    $p->add_field('state', $_POST["payer_state"]);
                    $p->add_field('country', $_POST["payer_country"]);
                    $p->add_field('zip', $_POST["payer_zip"]);
                    $p->add_field('email', $_POST["payer_email"]);
                    $p->submit_paypal_post(); // POST it to paypal
                break;  

            case "success": 
                    $redirectLink = trim($redirectPaid);
                    if (intval($redirectLink) != 0)
                        $redirectLink = get_permalink($redirectLink);
                    else{
                        if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                    }       
                    wp_redirect( $redirectLink ); exit;            
                break;

            case "ipn": 
                    if ($p->validate_ipn()){
                        $wpdb->update(
                            $wpdb->prefix . "jgusers",
                            array(
                                'paidaccess' => 1,
                                'txn_id' => $_POST["txn_id"]
                            ),
                            array(                 
                                'paytoken' => trim($_POST["invoice"])
                            )); 
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
        'productid' => rand(1111, 99999),
        'invoiceid' => date("His").rand(1234, 9632)
    );
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('User', $_SESSION);
    $smarty->assign('Invoice', $invoice);
    
    if ($template != '') $smarty->display($template);    
    else $smarty->display('paypal.html');
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}