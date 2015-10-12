<?php 
/**
 * Template Name: Contact Page
 *
 * @package GoodAgency
 */
get_header(); 
if(get_the_post_thumbnail(get_the_ID(), null)){
?>
        <section class="hero_banner">
          <div class="inner_content">          
            <?php echo get_the_post_thumbnail(get_the_ID(), null, array('class' => "background",'alt' => "",'title' => ""));?>
          </div>
        </section>
<?php
}
?>   
        <div class="clear"></div>
    <div class="mainContent" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/WebPageElement">
        
        <div class="contact_content">
         <div class="inner_content">

          <!-- Address -->
          <section class="address" itemprop="sourceOrganization" itemscope="itemscope" itemtype="http://schema.org/LocalBusiness">
            <div class="address_text address_panel">
              <h1>Contact us</h1>

              <p>RING: <a href="tel:020 7738 1900" itemprop="telephone">020 7738 1900</a></p>

              <p>PING: <a href="mailto:hello@thegoodagency.co.uk" target="_blank" itemprop="email">hello@thegoodagency.co.uk</a></p>

              <p ><a href="https://goo.gl/maps/A2UXE" target="_blank" itemprop="name">The Good Agency,<br />
              <span itemprop="address" itemscope="itemscope" itemtype="http://schema.org/PostalAddress">
              <span itemprop="streetAddress">8 Boundary Row</span>,<br />
              <span itemprop="addressLocality">London,<br />
              SE1 8HP</span></a></p>

              <p>Press?<br />
              <a href="mailto:HelloBrogan@GOODagency.co.uk" target="_blank">HelloBrogan@GOODagency.co.uk</a></p>
            </div>
            <div class="address_panel map">
              <a href="#" target="_blank" class="google_map show_mobile">
                Google map
              </a>
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9934.104433286622!2d-0.11227956374184082!3d51.50356327508436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487604a52cd039d5%3A0x2ee5fa79811fed02!2sThe+Good+Agency!5e0!3m2!1sen!2suk!4v1416411026489" width="100%" height="450" frameborder="0" style="border:0"></iframe>
            </div>
          </section>
          <div class="clear"></div>
          <!-- End address -->

          <!-- new business section -->
          <section class="new_business">
            <h2>Unleash the GOOD</h2>
            <p>If you’d like to find out more about what we do, or come in for a chat, contact Nicola Lapsley <a href="mailto:hellonicola@GOODagency.co.uk" target="_blank">hellonicola@GOODagency.co.uk</a></p>
          </section>
          <!-- End new business -->

           <!-- join us section -->
          <section class="join_us">
            <h2>Join us</h2>
            <p> We’re always on the lookout for potential GOOD people. If you’re good at what you do and share our values, send your CV or portfolio to our People Manager Jo Hankin: <a href="mailto:hellojo@GOODagency.co.uk?subject=Hello Jo,">HelloJo@GOODagency.co.uk</a></p>
            <h3>Current Roles</h3>
<?php
	$jobBits = new WP_Query();
    $jobBits->query(array('post_type' => 'job', 'post_status'=>'publish'));
	if ($jobBits->have_posts()):
?>
            <div class="current_roles">
              <ul>
<?php
		while ($jobBits->have_posts()): 
			$jobBits->the_post();
			$block_link = get_post_meta($post->ID, 'block_link', true);
?>
		<li>
            <a href="<?php echo the_permalink();?>"
                <header class="sticky"><?php the_title(); ?></header>
                <p class="role_desc"><?php the_excerpt_max_charlength(94); ?></p>
            </a>
            <a href="<?php echo the_permalink();?>" class="read_more_cta">Read more and apply</a>
		</li>
<?php
		endwhile;
?>
            </ul>
          </div>          
<?php
	endif;
?>
    </section>      

          <div class="clear"></div>
          <!-- End join us -->

          <!-- Email sign up -->
          
          
          <?php echo do_shortcode('[cm_ajax_subscribe id=0]'); ?>
                    
        </div>        
      </div>              
    </div>
   </div>                         
<?php get_footer(); ?>