<?php
global $wpjg_login; 
$wpjg_login = false;
if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}

function wpjg_signon(){	
    global $error;
    global $wpjg_login;
    global $wpdb;
    
    $wpjg_generalSettings = get_option('jg_general_settings');

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login') && ($_POST['formName'] == 'login') ){

        /*wrong password - but maybe they changed the password at justgiving */
        include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
        $client = new JustGivingClient(
            $wpjg_generalSettings['ApiLocation'],
            $wpjg_generalSettings['ApiKey'],
            $wpjg_generalSettings['ApiVersion'],
            $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);        

        $hasJGAccount = $client->Account->IsEmailRegistered(trim($_POST['user-name']));
        //error_log(print_R($hasJGAccount, true));
        if($hasJGAccount){
            /* if login JG change password */
            $ret = $client->Account->ValidateAccount(trim($_POST['user-name']), trim($_POST['password']) );
            if ($ret->isValid ){  
                $client->Username = trim($_POST['user-name']);
                $client->Password = trim($_POST['password']);
                $_SESSION['email'] = trim($_POST['user-name']);
                $_SESSION['userEnc'] = base64_encode($_POST['user-name'].':'.trim($_POST['password']));
                $ret = $client->Account->GetUser(base64_encode($_POST['user-name'].':'.trim($_POST['password'])));
                if ($ret){
                    $sql = "INSERT INTO {$wpdb->prefix}jgusers (userEnc,userid,firstname,lastname,email,address, address2,towncity,county,postcode) VALUES (%s,%d,%s,%s,%s,%s,%s,%s,%s,%s) ON DUPLICATE KEY UPDATE userid = %d, firstname = %s, lastname = %s, email = %s, address = %s, address2 = %s, towncity = %s, county = %s, postcode = %s";
                    $sql = $wpdb->prepare($sql,base64_encode($_POST['user-name'].':'.trim($_POST['password'])),intval($ret->accountId),trim($ret->firstName),trim($ret->lastName),trim($_POST['user-name']),$ret->address->line1,$ret->address->line1,$ret->town,$ret->address->countyOrState,$ret->address->postcodeOrZipcode,intval($ret->accountId),trim($ret->firstName),trim($ret->lastName),trim($_POST['user-name']),$ret->address->line1,$ret->address->line1,$ret->town,$ret->address->countyOrState,$ret->address->postcodeOrZipcode);
                    $wpdb->query($sql);
                    
                }
                /*if in db then nothing else new row*/
            }                  
            else  $wpjg_login = new WP_Error('login', __("Password incorrect"));
        }
        else  $wpjg_login = new WP_Error('login', __("no Login"));
    }
}
add_action('init', 'wpjg_signon');

function jg_front_end_logout( $atts ){
    $loginFilterArray = array();
    ob_start();

    global $wpjg_login;
    $wpjg_generalSettings = get_option('jg_general_settings');
    extract(shortcode_atts(
        array(
        'forgot'=> 0, 
        'display' => true, 'redirect' => '', 'create'=> ''), $atts));

    $passworderror = '';
    $usernameerror = '';
    $permaLnk2 = get_home_url( '/' );
    if ( isset($_SESSION['userEnc']) ){ // Successful login
        $permaLnk2 = get_home_url( '/' );
        unset($_SESSION['userEnc']);
        $_SESSION = Array();
        foreach(array_keys($_SESSION) as $k) unset($_SESSION[$k]);        
        if(session_id() == '' || !isset($_SESSION)) session_destroy ( ); 
        if (trim($redirect) != ''){
            $permaLnk2 = trim($redirect);
            if (intval($permaLnk2) != 0) $permaLnk2 = get_permalink($permaLnk2);
            else if (!jg_check_missing_http($permaLnk2)) $permaLnk2 = 'http://'. $permaLnk2;
        }
        elseif (trim($create) != ''){
            $permaLnk2 = trim($create);
            if (intval($permaLnk2) != 0) $permaLnk2 = get_permalink($permaLnk2);
            else if (!jg_check_missing_http($permaLnk2)) $permaLnk2 = 'http://'. $permaLnk2;
        }       
    }

    if (!headers_sent($filename, $linenum)) {
        wp_redirect( $permaLnk2 );
        exit;
    } else {
        echo "Headers already sent in $filename on line $linenum\n" .
              "Cannot redirect, for now please click this <a " .
              "href=\"{$permaLnk2}\">link</a> instead\n";
        exit;
    }
}    
    
