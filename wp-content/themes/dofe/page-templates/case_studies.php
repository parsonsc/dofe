<?php 
/**
 * Template Name: Case Studies Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content case_sudies_content" id="mainContent">       
                <section class="">
                    <div class="inner_content">
        <?php if (have_posts()) : while (have_posts()) : the_post();?>                    
                        <header>
                           <?php the_content(); ?>
                        </header>
        <?php endwhile; endif;?>    
<?php
	$celebs = new WP_Query('category_name=CaseStudies&showposts=4&order=ASC&orderby=ID');
    //echo $clients->request;
	if ($celebs->have_posts()):
        $x = 0;
		while ($celebs->have_posts()):         
            $x++;
            $celebs->the_post();
?>  
                        <div class="case_study case_<?php echo str_pad($x, 2, '0', STR_PAD_LEFT);?>">
                            <?php echo get_the_post_thumbnail(get_the_ID(), null);?>
                            <article>
                                <?php the_title('<h2>','</h2>'); ?>
                                <?php the_content(); ?>
                            </article>
                        </div>  
<?php
		endwhile;
	endif;
?>       
                    </div>
                </section>                                      
            </div>
<?php 
get_footer();