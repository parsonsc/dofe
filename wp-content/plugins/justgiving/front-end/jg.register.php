<?php
if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}

function get_jgip() {

	//Just get the headers if we can or else use the SERVER global
	if ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
	} else {
		$headers = $_SERVER;
	}
	//Get the forwarded IP if it exists
	if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		$the_ip = $headers['X-Forwarded-For'];
	} 
	elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
		$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
	} else {
		$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}
	return $the_ip;
}

//function to display the registration page
function jg_front_end_register($atts){
    ob_start();
    global $current_user;
    global $wp_roles;
    global $wpdb;
    global $error;	
    global $js_shortcode_on_front;
    

    /* Check if users can register. */
    $registration = get_option( 'users_can_register' );
        
    extract(shortcode_atts(array('forgot'=> 0, 'display' => true, 'redirect' => '', 'submit' => 'page', 'create' => '', 'thanks' => '', 'login' => '', 'template' => ''), $atts));
    $ordate = '';
    $errors = array();
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] 
        && !empty( $_POST['action'] ) 
        && $_POST['action'] == 'adduser' 
        && wp_verify_nonce($_POST['register_nonce_field'],'verify_true_registration') 
        && ($_POST['formName'] == 'register') ) {
        $default_role = get_option( 'default_role' );
        $user_pass = '';
        if (isset($_POST['password']))
            $user_pass = esc_attr( $_POST['password'] );
        $email = '';
        if (isset($_POST['email'])){
            $email = trim ($_POST['email']);
            $_SESSION['email'] = $email;
        }
        $_SESSION['optin'] = isset($_POST['optin']) ? $_POST['optin'] : 0;
        $_SESSION['country'] = isset($_POST['country']) ? $_POST['country'] : '';
        
        $user_name = jg_generate_random_username($email);
        $first_name = '';
        if (isset($_POST['firstname']))
            $first_name = trim ($_POST['firstname']);
        $last_name = '';
        if (isset($_POST['lastname']))
            $last_name = trim ($_POST['lastname']);        
        $userdata = array(
            'user_pass' => $user_pass,
            'user_login' => esc_attr( $user_name ),
            'first_name' => esc_attr( $first_name ),
            'last_name' => esc_attr( $last_name ),
            'user_email' => esc_attr( $email ),
            'role' => $default_role
        );
        /*
        if ($_POST['haveaccount'] == 0 &&
                $_POST['createpage'] == 1 &&
                (trim($userdata['user_pass']) == '' ||
                 trim($userdata['user_pass']) != trim($_POST['cpassword']))){

            $foundError = true;
        }
           */
        include_once(JG_PLUGIN_DIR.'/lib/functions.php');
        $results = array(
            'title' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'address' => '',
            'address2' => '',
            'town' => '',
            'county' => '',
            'postcode' => '',
            'packpost' => '',
            'createpage' => '',
            'haveaccount' => '',
            'optin' => 0,
            'country' => '',
            'heardabout' => '',
            'password' => '',
            'cpassword' => '',
            'tsandcs' => '',
        );	
        $rules = array(
            'title' => 'notEmpty',
            'title_alt' => 'other_title',
            'firstname' => 'notEmpty',
            'lastname' => 'notEmpty',
            'email' => 'email',
            'address' => 'notEmpty',
            /*'address2' => 'notEmpty',*/
            'town' => 'notEmpty',
            'postcode' => ($_POST['country'] == 'Ireland') ? '' : 'postCode',
            'createpage' => 'notEmpty',
            'country' => 'ukonly',
            'password' => 'length6',
           /* 'cpassword' => 'length6',*/
            'tsandcs' => 'notEmpty',            
        );
        $messages = array(
            'title' => 'Please choose your title',
            'firstname' => 'Please enter your first name',
            'lastname' => 'Please enter your surname',
            'email' => "Hmm. There's something wrong with this address. Please check.",
            'address' => 'Please enter your address',
            'address2' => 'Please enter your address',
            'town' => 'Please enter your town',
            'postcode' => 'Please enter your postcode',
            'packpost' => 'Would you like a fundraising pack',
            'createpage' => 'Would you like to create a fundraising page',
            'country' => 'ukonly',
            'password' => 'Please enter a password',
            /*'cpassword' => 'notEmpty',*/
            'tsandcs' => 'You must accept the terms and conditions'
        );    
        foreach ($results as $key => $value){
                $results[$key] = $_POST[$key];
        }
        

        
        $errors = validateJGInputs($results, $rules, $messages);
        if (count($errors) != 0) $foundError = true;
        if (($_POST['country'] != 'United Kingdom' && $_POST['country'] != 'Ireland') && $_POST['createpage'] == 1){
             $foundError = true;
             $errors['country']['message'] = "We're sorry - you can't create a JustGiving page from this country";
        }
        include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
        $wpjg_generalSettings = get_option('jg_general_settings');
        $client = new JustGivingClient(
            $wpjg_generalSettings['ApiLocation'],
            $wpjg_generalSettings['ApiKey'],
            $wpjg_generalSettings['ApiVersion'],
            $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true);
        //print_R($errors);
        $hasJGAccount = $client->Account->IsEmailRegistered(trim($results['email']));

        if ($hasJGAccount){
            $errors['email']['message'] = 'A JustGiving account exists for that email address';
            $foundError = true;
        }
        //print_R($errors);
        //print_R($hasJGAccount);        
        if (!$foundError){
			if ((int)$wpjg_generalSettings['lolagrove'] == 1 && isset($_SESSION['lolagrove'])){
				//from=lolagrove&id=1234.567&sub_id=subid1&callback_id=callbackid1
				$lolagrove = json_decode($_SESSION['lolagrove']);
				$dumpdata = array(
					'id' => $lolagrove->id,
					'email' => $results['email'],
					'title' => ($results['title'] == 'Other' && trim($_POST['other_title']) !== '') ? $_POST['other_title'] : $results['title'], 
					'firstname' =>  $results['firstname'],
					'lastname' => $results['lastname'],
					'address1' => isset($results['address']) ? $results['address'] : '',
					'address2' => isset($results['address2']) ? $results['address2'] : '',
					'towncity' => isset($results['town']) ? $results['town'] : '',
					'postcode' => isset($results['postcode']) ? $results['postcode'] : '',
					'timestamp' => date('YmdHis',time()),
					'terms' => 'y',
					'sub_id' => $lolagrove->sub_id,
					'ipaddress' => get_jgip(),
					'callback_id' => $lolagrove->callback_id,
					'supplier_lead_id' => '',
					'securekey' => '1c7c35d9-d962-4f6a-be83-d826d9d8f72f'
				);
				$lurl = 'https://v3.lolagrove.com/data.ashx?';
				$qstring = http_build_query($dumpdata);
				$res = wp_remote_get($lurl.$qstring);
                		//error_log('lola = '.$lurl.$qstring);
				//error_log('localret = '.print_R($res, true));
			}
			/*
	       	https://v3.lolagrove.com/data.ashx?id=12432.2724&email=demo@lolagrove.com&title=Mr&firstname=Robin&lastname=Caller&address1=7 Brayfield Terrace&address2=Barnsbury&towncity=London&postcode=N1 1HZ&timestamp=20101231235959&terms=y&sub_id=abc123&ipaddress=127.0.0.1&callback_id=abc123&supplier_lead_id=xxx&securekey=1c7c35d9-d962-4f6a-be83-d826d9d8f72f
        	*/        
            //print_R($_POST);
            //print_R($userdata);
            //$new_user = wp_insert_user( $userdata );
            unset($_POST['password']);
            unset($_POST['cpassword']);
            unset($_POST['firstname']);
            unset($_POST['lastname']);
            unset($_POST['action']);
            unset($_POST['register_nonce_field']);
            unset($_POST['formName']);
            unset($_POST['submit']);
            unset($_POST['_wp_http_referer']);
            if ($results['country'] == 'Ireland' ) $results['postcode'] = 'n/a';
            $wpdb->insert( 
                $wpdb->prefix . "jgusers", 
                array( 
                    'title' => ($results['title'] =='Other' && trim($_POST['other_title']) !== '') ? $_POST['other_title'] : $results['title'], 
                    'firstname' => $results['firstname'], 
                    'lastname' => $results['lastname'], 
                    'dob' => $results['dob'], 
                    'email' => $results['email'], 
                    'address' => isset($results['address']) ? $results['address'] : '', 
                    'address2' => isset($results['address2']) ? $results['address2'] : '', 
                    'towncity' => isset($results['town']) ? $results['town'] : '', 
                    'county' => isset($results['county']) ? $results['county'] : '', 
                    'postcode' => isset($results['postcode']) ? $results['postcode'] : '',
                    'packbypost' => isset($results['packpost']) ? $results['packpost'] : '',
                    'cpage' => isset($results['createpage']) ? $results['createpage'] : '',
                    'hasaccount' => isset($results['haveaccount']) ? $results['haveaccount'] : '',
                    'userEnc' => base64_encode($results['email'].':'.trim($userdata['user_pass'])),
                    'pageurl' => '',
                    'signupdate' => time(),
                    'eventstart' => isset($_POST['eventstart']) ? strtotime($_POST['eventstart']) : '',
                    'eventend' => isset($_POST['eventend']) ? strtotime($_POST['eventend']) : '',
                    'optin' => isset($results['optin']) ? $results['optin'] : 0,
                    'country' => isset($results['country']) ? $results['country'] : '',
                    'heardabout' => isset($results['heardabout']) ? $results['heardabout'] : '',
                    'tsandcs' => $_POST['tandcs']
                )
            ); 
            
            if($hasJGAccount){
                /* if login JG change password */
                if ($_POST['createpage'] == 1)
                {
                    //create a page
                    $_SESSION['email'] = trim($results['email']);
                    $_SESSION['userEnc'] = base64_encode($results['email'].':'.trim($userdata['user_pass']));
                
                    $redirectLink = trim($login);
                    if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                    else{
                        if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                    }
                    wp_redirect( $redirectLink ); exit;
                }
                elseif ((intval(trim($redirect)) != 0)){
                    $redirectLink = get_permalink($redirect);
                    wp_redirect( $redirectLink );
                    exit;
                }
                else
                { 
                    // -> send stright to thanks - send email
                    $redirectLink = trim($thanks);
                    if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                    else{
                        if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                    }
                    wp_redirect( $redirectLink );
                    $vars = array(
                        'firstname' => $results['firstname']
                    );
                    sendthanks($results['email'], $results['firstname']. ' '. $results['lastname'], $vars, 0);                            
                    exit;
                }
            }
            else{
                error_log('register 293 '.print_R($_POST, true));
                error_log('register 293 '.print_R($userdata, true));
                if ($_POST['haveaccount'] == 0  && trim($userdata['user_pass']) != '' ){

                //include_once(JG_PLUGIN_DIR.'/lib/ApiClients/Model/CreateAccountRequest.php');
                    $request = array();
                    $request['email'] = trim($results['email']);
                    $request['firstName'] = $results['firstname'];
                    $request['lastName'] = $results['lastname'];
                    $request['password'] = trim($userdata['user_pass']);
                    $request['title'] = ($results['title'] =='Other' && trim($_POST['other_title']) !== '') ? $_POST['other_title']: $results['title'];
                    $request['address']['line1'] = trim($results['address']);
                    $request['address']['line2'] = trim($results['address2']);
                    $request['address']['countyOrState'] = trim($results['county']);
                    $request['address']['townOrCity'] = trim($results['town']);
                    $request['address']['postcodeOrZipcode'] = trim($results['postcode']);
                    $request['address']['country'] = trim($results['country']);
                    $request['acceptTermsAndConditions'] = true;
					//error_log(print_R($request, true));
                    $response = $client->Account->Create($request);
                    //$current = print_R($_POST, true);
                    //file_put_contents('curldata.txt', $current, FILE_APPEND);
                    $ret = $client->Account->GetUser(base64_encode($request['email'].':'.$request['password']));
                    error_log('getuser register 315 '.print_R($ret, true));
                    if ($ret){
                        $wpdb->update(
                            $wpdb->prefix . "jgusers",
                            array(
                                'userid' => intval($ret->accountId)
                            ),
                            array(                 
                                'email' => $request['email']
                            )
                        );
                    }                    
                    if ($_POST['createpage'] == 1)
                    {
                        $_SESSION['email'] = trim($results['email']);                    
                        $_SESSION['userEnc'] = base64_encode($results['email'].':'.trim($userdata['user_pass']));
                        //create a page
                        $redirectLink = trim($create);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                        //echo 'goto' .$redirectLink;
                        //$current = print_R('goto a' .$redirectLink, true);
                        //file_put_contents('curldata.txt', $current, FILE_APPEND);

                        wp_redirect( $redirectLink ); exit;
                    }
                    else
                    {
                        //echo $thanks;
                        // -> send stright to thanks - send email
                        $redirectLink = trim($thanks);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                        //$current = print_R('goto b' .$redirectLink, true);
                        //file_put_contents('curldata.txt', $current, FILE_APPEND);
                        $vars = array(
                            'firstname' => $results['firstname']
                        );
                        sendthanks($results['email'], $results['firstname']. ' '. $results['lastname'], $vars, 0);                           
                        wp_redirect( $redirectLink );
                        exit;
                    }
                }
                else
                {
                    if ($_POST['createpage'] == 1)
                    {
                        $_SESSION['email'] = trim($results['email']);                    
                        //login with the account you said you had 
                        // even though you don't have an account on this email
                        // cos we'd have found it by now
                        $redirectLink = trim($login);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                        //$current = print_R('goto c' .$redirectLink, true);
                        //file_put_contents('curldata.txt', $current, FILE_APPEND);

                        wp_redirect( $redirectLink ); exit;
                    }
                    else{
                        //echo 'meh';
                        //echo $thanks;                    
                        /* what to do if login is incorrect but wanted to create a page ?*/
                        // -> send stright to thanks
                        //echo $thanks;
                        $redirectLink = trim($thanks);
                        if (intval($redirectLink) != 0)
                            $redirectLink = get_permalink($redirectLink);
                        else{
                            if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                        }
                        //echo $redirectLink;
                        $vars = array(
                            'firstname' => $results['firstname']
                        );
                        sendthanks($results['email'], $results['firstname']. ' '. $results['lastname'], $vars, 0);      
                        //$current = print_R('goto d' .$redirectLink, true);
                        //file_put_contents('curldata.txt', $current, FILE_APPEND);                        
                        wp_redirect( $redirectLink );
                        exit;
                    }
                }
            }
            $redirectLink = trim($redirect);
            if (intval($redirectLink) != 0)
                $redirectLink = get_permalink($redirectLink);
            else{
                if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
            }
            wp_redirect( $redirectLink ); exit;
        }
    }
    // if ( $registration || current_user_can( 'create_users' ) ) :
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
   
    $smarty->assign('formurl', jg_curpageurl());
    $smarty->assign('Errors', $errors);

    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);
        
    $countries = get_transient('justgiving_countries')  ;
    if (DEBUG){
        $countries = NULL;
    }  

    if (!$countries){
        $countries = $client->Country->GetCountries();
    } 
    //print_r($countries);
    if (!DEBUG){
        set_transient('justgiving_countries', $countries, 60 * 60 * 12); // 1/2 day storage
    }
    include_once(JG_PLUGIN_DIR.'/lib/functions.php');

    if (!isset($_POST['country']) || $_POST['country'] == '') $_POST['country'] ="United Kingdom";
    $smarty->assign('countries', $countries);  
    
    $smarty->assign('maxdate', date('Y-m-d'));
    

    if (isset($_POST['dob'])) $_POST['dob'] = $ordate;
    
    $smarty->assign('Get', $_GET);    
    $smarty->assign('Post', $_POST);
    $smarty->assign('nonce', wp_nonce_field('verify_true_registration','register_nonce_field', true, false)); 
    $smarty->assign('home',get_home_url());
    $smarty->assign('settings', $wpjg_generalSettings);    
    if ($template != '') $smarty->display($template);    
    else $smarty->display('register.html');
    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}