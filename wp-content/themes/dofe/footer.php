        <!-- site footer -->
        <section class="partners" >
            <div class="inner_content">
                <header>
                    <p><strong>Our Headline Diamond Partners</strong></p>
                    <p>2016 marks the Diamond Anniversary of the DofE. In this very special year, meet the businesses
                    helping to change young people's lives.</p>
                </header>
                <div class="partner_logo">
<?php
	$partners = new WP_Query();
    $partners->query(array('post_type' => 'partners', 'posts_per_page'=> -1, 'post_status'=>'publish', 'orderby' => 'date menu_order title', 'order' => 'ASC'));
    //echo $clients->request;
	if ($partners->have_posts()):
?>
              <ul>
<?php
        $x = 0;
		while ($partners->have_posts()): 
			$partners->the_post();
			$custom = get_post_meta(get_the_ID(), 'partnerurl', true);
?>
		<li class="<?php echo csssafename($x, 6);?>">
            <a href="<?php echo $custom ?>">
            	<?php echo get_the_post_thumbnail(get_the_ID(), null, array('alt' => get_the_title(),'title' => ""));?>                
            </a>
		</li>
<?php
            $x++;
		endwhile;
?>
            </ul>       
<?php
	endif;
?>                   
                </div>
            </div>
        </section>  
        <div class="clear"></div>

        <!-- Site footer -->
        <footer class="site_footer os-animation" data-os-animation="" data-os-animation-delay="0s">
            <div class="inner_content">
                <div class="footer_logo">
                    <a href="<?php echo home_url(); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_logo.png" alt="DofE">
                    </a>
                </div>

                <div class="email_signup">
                    <?php echo do_shortcode('[subscribe]'); ?>
                </div>

                <div class="footer_social">
                    <p>Follow DofE</p>
                    <ul>
                        <li>
                            <a href="https://twitter.com/DofE?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_twitter.png" alt="Follow us on Twitter"></a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/theDofE" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_facebook.png" alt="Like us on Facebook"></a>
                        </li>
                        <li>
                            <a href="https://instagram.com/dofewales/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_insta.png" alt="View us on Instagram"></a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/user/theDofEUK" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_youtube.png" alt="Watch us on Youtube"></a>
                        </li>
                    </ul>
                </div>

                <div class="bottom_bar">
                    <ul>
                        <li>&copy; <?php echo date('Y');?> DofE, is a registered charity No. 1072490</li>
                        <?php core_nav_menu( array( 'theme_location' => 'footer-links', 'container' => false, 'fallback_cb' => 'default_page_menu' ) ); ?>
                    </ul>
                </div>
            </div>
        </footer>
    </div>            

    <?php wp_footer(); ?>
  </body>
</html>
<?php ob_end_flush(); ?>