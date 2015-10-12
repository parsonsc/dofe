<?php 
/**
 * Template Name: Resources Page
 *
 * @package DofE
 */

get_header();
?>
           <div class="main_content resources_content" id="mainContent">       
                <section class="">
                    <div class="inner_content">
                        <header><?php if (have_posts()) : while (have_posts()) : the_post();
                        $pid = get_the_ID();
                        $post = get_post();
                        the_content(); endwhile; endif;?>
                    </header>

                    <div class="resource_grid">                        
                        <?php echo do_shortcode('[wpfilebase tag="list" tpl="resource" /]'); ?>
                        <div class="tagline">
                            <h2>Lorem ipsum dolor sit amet</h2>
                            <a href="<?php echo get_permalink(9)?>" class="site_cta">Start your challenge</a>
                        </div>                     
                    </div>
                </section>                                      
            </div> 
<?php 
get_footer();