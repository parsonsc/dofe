<?php /* Smarty version 2.6.28, created on 2015-04-01 15:37:35
         compiled from thanks.html */ ?>
<div class="thank_you">
    <header class="thanks_header">
        <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/img/thanks_header.png" alt="Thanks for taking the h2only challenge" class="thanks_hero">
        <h2>Get ready for 10 days. 100% water.</h2>
        <p>It’s going to be a challenge. But we’ll be with you all the way, helping you keep your head above water.</p>
        <p>When the cravings get too much, just remember that you’re helping the RNLI save lives at sea.</p>
        <p>So stay strong, and good luck.</p>
        <section class="social_bar">
            <div class="social_underline"></div>
            <h4>Tell your friends and colleagues</h4>
            <?php if ($this->_tpl_vars['settings']['fbappid'] != ''): ?>
            <script type="application/javascript">
              window.fbAsyncInit = function() {
                // init the FB JS SDK
                FB.init({
                  appId      : '<?php echo $this->_tpl_vars['settings']['fbappid']; ?>
',                            
                  status     : true,                                 
                  xfbml      : true                                  
                });
              };
              // Load the SDK asynchronously
              (function(d, s, id){
                 var js, fjs = d.getElementsByTagName(s)[0];
                 if (d.getElementById(id)) {return;}
                 js = d.createElement(s); js.id = id;
                 js.src = "//connect.facebook.net/en_US/all.js";
                 fjs.parentNode.insertBefore(js, fjs);
               }(document, 'script', 'facebook-jssdk'));
               
            function FBShareOp(){
                var product_name   =    'Go on, just one sip';
                var description    =    "I’m denying the drinks I love for 10 days. Join me and say no to tea, coffee, wine, beer and fizzy drinks to help the RNLI save lives at sea. Let’s team up and take the challenge together from 2-12 June";
                var share_image    =    '<?php  global $wpjg_generalSettings; echo (!jg_check_missing_http($wpjg_generalSettings['imageurl'])) ? get_home_url() : ''; ?><?php echo $this->_tpl_vars['settings']['imageurl']; ?>
';
                var share_url      =    '<?php  echo get_home_url('/')  ?>';  
                var share_capt     =    'H2Only';
                FB.ui({
                    method: 'feed',
                    name: product_name,
                    link: share_url,
                    picture: share_image,
                    caption: share_capt,
                    description: description
                }, function(response) {
                    if(response && response.post_id){}
                    else{}
                });
              }  
              function FBSendAOp(link){  
              FB.ui({  
                method: 'send',
                link: link,
                });  
              }
            </script>
            <?php endif; ?>                   
            <nav class="spread-social">
                <ul>
                    <li class="twitter_box">
                        <a data-provider="twitter"  rel="nofollow" title="Share on Twitter" href="http://twitter.com/share?url=<?php echo $this->_tpl_vars['homepage']; ?>
&amp;text=<?php  echo urlencode("I’m denying the drinks I love for 10 days. Come aboard with me and go #H2Only to help #RNLI save lives at sea "); ?>">
                            <i class="fa fa-twitter"></i><p>Tweet</p>
                        </a>
                    </li>
                    <li class="fb_box">
                        <a data-provider="facebook"  rel="nofollow" title="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $this->_tpl_vars['homepage']; ?>
" <?php if ($this->_tpl_vars['settings']['fbappid'] != ''): ?>onclick="FBShareOp(); return false;"<?php endif; ?>>   
                            <i class="fa fa-facebook"></i> <p>Share</p>
                        </a>
                    </li>
                    <li class="web_box">
                        <?php 
                            $user = $this->get_template_vars('user');
                            $link = get_home_url('/');
                            $subj = "Can you deny the drinks you love for 10 days?";
                            $body = "I’m going H2Only for 10 days from 2-12 June at 5pm, to help the RNLI’s brave lifeboat crew and lifeguards to save lives at sea.
