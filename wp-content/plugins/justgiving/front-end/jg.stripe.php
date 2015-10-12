<?php

function jg_front_end_stripe($atts){
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
           
    extract(shortcode_atts(array('redirectpaid'=> 0, 'display' => true, 'template' => ''), $atts));
/*
    if ( trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim(home_url());
        if (intval($redirectLink) != 0)
            $redirectLink = get_permalink($redirectLink);
        else{
            if (!jg_check_missing_http($redirectLink))
                $redirectLink = 'http://'. $redirectLink;
        }
        wp_redirect( $redirectLink ); exit;
    }
 */
    if ( trim($_SESSION['userEnc']) == '' ){
        $redirectLink = trim(home_url());
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
    if (isset($result['paidaccess']) && $wpjg_generalSettings['paidaccess'] == 1 && intval($result['paidaccess']) == 1 && intval($redirectpaid) != 0)
    {
        $redirectLink = trim($redirectpaid);
        if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
        else if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
        wp_redirect( $redirectLink ); exit;
    }
    
    $success = '';
    if (isset($_REQUEST["currency_code"]))
    {
        require_once(JG_PLUGIN_DIR."/stripe/init.php");       
        \Stripe\Stripe::setApiKey($wpjg_generalSettings['stripe_key']);
        try {
            if (!isset($_POST['stripeToken'])) throw new Exception("The Stripe Token was not generated correctly");
            // Create a Customer
            $customer = \Stripe\Customer::create(array(
              "source" => $_POST["stripeToken"],
              "email" => strip_tags(trim($result['email'])),
              "description" => $result['firstname'].' '.$result['lastname'])
            );

            $charge = \Stripe\Charge::create(array(
              "amount" => $wpjg_generalSettings['payamount']*100,
              "currency" => $_POST["currency_code"],           
              "description" => "Entry fee",
              "customer" => $customer->id
              /*  "source" => $_POST["stripeToken"], // obtained with Stripe.js*/
            ));
            //error_log(print_R($charge->__toJSON(), true));
            $ch_data = json_decode($charge->__toJSON());
            //error_log(print_R($ch_data, true));
            $wpdb->update(
                $wpdb->prefix . "jgusers",
                array(
                    'paidaccess' => 1,
                    'txn_id' => $ch_data->balance_transaction,
                ),
                array(                 
                    'userEnc' => trim($_SESSION['userEnc'])
                ));
                
            $success = 'Your payment was successful.';
            if (trim($redirectpaid) != '' && intval($redirectpaid) != 0)
            {
                $redirectLink = trim($redirectpaid);
                if (intval($redirectLink) != 0)
                    $redirectLink = get_permalink($redirectLink);
                else{
                    if (!jg_check_missing_http($redirectLink))
                        $redirectLink = 'http://'. $redirectLink;
                }       
                wp_redirect( $redirectLink ); exit;
            }
        }
        catch (Exception $e) {
            $errors = $e->getMessage();
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

    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('User', $_SESSION);
    $smarty->assign('UserD', $result);
    $smarty->assign('success', $success);
    
    if ($template != '') $smarty->display($template);    
    else $smarty->display('stripe.html');
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}