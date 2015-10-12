<?php 
/**
 * Template Name: Celeb Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content celebs celebs_content" id="mainContent">       
                <section class="">
                    <div class="inner_content small_container">
        <?php if (have_posts()) : while (have_posts()) : the_post();
        $pid = get_the_ID();
        $post = get_post();
        ?>                    
                        <header>
                           <?php the_content(); ?>
                        </header>
        <?php endwhile; endif;?>          
<?php
	$celebs = new WP_Query();
    $celebs->query(array('post_type' => 'celeb', 'posts_per_page'=> -1, 'post_status'=>'publish', 'orderby' => 'date menu_order title', 'order' => 'DESC'));
    //echo $clients->request;
	if ($celebs->have_posts()):
        $x = 0;
		while ($celebs->have_posts()): 
            $x++;
			$celebs->the_post();
			$custom = get_post_meta(get_the_ID(), 'partnerurl', true);
?>                
                        <div class="celebrity celeb_<?php echo str_pad($x, 2, '0', STR_PAD_LEFT);?>">
                            <?php echo get_the_post_thumbnail($id, null);?>
                            <article>
                                <?php the_title('<h2>','</h2>'); ?>
                                <?php the_content(); ?>
                            </article>
                        </div>
<?php
		endwhile;
	endif;
?>                        
                    <div class="tagline">
                            <h2>Lorem ipsum dolor sit amet</h2>
                            <a href="<?php echo get_permalink(9)?>" class="site_cta">Start your challenge</a>
                        </div>
                    </div>
                </section>                                      
            </div>
<?php 
get_footer();