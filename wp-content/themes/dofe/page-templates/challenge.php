<?php 
/**
 * Template Name: Choose a challenge Page
 *
 * @package DofE
 */

get_header();
?>

            <div class="main_content challenge_content" id="mainContent">       
                <section class="">
                    <div class="inner_content">
                        <header>
                           <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aliquam ultrices nibh, a viverra erat posuere ut. Fusce sed porttitor diam. Morbi ut molestie dolor, id porttitor elit. </p>
                        </header>

<?php
    $the_query = new WP_Query('category_name=Challenges&order=ASC&orderby=ID');
    if ($the_query->have_posts()):
        while ($the_query->have_posts()) : $the_query->the_post(); $id = get_the_ID(); 
            $post = get_post();
?>                           
                        
                        
                        <div class="<?php echo get_post_meta( get_the_ID(), 'anchor', true ); ?> challenge_block">
                            <?php echo get_the_post_thumbnail($id, null, array('class'=>'os-animation', 'data-os-animation'=>'fadeInUp', 'data-os-animation-delay'=>'0.25s'));?>
                            <article class="os-animation" data-os-animation="fadeInUp" data-os-animation-delay="0.50s">
                                <?php the_title('<h2>','</h2>'); ?>
                                <?php the_content(); ?>
                                <a href="<?php
                    $customlink = get_post_meta( get_the_ID(), 'popup', true );
                    if (strpos($customlink, 'http') !== false || strpos($customlink, 'mailto') !== false) echo $customlink;
                    else echo get_permalink($customlink); ?>" class="site_cta">start your challenge</a>
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