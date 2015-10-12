<?php

function jg_front_end_sagepay($atts){
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
    if (trim($template) == '') $template = 'sagepay.html'; 
    $crypt = '';    
    if (isset($_REQUEST["action"]))
    {
        require_once(JG_PLUGIN_DIR."/sagepay/sagepay.php");
        $p 				= new SagePay(); // paypal class
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
                    $p->setSuccessURL($this_script.'?action=success'); // return URL after the transaction got over
                    $p->setFailureURL($this_script.'?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
                    $p->setCurrency($_POST["currency_code"]);
                    $p->setDescription($_POST["product_name"]);
                    $p->setAmount($wpjg_generalSettings['payamount']);
                    $p->setBillingFirstnames($_POST["payer_fname"]);
                    $p->setBillingSurname($_POST["payer_lname"]);
                    $p->setBillingAddress1($_POST["payer_address"]);
                    $p->setBillingCity($_POST["payer_city"]);
                    $p->setBillingState($_POST["payer_state"]);
                    $p->setBillingCountry($_POST["payer_country"]);
                    $p->setBillingPostCode($_POST["payer_zip"]);
                    $p->setVendorTxCode($_POST["invoice"]);
                    $p->setDeliverySameAsBilling();

                    $xml = new DOMDocument();
                    $basketNode = $xml->createElement("basket");
                    $itemNode = $xml->createElement("item");
                    
                    $descriptionNode =  $xml->createElement( 'description' );
                    $descriptionNode->nodeValue = 'Entry fee';
                    $itemNode -> appendChild($descriptionNode);
                    
                    $quantityNode =  $xml->createElement('quantity');
                    $quantityNode->nodeValue = $_POST["product_quantity"];
                    $itemNode -> appendChild($quantityNode);
                    
                    $unitNetAmountNode =  $xml->createElement('unitNetAmount');
                    $unitNetAmountNode->nodeValue = $wpjg_generalSettings['payamount']  ;
                    $itemNode -> appendChild($unitNetAmountNode);
                    
                    $unitTaxAmountNode =  $xml->createElement('unitTaxAmount');
                    $unitTaxAmountNode->nodeValue = '0';
                    $itemNode -> appendChild($unitTaxAmountNode);
                    
                    $unitGrossAmountNode =  $xml->createElement('unitGrossAmount');
                    $unitGrossAmountNode->nodeValue = $wpjg_generalSettings['payamount'];
                    $itemNode -> appendChild($unitGrossAmountNode);
                    
                    $totalGrossAmountNode =  $xml->createElement('totalGrossAmount');
                    $totalGrossAmountNode->nodeValue = $wpjg_generalSettings['payamount'];
                    $itemNode -> appendChild($totalGrossAmountNode);
                    
                    $basketNode->appendChild( $itemNode );
                    $xml->appendChild( $basketNode );
                    
                    $p->setBasketXML($xml->saveHTML());
                         
                    $crypt = $sagePay->getCrypt();                        
                break;  

            case "success": 
                if ($_REQUEST['crypt']) {
                    $responseArray = $sagePay -> decode($_REQUEST['crypt']);
                    //Check status of response
                    if($responseArray["Status"] === "OK"){
                        $wpdb->update(
                            $wpdb->prefix . "jgusers",
                            array(
                                'paidaccess' => 1,
                                'txn_id' => $responseArray["VPSTxId"]
                            ),
                            array(                 
                                'paytoken' => trim($_POST["VendorTxCode"])
                            )); 
                    }elseif($responseArray["Status"] === "ABORT"){
                        // Payment Cancelled
                    }else{
                        // Payment Failed
                        throw new \Exception($responseArray["StatusDetail"]);
                    }
                    print '<pre>';
                    print_r($responseArray);
                    print '</pre>';
                    exit; 
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
    $smarty->assign('crypt', $crypt);
    $smarty->assign('vendor', $wpjg_generalSettings['paypal_femail']);
    
    $smarty->display($template);    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}