function jg_front_end_login( $atts ){
    $loginFilterArray = array();
    ob_start();

    global $wpjg_login;
    $wpjg_generalSettings = get_option('jg_general_settings');
    extract(shortcode_atts(
        array(
        'forgot'=> 0, 
        'display' => true, 'redirect' => '', 'register'=> 0, 'create'=> 0,'choose'=> 0, 'submit' => 'page', 'template' => ''), $atts));

    $passworderror = '';
    $usernameerror = '';
    //echo $permaLnk2;
    if ( isset($_SESSION['userEnc']) ){ // Successful login
        $permaLnk2 = jg_curpageurl();
        if (trim($redirect) != ''){
            $permaLnk2 = trim($redirect);
            if (intval($permaLnk2) != 0)
                $permaLnk2 = get_permalink($permaLnk2);
            else{
                if (!jg_check_missing_http($permaLnk2)) $permaLnk2 = 'http://'. $permaLnk2;
            }
        }
        elseif (trim($create) != ''){
            $permaLnk2 = trim($create);
            if (intval($permaLnk2) != 0)
                $permaLnk2 = get_permalink($permaLnk2);
            else{
                if (!jg_check_missing_http($permaLnk2)) $permaLnk2 = 'http://'. $permaLnk2;
            }
        }
        wp_redirect( $permaLnk2 );exit;
    }else{ // Not logged in
        if (!empty( $_POST['action'] ) && isset($_POST['formName']) ){
            if ($_POST['formName'] == 'login'){
                if (trim($_POST['user-name']) == ''){
                    if (isset($wpjg_generalSettings['loginWith']) && ($wpjg_generalSettings['loginWith'] == 'email')){
                        $loginFilterArray['emptyUsernameError'] = __('The email field is empty', 'justgiving').'.'; 
                        $loginFilterArray['emptyUsernameError'] = apply_filters('wpjg_login_empty_email_as_username_error_message', $loginFilterArray['emptyUsernameError']);                                    
                    }else{
                        $loginFilterArray['emptyUsernameError'] = __('The username field is empty', 'justgiving').'.'; 
                        $loginFilterArray['emptyUsernameError'] = apply_filters('wpjg_login_empty_username_error_message', $loginFilterArray['emptyUsernameError']);
                    }
                    $usernameerror = $loginFilterArray['emptyUsernameError'];
                }elseif (trim($_POST['password']) == ''){
                    $loginFilterArray['emptyPasswordError'] = __('The password field is empty', 'justgiving').'.'; 
                    $loginFilterArray['emptyPasswordError'] = apply_filters('wpjg_login_empty_password_error_message', $loginFilterArray['emptyPasswordError']);
                    
                    $passworderror =  $loginFilterArray['emptyPasswordError'];
                }
                if ( is_wp_error($wpjg_login) ){
                    $loginFilterArray['wpError'] = 'Incorrect password';
                    $loginFilterArray['wpError'] = apply_filters('wpjg_login_wp_error_message', $loginFilterArray['wpError'],$wpjg_login);
                    $passworderror =  $loginFilterArray['wpError'];
                }
            }
        }
        /* use this action hook to add extra content before the login form. */
        do_action( 'wppb_before_login' );
        global $vars;
        $vars = array();
        $forgotURL = '';
        if (trim($forgot) != '' && intval($forgot) > 0){
            $forgoturi = true;
            $forgotURL = get_permalink($forgot);
        }
        $chooseURL = '';        
        if (trim($choose) != '' && intval($choose) > 0){
            $chooseuri = true;
            $chooseURL = get_permalink($choose);
        }        
            
        require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
        $smarty = new Smarty();
        $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
        $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
        $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
        $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
        //print_R($_POST);
        if (isset($_POST['user-name'])) $smarty->assign('userName', esc_html( $_POST['user-name'] )); 
        else $smarty->assign('userName', '');        
        
        $formurl = jg_curpageurl();
        
        $smarty->assign('pageshortname',isset($_POST['pageshortname']) ? stripslashes($_POST['pageshortname']) : '');

        $smarty->assign('passworderror', $passworderror); 
        $smarty->assign('usernameerror', $usernameerror);
        $smarty->assign('submit', 'page');        
        $smarty->assign('pagetitle', isset($_POST['pagetitle']) ? stripslashes($_POST['pagetitle']) : '');
        $smarty->assign('errorpagetitle', isset($errors['pagetitle']) ? $errors['pagetitle']['message'] : ''); 
        $smarty->assign('nonce', wp_nonce_field('verify_true_login','login_nonce_field', true, false)); 
        $smarty->assign('jgoptinyes', ((isset($_POST['jgoptin']) && $_POST['jgoptin'] =='1') || !isset($_REQUEST['jgoptin'])) ? 'checked="checked"':'');
        $smarty->assign('jgoptinno', ((isset($_POST['jgoptin']) && $_POST['jgoptin'] =='0')) ? 'checked="checked"':'');
        $smarty->assign('choptinyes', ((isset($_POST['charityoptin']) && $_POST['charityoptin']=='1') || (isset($_SESSION['optin']) && $_SESSION['optin'] == 1 && $_POST['charityoptin'] != 0)  || (!isset($_REQUEST['charityoptin']) && (!isset($_SESSION['optin']) || $_SESSION['optin'] != 0))) ? 'checked="checked"':'');
        $smarty->assign('choptinno', (isset($_POST['charityoptin']) && $_POST['charityoptin'] =='0') ? 'checked="checked"':'');
        $smarty->assign('formurl', $formurl);
        $smarty->assign('forgotURL', $forgotURL);
        $smarty->assign('chooseURL', $chooseURL);
        $smarty->assign('settings', $wpjg_generalSettings);
        if ($template != '') $smarty->display($template);    
        else $smarty->display('login-page.html');            
  
    }
    /* use this action hook to add extra content after the login form. */
    do_action( 'wppb_after_login' );	
    $output = ob_get_contents();
    ob_end_clean();
    $loginFilterArray = apply_filters('wpjg_login', $loginFilterArray);
    return $output;
}