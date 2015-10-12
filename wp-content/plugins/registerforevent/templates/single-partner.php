<?php get_header(); ?>

        <div id="container">
            <div id="content" role="main">
    
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php 
                $post_id = get_the_ID();
                $transient_name = 'partners_' . $post_id;
                $settings = get_transient( $transient_name );
                if( !$settings ) {
                    $settings = get_post_meta( $post_id );
                    //error_log(print_R($settings, true));  
                    set_transient( $transient_name, $settings, 86400 );
                }                
                extract ( $settings ); 
                ?>
                <div class="post download">
                    <h1 class="entry-title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h1>

                    <div class="clear"></div>
                    
                    <div class="entry-content">
                        <?php echo $custom_header; ?><?php echo $organizer_logo; ?>
                        <?php the_content(); ?>
                        <?php echo $venue; ?>
                        <?php echo $address; ?>
                        <?php echo $city; ?>
                        <?php echo $region; ?>
                        <?php echo $postal_code; ?>
                        <?php echo $country_code; ?>
                        <?php echo $country_name; ?>
                        <?php echo $latitude; ?>
                        <?php echo $longitude; ?>
                        
                        <?php echo $start_date; ?>
                        <?php echo $end_date; ?>
                        <?php echo $capacity; ?>
                        <?php echo $organizer_shortname; ?>
                        
                        
                        <?php echo $custom_footer; ?>
                        
                    </div>
                    
                    <div style="width:100%; text-align:left;" ><iframe src="http://www.eventbrite.com/tickets-external?eid=<?php echo $event_id;?>&ref=etckt" frameborder="0" height="650" width="100%" vspace="0" hspace="0" marginheight="5" marginwidth="5" scrolling="auto" allowtransparency="true"></iframe><div style="font-family:Helvetica, Arial; font-size:10px; padding:5px 0 5px; margin:2px; width:100%; text-align:left;" ><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/r/etckt" >Online Ticketing</a><span style="color:#ddd;" > for </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=etckt" ><?php the_title(); ?></a><span style="color:#ddd;" > powered by </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com?ref=etckt" >Eventbrite</a></div></div>

                    <div style="width:100%; text-align:left;" ><iframe src="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=eweb" frameborder="0" height="1000" width="100%" vspace="0" hspace="0" marginheight="5" marginwidth="5" scrolling="auto" allowtransparency="true"></iframe><div style="font-family:Helvetica, Arial; font-size:10px; padding:5px 0 5px; margin:2px; width:100%; text-align:left;" ><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/r/eweb" >Online Ticketing</a><span style="color:#ddd;" > for </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=eweb" ><?php the_title(); ?></a><span style="color:#ddd;" > powered by </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com?ref=eweb" >Eventbrite</a></div></div>

    
                    <div style="width:195px; text-align:center;" ><iframe src="http://www.eventbrite.com/calendar-widget?eid=<?php echo $event_id;?>" frameborder="0" height="382" width="195" marginheight="0" marginwidth="0" scrolling="no" allowtransparency="true"></iframe><div style="font-family:Helvetica, Arial; font-size:10px; padding:5px 0 5px; margin:2px; width:195px; text-align:center;" ><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/r/ecal">Online event registration</a><span style="color:#ddd;" > powered by </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com?ref=ecal" >Eventbrite</a></div></div>
                    
                    <div style="width:195px; text-align:center;" ><iframe src="http://www.eventbrite.com/countdown-widget?eid=<?php echo $event_id;?>" frameborder="0" height="479" width="195" marginheight="0" marginwidth="0" scrolling="no" allowtransparency="true"></iframe><div style="font-family:Helvetica, Arial; font-size:10px; padding:5px 0 5px; margin:2px; width:195px; text-align:center;" ><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/r/ecount" >Online event registration</a><span style="color:#ddd;" > for </span><a style="color:#ddd; text-decoration:none;" target="_blank" href="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=ecount" ><?php the_title(); ?></a></div></div>
    
                    <a href="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=ebtn" target="_blank"><img border="0" src="http://www.eventbrite.com/custombutton?eid=<?php echo $event_id;?>" alt="Register for <?php the_title(); ?> on Eventbrite" /></a>


                    <a href="http://www.eventbrite.com/event/<?php echo $event_id;?>?ref=elink" target="_blank"><?php echo ($text) ? $text : get_the_title(); ?></a>
                    <div class="event-tickets">
                        <iframe src="http://www.eventbrite.com/tickets-external?eid=<?php echo $event_id ?>" height="306" width="100%" ></iframe>
                    </div>
                </div>
            <?php endwhile; endif; ?>

            </div><!-- #content -->
        </div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
