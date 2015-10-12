<?php 
if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
} 

function csssafename($int, $span = 10){
    $string = '';
    $int = $int % $span;
    switch ((int)$int){
        case 0: return 'first';
        case 1: return 'second';
        case 2: return 'third';
        case 3: return 'fourth';
        case 4: return 'fifth';
        case 5: return 'sixth';
        case 6: return 'seventh';
        case 7: return 'eighth';
        case 8: return 'ninth';
        case 9: return 'tenth';
    }
}
ob_start();
?><!DOCTYPE html>
<!--[if lt IE 7]> <html <?php dofe_html_schema(); ?> <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html <?php dofe_html_schema(); ?> <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html <?php dofe_html_schema(); ?> <?php language_attributes(); ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php dofe_html_schema(); ?> <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title itemprop="name"><?php wp_title(''); ?></title>
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <?php wp_head(); ?>
</head>
<body>
    <?php include_once("analyticstracking.php") ?>
    <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    
    <div class="site_container<?php echo ' '. basename(get_page_template(), ".php").' '; ?><?php echo (basename(get_page_template(), ".php") =='page')? ' form form_thanks ': ''; ?><?php if ( is_page_template('page-templates/about.php') ) { ?> generic <?php }?><?php if ( is_page_template('page-templates/home.php') ) { ?> hp <?php }?> <?php echo lcfirst(str_replace(" ", "", ucwords(trim(strtolower(preg_replace('/\b[a-zA-Z]{1,2}\b/u','',preg_replace('/[^a-zA-Z]+/u',' ', get_post_type()))))))); ?> <?php echo lcfirst(str_replace(" ", "", ucwords(trim(strtolower(preg_replace('/\b[a-zA-Z]{1,2}\b/u','',preg_replace('/[^a-zA-Z]+/u',' ', get_the_title()))))))); ?>" id="site-container">
        <div class="cookie">
            <div class="inner_content">
                <strong></strong>Cookie notification:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sollicitudin velit ut lectus euismod, sit amet fermentum mi maximus ut sollicitudin velit ut lectus euismod, sit amet fermentum mi maximus. <strong><a href="#" id="dismissCookieWarning">Close <i class="fa fa-times"></i></a></strong>
            </div>                
        </div>    
        <header class="site_header<?php if ( is_page_template('page-templates/home.php') ) { ?> full_bg<?php }?> ">
            <!-- Navigation -->
            <nav class="site_nav">
                <div class="logo">
                    <a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/logo.png" alt="DofE"></a>
                </div>
                <button class="mobile_button show_mobile">
                    <i class="fa fa-bars"></i> Menu
                </button>
                <div class="signup">
                    <a href="<?php echo get_permalink(9); ?>">Sign Up</a>
                </div>
                <ul>        
                    <?php core_nav_menu( array( 'theme_location' => 'main-nav', 'container' => false, 'fallback_cb'=> 'default_page_menu' ) ); ?>                
                    <div class="social_share show_mobile">
                        <ul>
                            <li class="twitter"><a href="https://twitter.com/DofE"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/twitter.png" alt="Share on Twitter"></a></li>
                            <li class="facebook"><a href="https://www.facebook.com/theDofE"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/facebook.png" alt="Share on Facebook"></a></li>
                        </ul>
                    </div>

                    <li class="show_mobile">
                        <a href="#" class="up"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>                        
            </nav> 
            <aside class="social_share os-animation show_desktop" data-os-animation="fadeInRight" data-os-animation-delay="0.75s"> 
                <ul>
                    <li class="twitter"><a href="https://twitter.com/DofE"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/twitter.png" alt="Share on Twitter"></a></li>
                    <li class="facebook"><a href="https://www.facebook.com/theDofE"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/facebook.png" alt="Share on Facebook"></a></li>
                </ul>
            </aside>
            <?php if ( is_page_template('page-templates/home.php') ) { ?>
            <div class="header_tagline">
                <img src="<?php echo get_template_directory_uri(); ?>/images/hp/tagline.png" alt="" class="strapline">
                <p>There's a challenger in all of us and there's never been a better time to unleash yours. In our diamond anniversary year, we're inviting everyone to take part in the DofE Diamond Challenge to raise money and help disadvantaged young people to shine.</p>
                <a href="#" class="site_cta">learn more about the challenge</a>
            </div>                   
            <footer class="click_down os-animation" data-os-animation="fadeIn" data-os-animation-delay="1s">
                <a href="#mainContent">Find out more <i class="fa fa-chevron-down"></i></a>
            </footer>
            <?php }else{ ?>
            <div class="header_tagline">
                <?php the_title('<h1>','</h1>'); ?>
            </div>
            <?php } ?>
        </header>        