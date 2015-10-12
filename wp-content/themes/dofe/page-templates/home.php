<?php 
/**
 * Template Name: Home Page
 *
 * @package DofE
 */

get_header();
?>
            <div class="main_content hp_content" id="mainContent">                 
                <section class="challenges">
                    <div class="inner_content">
                        <header class="os-animation" data-os-animation="fadeIn" data-os-animation-delay="0s">
                            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                            <?php the_content();?>
                            <?php endwhile; endif;?>
                        </header>

<?php
    $the_query = new WP_Query('category_name=ChallengeIntros&showposts=3&order=ASC&orderby=ID');
    if ($the_query->have_posts()):
        while ($the_query->have_posts()) : $the_query->the_post(); $id = get_the_ID(); 
            $post = get_post();
?>                           
                        <article class="challenge_container">
                            <div class="challenge <?php echo strtolower(get_the_title());?> os-animation" data-os-animation="fadeInUp" data-os-animation-delay="0.25s">
                                <div class="challenge_diamond_bg os-animation" data-os-animation="fadeIn" data-os-animation-delay="1.75s"></div>
                                <a href="<?php
                    $customlink = get_post_meta( get_the_ID(), 'popup', true );
                    if (strpos($customlink, 'http') !== false || strpos($customlink, 'mailto') !== false) echo $customlink. get_post_meta( get_the_ID(), 'anchor', true );
                    else echo get_permalink($id); ?>">
                                    <div class="diamond">
                                        <div class="inner-diamond discover">
                                            <div class="inner vcenter-parent">
                                                <?php the_title('<h3>','</h3>');?>
                                            </div>
                                        </div>
                                    </div>
                                </a>                            
                            </div>
                            <footer class="os-animation" data-os-animation="fadeIn" data-os-animation-delay="1.5s">
                                <?php the_content(); ?>
                            </footer>                            
                        </article>
<?php
        endwhile; endif; 
?>  
                        
                        <div class="clear"></div>

                        <a href="<?php echo get_permalink(23)?>" class="site_cta os-animation" data-os-animation="fadeIn" data-os-animation-delay="1.5s">learn more about the challenge</a>
                    </div>
                </section>

                <section class="challenge_video os-animation" data-os-animation="fadeIn" data-os-animation-delay="0.75s">
                    <div class="inner_content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>                    
<?php
$cont = htmlspecialchars_decode(get_post_meta( get_the_ID(), 'second_column', true ));
$cont = apply_filters('the_content', $cont);
echo str_replace(']]>', ']]&gt;', $cont);
?>  
<?php endwhile; endif;?>                        
                    </div>
                </section>

                <section class="resource_blocks os-animation" data-os-animation="fadeIn" data-os-animation-delay="0.5s">
                    <div class="inner_content">
<?php
    $the_query = new WP_Query('category_name=Intros&showposts=4&order=ASC&orderby=ID');
    if ($the_query->have_posts()):

?>
<ul>
<?php
            while ($the_query->have_posts()) : $the_query->the_post(); $id = get_the_ID(); 
?>                    
                <li>
                    <?php echo get_the_post_thumbnail($id, null);?>
                    <?php the_title('<span>','</span>');?>
                    <article>
                    <?php echo wpautop($post->post_content);?>
                    <a href="<?php
                    $customlink = get_post_meta( get_the_ID(), 'popup', true );
                    if (strpos($customlink, 'http') !== false || strpos($customlink, 'mailto') !== false) echo $customlink;
                    else echo get_permalink($customlink); ?>" class="site_cta"><?php echo get_post_meta( get_the_ID(), 'anchor', true ); ?></a>
                    </article>
                </li>                       
<?php
            endwhile; 
?>                            
                        </ul>
<?php
            endif; 
?>                            
                    </div>
                </section>

                <section class="latest_news os-animation" data-os-animation="fadeIn" data-os-animation-delay="0.5s">
                    <div class="inner_content">
                        <header class="news_header">
                            <h2>Latest news</h2>
                        </header>

                        <div class="news_grid">
<?php
    $the_query = new WP_Query('category_name=Tweets&showposts=4');
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

                        </div>
                        <div class="clear"></div>
                        <?php
                        wp_reset_postdata(); 
                        ?>
                        <a href="<?php echo get_permalink(25); ?>" class="site_cta">See all the latest news</a>
                    </div>
                </section>                   
            </div>

<?php 
get_footer();