That’s 10 days without tea, coffee, wine, beer and fizzy drinks. It’s going to be a challenge, but we’ll be stronger as a team. Will you sign up now and join me? 
{$link}
Just be sure to keep the evening of 12 June free, to celebrate being reunited with our favourite drinks.";                        
                            
                            
                            $subj = str_replace(" ", "%20", $subj);
                            $body = str_replace(array("\n", "\r"), '%0D%0A%0D%0A', str_replace(" ", "%20",$body));
                         ?>
                        <a data-provider="email" href="mailto: ?subject=<?php  echo $subj; ?>&body=<?php  echo $body; ?>" rel="nofollow">    
                            <i class="fa fa-envelope-o"></i> <p>Email</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </section>
    </header> 
    <div class="clear"></div>

    <section class="challenge_others">
        <div class="inner_content">
            <div class="block challenge_block">
                <div class="challenge_copy"> 
                    <h3>CHALLENGE YOUR CREW</h3>
                    <p>They’re the friends we all know and love; the ones you gossip with around the kettle at work, your pub sidekicks, or your coffee morning mums. Because if you’re going H2Only, it’s only fair you take them with you. You’ll be stronger as a crew – and you’ll raise even more money.</p>
                    <a class="site_cta" data-provider="facebook"  rel="nofollow" title="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $this->_tpl_vars['homepage']; ?>
" <?php if ($this->_tpl_vars['settings']['fbappid'] != ''): ?>onclick="FBShareOp(); return false;"<?php endif; ?>>CHALLENGE THEM</a>                
                </div>               
            </div>
            <div class="block challenge_block challenge_img">
                <!-- <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/img/challege_others.jpg" alt="Challenge others" width="100%"> -->
            </div>
        </div>
    </section>

    <section class="select_challenge">
        <div class="inner_content">
            <h3>Select a challenge message to share on facebook</h3>
            <ul>
                <li>
                    <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/img/challenege_01.jpg" alt="">
                        <article>
                            <h3>Ahoy there, party animals!</h3>
                            <p>I’m packing away the cocktail shaker from 2-12 June. And I challenge you to join me and deny our favourite drinks for 10 days. Sign up and let’s go H2Only together to help the RNLI to save lives at sea. </p>
                            <a href="http://www.facebook.com/dialog/send?app_id=<?php echo $this->_tpl_vars['settings']['fbappid']; ?>
&link=<?php  echo get_home_url('/').'?party';  ?>&redirect_uri=<?php  echo get_permalink();  ?>" onclick="FBSendAOp('<?php  echo get_home_url('/').'?party';  ?>'); return false;" class="site_cta">Challenge them</a>
                        </article>
                    </img>
                </li>
                <li>
                    <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/img/challenege_02.jpg" alt="">
                        <article>
                            <h3>Ahoy there, beer buddies!</h3>
                            <p>I’m calling time at the bar from 2-12 June. And I challenge you to avoid the pub and go H2Only with me. That’s no beer, wine, tea, coffee or fizzy drinks for 10 days. Sign up and let’s go to help the RNLI save lives at sea.</p>
                            <a href="http://www.facebook.com/dialog/send?app_id=<?php echo $this->_tpl_vars['settings']['fbappid']; ?>
&link=<?php  echo get_home_url('/').'?beer';  ?>&redirect_uri=<?php  echo get_permalink();  ?>" onclick="FBSendAOp('<?php  echo get_home_url('/').'?beer';  ?>'); return false;" class="site_cta">Challenge them</a>
                        </article>
                    </img>
                </li>
                <li>
                    <img src="<?php echo $this->_tpl_vars['templateurl']; ?>
/img/challenege_03.jpg" alt="">
                        <article>
                            <h3>Ahoy there, tea-breakers!</h3>
                            <p>I’m switching off the kettle and the coffee machine from 2-12 June. And I challenge you to swap our tea breaks for water cooler moments, as we go H2Only together. That’s no tea, coffee, fizzy drinks, beer or wine to raise money and help the RNLI save lives at sea.</p>
                            <a href="http://www.facebook.com/dialog/send?app_id=<?php echo $this->_tpl_vars['settings']['fbappid']; ?>
&link=<?php  echo get_home_url('/').'?tea';  ?>&redirect_uri=<?php  echo get_permalink();  ?>" onclick="FBSendAOp('<?php  echo get_home_url('/').'?tea';  ?>'); return false;" class="site_cta">Challenge them</a>
                        </article>
                    </img>
                </li>
            </ul>
        </div>
    </section>

</div>    