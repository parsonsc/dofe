<?php
    /* Last updated with uplFlickr 1.4
     *
     * If you need your app to always login with the same user (to see your private
     * photos or photosets, for example), you can use this file to login and get a
     * token assigned so that you can hard code the token to be used.  To use this
     * use the uplFlickr::setToken() function whenever you create an instance of 
     * the class.
     */

    require_once("uplFlickr.php");
    $bhfpf = new uplFlickr("<api key>", "<secret>");
    
    //change this to the permissions you will need
    $bhfpf->auth("read");
    
    echo "Copy this token into your code: " . $_SESSION['uplFlickr_auth_token'];
    
?>