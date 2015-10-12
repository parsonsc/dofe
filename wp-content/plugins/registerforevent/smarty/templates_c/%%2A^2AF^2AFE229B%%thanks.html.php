<?php /* Smarty version 2.6.28, created on 2015-06-18 11:09:01
         compiled from thanks.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'print_r', 'thanks.html', 90, false),)), $this); ?>
<div class="thank_you">
    <header class="thanks_header">
        
        <p>good luck.</p>
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
                var share_image    =    '<?php  global $wpjg_generalSettings; echo (!jg_check_missing_http($wpjg_generalSettings['imageurl'])) ? get_home_url() : ''; ?>/FB_share_TellYourFriends.jpg';
                var share_url      =    '<?php  echo get_home_url('/')  ?>';  
                var share_capt     =    '';
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
        <?php echo print_r($this->_tpl_vars['post']); ?>

        <?php echo print_r($this->_tpl_vars['post_meta']); ?>

    </section>


</div>    