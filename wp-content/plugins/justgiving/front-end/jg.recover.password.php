<?php
//function needed to check if the current page already has a ? sign in the address bar
if(!function_exists('jg_curpageurl_password_recovery')){
    function jg_curpageurl_password_recovery() {
        $pageURL = 'http';
        if ($_SERVER["SERVER_PORT"] == "443")
			$pageURL .= "s";
        $pageURL .= "://";  
        $pageURL .= $_SERVER["SERVER_NAME"];
        if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
			$pageURL .= ":".$_SERVER["SERVER_PORT"];        
        $pageURL .= $_SERVER["REQUEST_URI"];        
        $questionPos = strpos( (string)$pageURL, '?' );
        $submittedPos = strpos( (string)$pageURL, 'submitted=yes' );
        if ($submittedPos !== false)
            return $pageURL;
        elseif($questionPos !== false)
            return $pageURL.'&submitted=yes';
        else
            return $pageURL.'?submitted=yes';
    }
}


//function to display the password recovery page
function jg_front_end_password_recovery($atts){
    ob_start();
    global $current_user;
    global $wp_roles;
    global $wpdb;
    global $error;	
    global $jg_shortcode_on_front;
    global $wpdb;
    
    $linkLoginName = '';
    $linkKey = '';
    
    ob_start();
    extract(shortcode_atts(array('login'=> 0, 'display' => true, 'home' => 0, 'template' => ''), $atts));
    $message = '';

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] 
        && !empty( $_POST['action'] ) && $_POST['action'] == 'recover_password' 
        && wp_verify_nonce($_POST['password_recovery_nonce_field'],'verify_true_password_recovery') ) {
        $postedData = $_POST['username_email'];	//we get the raw data

        //check to see if it's an e-mail (and if this is valid/present in the database) or is a username
        if (is_email($postedData)){

            $jg_generalSettings = get_option('jg_general_settings');
            include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
            $client = new JustGivingClient(
                $jg_generalSettings['ApiLocation'],
                $jg_generalSettings['ApiKey'],
                $jg_generalSettings['ApiVersion'],
                $jg_generalSettings['TestUsername'], $jg_generalSettings['TestValidPassword']);
            $hasJGAccount = $client->Account->IsEmailRegistered(trim($postedData));
            if ($hasJGAccount){
                $ret = $client->Account->RequestPasswordReminder($postedData);
                if (!$ret){
                    $recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = '<strong>'. __('ERROR', 'justgiving') .': </strong>' . sprintf(__( 'There was an error while trying to send the activation link to %1$s!', 'justgiving'), $postedData);
                    $recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = apply_filters('jg_recover_password_sent_message_error_sending', $recoverPasswordFilterArray['sentMessageCouldntSendMessage']);
                    $messageNo = '5';
                    $message = $recoverPasswordFilterArray['sentMessageCouldntSendMessage'];
                }
                else{
                    $permaLnk2 = '';
                    if (trim($login) != ''){
                        $permaLnk2 = trim($login);
                        if (intval($permaLnk2) != 0)
                            $permaLnk2 = get_permalink($permaLnk2);
                        else{
                            if (!jg_check_missing_http($permaLnk2)) $permaLnk2 = 'http://'. $permaLnk2;
                        }
                    }
                    $message = __( '<p>Your password has been sent to you, please check your details.</p>', 'justgiving');
                    if (trim($permaLnk2) != ''){
                        $message .= '<a class="back-home-thanks" href="'.$permaLnk2.'">Back to login page</a>';
                        //$message .= '<meta http-equiv="Refresh" content="3;url='.$permaLnk2.'" />';
                    }
                    $messageNo = '1';
                }
            }else{
                $recoverPasswordFilterArray['sentMessage2'] = __('The email address entered wasn\'t found in the database!', 'justgiving').'<br/>'.__('Please check that you entered the correct email address.', 'justgiving');
                $recoverPasswordFilterArray['sentMessage2'] = apply_filters('jg_recover_password_sent_message2', $recoverPasswordFilterArray['sentMessage2']);
                $messageNo = '2';
                $message = $recoverPasswordFilterArray['sentMessage2'];
            }
        }
    }
    
    /* use this action hook to add extra content before the password recovery form. */
    do_action( 'jg_before_recover_password_fields' );
    //display error message and the form
    require_once(JG_PLUGIN_DIR.'/lib/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = JG_PLUGIN_DIR.'/smarty/templates/';
    $smarty->compile_dir  = JG_PLUGIN_DIR.'/smarty/templates_c/';
    $smarty->config_dir   = JG_PLUGIN_DIR.'/smarty/configs/';
    $smarty->cache_dir    = JG_PLUGIN_DIR.'/smarty/cache/';
               
    $smarty->assign('formurl', $formurl);                       
    if (($messageNo == '') || ($messageNo == '2') || ($messageNo == '4')){
        $smarty->assign('nonce', wp_nonce_field('verify_true_password_recovery','password_recovery_nonce_field', true, false)); 
    }elseif (($messageNo == '5')  || ($messageNo == '6')){
        $smarty->assign('message2', '<p class="warning">'.$message.'</p>');        
    }
    $homeurl = '';
    if (intval($home) != 0){
        $homeurl = trim($home);
        if (intval($homeurl) != 0)
            $homeurl = get_permalink($homeurl);
        else{
            if (!jg_check_missing_http($homeurl)) $homeurl = 'http://'. $homeurl;
        }
    }
    $loginurl = '';
    if (intval($login) != 0){
        $loginurl = trim($login);
        if (intval($loginurl) != 0)
            $loginurl = get_permalink($loginurl);
        else{
            if (!jg_check_missing_http($loginurl)) $loginurl = 'http://'. $loginurl;
        }
    }    
    $smarty->assign('homeurl', $homeurl);
    $smarty->assign('loginurl', $loginurl);
    if ($messageNo == 1)
    {
        $smarty->assign('message', $message);   
        if ($template != '') $smarty->display($template);    
        else $smarty->display('recovered-page.html');     
    }
    else
    {
        if ($template != '') $smarty->display($template);    
        else $smarty->display('recover-page.html'); 
    }
    $smarty->assign('settings', $wpjg_generalSettings);    
    /* use this action hook to add extra content after the password recovery form. */
    do_action( 'jg_after_recover_password_fields' );
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}