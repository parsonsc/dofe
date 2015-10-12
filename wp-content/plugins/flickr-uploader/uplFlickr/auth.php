<?php
    /* Last updated with uplFlickr 2.3.2
     *
     * Edit these variables to reflect the values you need. $default_redirect 
     * and $permissions are only important if you are linking here instead of
     * using uplFlickr::auth() from another page or if you set the remember_uri
     * argument to false.
     */
    $api_key                 = "[your api key]";
    $api_secret              = "[your api secret]";
    $default_redirect        = "/";
    $permissions             = "read";
    $path_to_uplFlickr_class = "./";

    ob_start();
    require_once($path_to_uplFlickr_class . "uplFlickr.php");
    @unset($_SESSION['uplFlickr_auth_token']);
     
	if ( isset($_SESSION['uplFlickr_auth_redirect']) && !empty($_SESSION['uplFlickr_auth_redirect']) ) {
		$redirect = $_SESSION['uplFlickr_auth_redirect'];
		unset($_SESSION['uplFlickr_auth_redirect']);
	}
    
    $bhfpf = new uplFlickr($api_key, $api_secret);
 
    if (empty($_GET['frob'])) {
        $bhfpf->auth($permissions, false);
    } else {
        $bhfpf->auth_getToken($_GET['frob']);
	}
    
    if (empty($redirect)) {
		header("Location: " . $default_redirect);
    } else {
		header("Location: " . $redirect);
    }
 
?>