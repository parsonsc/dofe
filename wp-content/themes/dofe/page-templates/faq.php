<?php 
/**
 * Template Name: FAQ Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content faq_content" id="mainContent">       
                <section class="">
                    <div class="inner_content">
         
<?php
	$celebs = new WP_Query();
    $celebs->query(array('post_type' => 'faq', 'posts_per_page'=> -1, 'post_status'=>'publish', 'orderby' => 'date menu_order title', 'order' => 'ASC'));
    //echo $clients->request;
	if ($celebs->have_posts()):
?>
                        <div class="accordion">
                            
<?php    
        $x = 0;
		while ($celebs->have_posts()): 
            $x++;
			$celebs->the_post();
			$custom = get_post_meta(get_the_ID(), 'partnerurl', true);
?>                
                        <div class="accordion-section">
                            <a class="accordion-section-title" href="#accordion-1"><i class="fa fa-caret-right"></i> <?php the_title(); ?></a>
                            <div id="accordion-1" class="accordion-section-content">
                                <?php the_content(); ?>
                            </div>
                        </div>

<?php
		endwhile;
?>
                    </div>
                            
<?php          
	endif;
?>                        
                    <?php if (have_posts()) : while (have_posts()) : the_post();
                        $pid = get_the_ID();
                        $post = get_post();
                        ?>                    

                           <?php the_content(); ?>

                        <?php endwhile; endif;?>         
                        
                    </div>
                </section>                                      
            </div>
<?php 
get_footer();