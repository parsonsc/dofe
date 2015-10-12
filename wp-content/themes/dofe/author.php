<?php 
/**
 * The template for displaying Author Archive pages.
 *
 * @package GoodAgency
 */
error_reporting(0);
define('PER_PAGE_DEFAULT', 8);
get_header(); 
$author_id = 0;
if (is_author()) {
	global $wp_query;
	$author_id = $wp_query->query_vars['author']; 

}
	
if(get_the_post_thumbnail(8253, null)){
?>
        <section class="hero_banner">
          <div class="inner_content">          
            <?php echo get_the_post_thumbnail(23, null, array('class' => "background",'alt' => trim( sprintf( __( 'Author Archive: %s', 'coretheme' ),  get_the_author_meta( 'first_name',$author_id  ) ) ),'title' => ""));?>
          </div>
        </section>
<?php
}
?>
	<div class="clear"></div>
    <div class="mainContent" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/WebPageElement">
       
        <section class="thinking_author_section">
            <div class="inner_content">
                <header>
<?php 
	//$postAuthor = $post->post_author; 

    $user = new WP_User( $author_id );
	if (!empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] != 'administrator') : 
?>
    <div class="author-image">
    <?php 
        $avatar = get_author_image_url($author_id); // Replace 123 with the id of the author you want to use
		$html = '<img src="'.$avatar.'" alt="A Picture of '.get_the_author_meta('display_name', $author_id).'" class="author"/>';
		echo $html;
	?>
    </div>
    <div class="author-job">
			<h2><?php the_author_meta('display_name',$author_id); ?></h2>			
            <p><?php the_author_meta('jobtitle',$author_id); ?></p>
    </div>
    <div class="author-about">
			<h3>About the author</h3>			
			<?php the_author_meta('description',$author_id); ?>
    </div>
<?php
	endif;
?>	                
                
                </header>
                <div class="clear hr"></div>
                <div class="news_grid quadmasonry" id="NewsContainer">   
                    <?php 
                    $current_page = max( 1, get_query_var('paged') );
                    $total_pages = $wp_query->max_num_pages; ?>
                    <?php $the_query = new WP_Query('post_type=thinksa&author='. $author_id .'&showposts=8&paged=' . $current_page); ?>
                    <?php while ($the_query->have_posts()) : $the_query->the_post(); $id = get_the_ID(); ?>

                    <div class="item item-<?php the_ID(); ?>">
                        <a href="<?php echo get_permalink($id);?>">
                            <?php 
                            $custom = get_post_custom($id);
                            $clienttitle = isset($custom["clienttitle"][0])? $custom["clienttitle"][0] : '' ;
                            $clientimage = isset($custom["clientimage"][0])? $custom["clientimage"][0] : '' ;
                            if ($clientimage !== '') :
							   echo '<img src="'. $clientimage .'" alt="" width="100%" />';
						    else:
							    echo get_the_post_thumbnail($id, null);
							endif; ?>                            
                            <div class="news_text"> 
                                <?php echo  ($clienttitle !='') ? '<p>'.$clienttitle.'</p>' : get_the_title('<p>','</p>'); ?>
                            </div>
                        </a>
                    </div>

                    <?php endwhile; ?>                    
                    
                    <?php 
                    next_posts_link( 'Older Entries', $total_pages );
                    wp_reset_postdata(); 
                    
                    ?>
                    
                </div>
                <a href="<?php echo get_permalink(8253); ?>" class="back_cta">Back to all GOOD thinking</a>
            </div>
        </section>                
    </div>            
<?php get_footer(); ?>