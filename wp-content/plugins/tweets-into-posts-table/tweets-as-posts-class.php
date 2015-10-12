<?php    

use \Abraham\TwitterOAuth\TwitterOAuth;           

    /**
     * Tweets As Posts
     *
     * PHP5 Class to query the Twitter search API for
     * tweets with given hashtags from given Twitter users
	 *
	 * Extends Twitter News Feed plugin by Keir Whitaker
     *
	 * @author Chandesh Parekh
     **/
    class TweetsAsPosts
    {
        

        
        /**
         * TweetsAsPosts options
         *
         * @var array
         **/
        var $options;
    
        /**
         * Twitter search query string
         *
         * @var string
         **/
        var $search_query;
    
        /**
         * SimplePie feed
         *
         * @var SimplePie object
         **/
        var $feed;
    
        /**
         * WordPress Database object
         *
         * @var wpdb object
         **/
        var $wpdb;
    
        function __construct($wpdb)
        {
            // Check for the existance of SimplePie
            //if (!class_exists("SimplePie")) {
            //	require_once("simplepie/simplepie.inc");
            //};
            require WP_PLUGIN_DIR.'/'.dirname(plugin_basename( __FILE__ ))."/twitteroauth/autoload.php";

            
            // Set up the internal class variables
            $this->wpdb = $wpdb;    
            $this->get_options();
            $this->get_search_query();       
        }
    
        /**
         * Return an array of consumed Tweets from the RSS feed
         *
         * @access public
         * @return array
         **/
        public function get_news_feed($add_news_to_db = TRUE) {
                    
            $toa = new TwitterOAuth(
                $this->options["consumer_key"], $this->options["consumer_sec"], 
                $this->options["access_token"], $this->options["access_secret"]);
                
            $tweets = $toa->get('search/tweets', array('q'=>$this->search_query, 'count' => 20, "exclude_replies" => true));
            //error_log(print_R($tweets,true));
            $post = array();
        
            // Array to hold all the tweet info for returning as an array
            $retval = array();
        
            // Set up two counters (1 for use in the return array and 1 for counting the number of inserted posts if applicable)
            $n = 0;
            $i = 0;
         
            // Array to hold the stored hashtags 
            $hashes = explode(',', $this->options["hashtags"]);
        
            foreach ($tweets->statuses as $result) {
                error_log($result->user->screen_name . ": " . print_R($result, true));

                // Get the Twitter status id from the status href
                $twitter_status_id = $result->id;

                // Check to see if the username is in the user profile meta data
                $post["user_id"] = (int)$this->options["user"];
                $user_id = $this->map_twitter_to_user($result->user->screen_name);
                if(!$user_id==NULL) {
                    $post["user_id"] = (int)$user_id;
                };

                // Add individual Tweet data to array
                $post["date"]                   = date("Y-m-d H:i:s",strtotime($result->created_at));
                $post["link"]                   = $this->create_tweet_link($result->user->screen_name, $twitter_status_id);
                $post["id"]                     = $twitter_status_id;
                $post["description"]            = $result->text;
                $post["description_filtered"]   = $this->strip_hashes($result->text, $hashes);
                $post["twitter_username"]       = $result->user->screen_name;
                $post["twitter_username_link"]  = $this->create_twitter_link($result->user->screen_name);
                foreach ($result->entities->media as $media){                    
                    $post["media"][] = array(
                        'type' => 'image',
                        'url' => $media->media_url
                    );
                }
                /*
                if (preg_match('~
                        # Match non-linked youtube URL in the wild. (Rev:20130823)
                        https?://         # Required scheme. Either http or https.
                        (?:[0-9A-Z-]+\.)? # Optional subdomain.
                        (?:               # Group host alternatives.
                          youtu\.be/      # Either youtu.be,
                        | youtube         # or youtube.com or
                          (?:-nocookie)?  # youtube-nocookie.com
                          \.com           # followed by
                          \S*             # Allow anything up to VIDEO_ID,
                          [^\w\s-]        # but char before ID is non-ID char.
                        )                 # End host alternatives.
                        ([\w-]{11})       # $1: VIDEO_ID is exactly 11 chars.
                        (?=[^\w-]|$)      # Assert next char is non-ID or EOS.
                        (?!               # Assert URL is not pre-linked.
                          [?=&+%\w.-]*    # Allow URL (query) remainder.
                          (?:             # Group pre-linked alternatives.
                            [\'"][^<>]*>  # Either inside a start tag,
                          | </a>          # or inside <a> element text contents.
                          )               # End recognized pre-linked alts.
                        )                 # End negative lookahead assertion.
                        [?=&+%\w.-]*      # Consume any URL (query) remainder.
                        ~ix', $result->text, $ytmat)){
                    //error_log(print_R($ytmat, true));
                    $post["media"][] = array(
                        'type' => 'video',
                        'url' => $media->media_url
                    );                            
                }
                */
                // Add the new post to the db?
                if($add_news_to_db) {
                    if($this->add_item_as_post($post)) {
                        $i++;
                    };  
                };
            
                // Add the Tweet to the return array    
                $retval[$n] = $post;
                $n++;
            };
        
            // Return correct values depending on the $add_news_to_db boolean
            if($add_news_to_db) {
                return $i;
            } else {
                return $retval;
            };
        }
    
        /**
         * Strip hash tags (#'s) from the Tweet string
         *
         * @access private
         * @return string
         **/
        private function strip_hashes($tweet, $hashes = array()) {
            if(is_array($hashes)) {
                foreach ($hashes as $hash) {
                    $tweet = str_replace("<a href=\"http://search.twitter.com/search?q=%23".$hash."\"><b>#".$hash."</b></a>", "", $tweet);
                };    
            };
            return $tweet;
        }
        private function create_tweet_link($twitter_username, $tweet){
            return "https://twitter.com/".$twitter_username."/status/".$tweet;
        }
        /**
         * Create a href to the Twitter users Twitter homepage
         *
         * @access private
         * @return string
         **/     
        private function create_twitter_link($twitter_username) {
            return "<a href=\"https://twitter.com/".$twitter_username."\">@".$twitter_username."</a>";
        }
    
         /**
         * Adds a Tweet to a given WP post category
         * Additionally adds post meta data to the post
         *
         * @access private
         * @return boolean
         **/  
        private function add_item_as_post($item = array()) {
        
            // Check to see if the post already exists

           //truncate first
			$charsLength = 70;
			$new_post_title = $item["description_filtered"];
			//strip out tags
			$new_post_title = strip_tags($new_post_title);
			if (strlen($new_post_title) > $charsLength) {
				$new_post_title = substr($new_post_title, 0, $charsLength);
				$new_post_slug = sanitize_title_with_dashes($new_post_title);
				$new_post_slug = $item["id"] . '-' . $new_post_slug;
				$new_post_title = $new_post_title . "...";
			}
			 
			$sql = "SELECT meta_id FROM ".$this->wpdb->postmeta." WHERE meta_value = '".$item['id']."' AND meta_key = 'status_id'";
        
            if($this->wpdb->get_var($sql) == NULL) {

                // It's a new entry so add it to the posts table
                $new_post = array();
                $new_post["post_date"]       = $item["date"]; 
                $new_post["post_title"]      = $new_post_title;
				$new_post["post_name"]       = $new_post_slug;
                //$new_post["post_excerpt"]    = $item["description_filtered"];
                $new_post["post_content"]    = $item["description"];
                $new_post["post_status"]     = "publish";
                $new_post["post_author"]     = (int)($item["user_id"]);
                $new_post["post_category"]   = array($this->options["category"]);
                if (isset($item["media"])){
                    foreach ($item["media"] as $file){
                        if ($file['type'] =='image') add_post_meta($post->ID, 'image', $file['url']);
                        else  add_post_meta($post->ID, 'other', $file['url']);
                    }
                }
                // Insert the post into the database and store the id
                $post_id = wp_insert_post($new_post);
            
                // Add custom fields to the post
                add_post_meta($post_id, $meta_key = "status_id", $meta_value=$item["id"], $unique=TRUE);
                add_post_meta($post_id, $meta_key = "status", $meta_value=$item["description"], $unique=TRUE);
                add_post_meta($post_id, $meta_key = "status_href", $meta_value=$item["link"], $unique=TRUE);
                add_post_meta($post_id, $meta_key = "twitter_username", $meta_value=$item["twitter_username"], $unique=TRUE); 
                add_post_meta($post_id, $meta_key = "twitter_username_link", $meta_value=$item["twitter_username_link"], $unique=TRUE);  
            
                return TRUE;
            } else {
                return FALSE;
            };  
        }
    
        /**
         * Checks to see if a given Twitter user id exists in
         * the Jabber field of any of the user profiles
         *
         * @access private
         * @return int || null
         **/
        private function map_twitter_to_user($twitter_username) {
            $sql = "SELECT user_id FROM ".$this->wpdb->usermeta." WHERE meta_key='jabber' AND meta_value='".$twitter_username."'";
            $user_id = $this->wpdb->get_var($sql);
            return $user_id;
        }
    
        /**
         * Get the Tweets As Posts (prefix tap_) options
         * from wp_options table using get_option($key) WP function
         *
         * @access private
         * @return void
         **/
        private function get_options() {
       
            $options = array();
       
            $options["hashtags"]        = get_option("tap_hashtags");
           	$options["usernames"]       = get_option("tap_usernames");
           	$options["exceptions"]      = get_option("tap_exceptions");
           	$options["category"]        = get_option("tap_category");
           	$options["user"]            = get_option("tap_user");
            $options["consumer_key"] =	get_option("tap_consumer_key");
            $options["consumer_sec"] = get_option("tap_consumer_sec");
            $options["access_token"] = get_option("tap_access_token");
            $options["access_secret"] = get_option("tap_access_secret");	               
           	$options["add_news_to_db"]  = get_option("tap_add_news_to_db");
		
    		$this->options = $options;
        }
    
        /**
         * Builds a Twitter API search string
         *
         * @access private
         * @return string
         **/     
        public function get_search_query() {
        
            //$search_url = "http://search.twitter.com/search.rss?rpp=50&q=";
            $hashtag_q = explode(",", $this->options['hashtags']);
            $n = 0;
        
            foreach($hashtag_q as $hashtag) {
                //$retval .= urlencode("#".$hashtag);
                $retval .= "#".$hashtag;
                if(!($n == count($hashtag_q)-1)) {
                    $retval .= " OR ";   
                }; 
                $n ++;
            };

            $usernames_q = explode(",", $this->options["usernames"]);
            $n = 0;
            foreach($usernames_q as $username) {
                if(!($n == 0)) {
                    $retval .= " OR from:"; 
                } else {
                    $retval .= " from:"; 
                };   
                $n ++; 
                //$retval .= urlencode($username);
                $retval .= $username;
            };
        
            $this->search_query = $retval;
        }       
    }
    // END class
?>