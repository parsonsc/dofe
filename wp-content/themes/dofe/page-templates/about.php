<?php 
/**
 * Template Name: About Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content about_content" id="mainContent">       
                <section class="about_challenges">
                    <div class="inner_content">
                        <header>
                            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                            <?php the_content();?>
                            <?php endwhile; endif;?>
                        </header>
                        
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>                    
<?php
$cont = htmlspecialchars_decode(get_post_meta( get_the_ID(), 'second_column', true ));
$cont = apply_filters('the_content', $cont);
echo str_replace(']]>', ']]&gt;', $cont);
?>  
<?php endwhile; endif;?>                            

                        <div class="tagline">
                            <h2>Lorem ipsum dolor sit amet</h2>
                            <a href="<?php echo get_permalink(9)?>" class="site_cta">Start your challenge</a>
                        </div>

                    </div>
                </section>      
            </div>
<?php 
get_footer();