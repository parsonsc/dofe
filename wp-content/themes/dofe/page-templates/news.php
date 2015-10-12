<?php 
/**
 * Template Name: News Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content gallery_content" id="mainContent">       
                <section class="latest_news">
                    <div class="inner_content">
                        <header>
                            <h1>DofE Challenge </h1>
                            <ul>
                                <li>
                                    <a href="https://twitter.com/DofE?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" ><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_twitter.png" alt="Follow us on Twitter"></a>
                                </li>
                                <li>
                                    <a href="https://www.facebook.com/theDofE"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_facebook.png" alt="Like us on Facebook"></a>
                                </li>
                                <li>
                                    <a href="https://instagram.com/dofewales/"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/footer_insta.png" alt="View us on Instagram"></a>
                                </li>
                            </ul>
        <?php if (have_posts()) : while (have_posts()) : the_post();
        $pid = get_the_ID();
        $post = get_post();
        ?>                    
                           <?php the_content(); ?>

        <?php endwhile; endif;?>  
                        </header>

                        <div class="news_gallery news_grid">        
<?php
    $current_page = max( 1, get_query_var('page') );
    $total_pages = $wp_query->max_num_pages;
    $the_query = new WP_Query('category_name=Tweets&showposts=16&paged=' . $current_page);
    if ($the_query->have_posts()):

?>
<ul>
<?php
            while ($the_query->have_posts()) : $the_query->the_post(); $id = get_the_ID(); 
                $post = get_post();
                //print_R($post);
                //echo strpos($post->post_content, 'https://twitter.com');
                $hra = get_post_meta( $post->ID, 'status_href', true ); 
                $images = get_post_meta($post->ID, 'image' ); 
                $vids = get_post_meta($post->ID, 'other' ); 
                if (count($images) > 0):
?>
                <li class="photo item-<?php the_ID(); ?>">
                    <a href="<?php echo get_post_meta( $post->ID, 'status_href', true ); ?>">
                    <figure class="hover_effect">
                        <img src="<?php $images[0]['url']; ?>" alt="" />  
                        <span class="icon"></span>                                            
                    </figure>
                    </a>
                </li>
<?php                
                elseif (count($vids) > 0):
?>
                <li class="video item-<?php the_ID(); ?>">
                    <a href="<?php echo $vids[0]['url']; ?>">
                        <figure class="hover_effect">
                            <img src="<?php echo get_picture_from_yt($vids[0]['url']); ?>" alt=""/>  
                            <span class="icon"></span>                                            
                        </figure>
                    </a>
                </li>  
<?php                
                elseif (trim($hra) != ''):
?>                
                <li class="twitter">
                    <img src="images/news/twitter_box.png" alt="Twitter">
                    <article class="twitter_copy">
                        <a href="<?php echo get_post_meta( $post->ID, 'status_href', true ); ?>">
                            <header>                                           
                                <p>@<?php echo get_post_meta( $post->ID, 'twitter_username', true ); ?></p>
                            </header>
                            <p><?php echo get_post_meta( $post->ID, 'status', true ); ?></p>
                        </a>
                    </article>                                   
                </li>
<?php 
                else:
?>
                <li class="twitter">
                    <img src="images/news/twitter_box.png" alt="Twitter">
                    <article class="twitter_copy">
                        <a href="<?php echo $post->post_content;?>">
<?php
preg_match( '!http://twitter.com/([^/]+)/status/(\d+)!', $post->post_content, $matches );
$author  = $matches[1];
$tweetid = $matches[2];
$string = strip_tags(wp_oembed_get($post->post_content, $args ),'<p><a>');
$bare = strip_tags_content($string);
$tweet = str_replace($bare, '', $string);
$dom = new DomDocument();
$dom->loadHTML($tweet);

$last = '';
foreach ($dom->getElementsByTagName('a') as $node)
{
  $last = $node->nodeValue;
}
?>                               
                            <header>                                           
                                <p>@<?php echo $author; ?></p>
                            </header>                     
                            <p><?php echo str_replace($last, '',$tweet); ?></p>
                        </a>
                    </article>                                   
                </li>
<?php
                endif;
            endwhile;
?>
</ul>
<?php
        
	endif;
?>                        
                           <nav class="news_pagination">
<?php                           
the_posts_pagination( array(
	'mid_size'  => 2,
	'prev_text' => '<i class="fa fa-chevron-left"></i>',
	'next_text' => '<i class="fa fa-chevron-right"></i>',
) ); 
?>                          
                              
                           </nav> 
                        </div>
                        <div class="clear"></div>

                    <div class="tagline">
                            <h2>Lorem ipsum dolor sit amet</h2>
                            <a href="<?php echo get_permalink(9)?>" class="site_cta">Start your challenge</a>
                        </div>
                    </div>
                </section>                                      
            </div>
<?php 
get_footer();