<?php

define('FLICKRBASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('FLICKRSITE_URL', site_url());
define('FLICKRDEBUG', true);
define('FLICKRVERSION', '1.0.0');

$upl_photo_destination_map = array(
    'photostream' => 'Photostream',
    'photoset' => 'Photoset',
);

function upl_get_cur_url() {
    $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
    $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
    $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
    $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["HTTP_HOST"].$port.$_SERVER["REQUEST_URI"];
    return $url;
}

function create_uplFlickr_obj() {
    global $bhfpf;
    unset($_SESSION['uplFlickr_auth_token']);
    $bhfpf = new uplFlickr(get_option('afg_api_key'), get_option('afg_api_secret')? get_option('afg_api_secret'): NULL);
    $bhfpf->setToken(get_option('phpFlickr_auth_token'));
}

?>