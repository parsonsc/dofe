<?php


function jg_front_end_choose( $atts ){
    $loginFilterArray = array();
    ob_start();

    global $wpjg_login;
    $wpjg_generalSettings = get_option('jg_general_settings');
    extract(shortcode_atts(
        array(
        'login'=> 0, 
        'register' => 0, 'submit' => 'page', 'template' => ''), $atts));

    // Not logged in
    if (!empty( $_POST['action'] ) && isset($_POST['formName']) ){
        switch($_POST['choose']){
            case 'login':
                $redirectLink = trim($login);
                if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                else{
                    if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                }       
                wp_redirect( $redirectLink ); exit;        
                break;
            case 'register':
                $redirectLink = trim($register);
                if (intval($redirectLink) != 0) $redirectLink = get_permalink($redirectLink);
                else{
                    if (!jg_check_missing_http($redirectLink)) $redirectLink = 'http://'. $redirectLink;
                }       
                wp_redirect( $redirectLink ); exit;        
                break;
        }
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

    $smarty->assign('submit', 'page');        
    $smarty->assign('pagetitle', isset($_POST['pagetitle']) ? stripslashes($_POST['pagetitle']) : '');
    $smarty->assign('errorpagetitle', isset($errors['pagetitle']) ? $errors['pagetitle']['message'] : ''); 
    $smarty->assign('nonce', wp_nonce_field('verify_true_login','login_nonce_field', true, false)); 
    $smarty->assign('formurl', $formurl);
    $smarty->assign('forgotURL', $forgotURL);
    $smarty->assign('settings', $wpjg_generalSettings);
    if ($template != '') $smarty->display($template);    
    else $smarty->display('account-choose.html');            
  

	
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}