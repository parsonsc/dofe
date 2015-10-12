<?php
    
    /*
    Plugin Name: Tweets into Posts table
    */

    // Tweets As Posts Version
    $tap_version = "10.1";
    
    // Require the PHP5 class
    require_once('tweets-as-posts-class.php');
    
    /**
     * Wrapper function to be called from a WP template
     * Get the news feed as numerically indexed array
     *
     * Returns an numerically indexed array to theme template in the following format
     *
     * [user_id]                => (string e.g 1 - needs to be cast to int if required)
     * [date]                   => (Y-m-d H:i:s)
     * [link]                   => (string e.g http://twitter.com/chandeshparekh/status/283784830708)
     * [id]                     => (string e.g 283784830708 - the actual status id of the tweet)
     * [description]            => (string - The unfiltered tweet which will include the #tag(s))
     * [description_filtered]   => (string - The filtered tweet)
     * [twitter_username_link]  => (String - href of the Twitter username)
     *
     * @return Numerically indexed array containing collected tweets
     **/
    function tap_get_news_as_array() {
        return tap_get_news($add_news_to_db = FALSE);
    }
    
    /**
     * Get the news feed
     *
     * Returns an numerically indexed array either to 
     * tap_get_news_as_array() or an integer value with 
     * number of posts added to the database for use in 
     * the admin screen
     *
     * @return Numerically indexed array containing collected tweets
     **/
    function tap_get_news($add_news_to_db = TRUE) {
    
        // Globalise the WP database class
        global $wpdb;
        
        // Create new TweetsAsPosts object
        $tap = new TweetsAsPosts($wpdb);
        
        // Return the Tweets to template or admin screen
        $retval = $tap->get_news_feed($add_news_to_db);
        
        // Update the log
        $comment = "Tweets As Posts ran at: ".date("l jS \of F Y - H:i:s", time());
        $sql = "INSERT INTO ".$wpdb->prefix."tap_log"." (time, comment) VALUES ('".time()."','".$wpdb->escape($comment)."')";
        $wpdb->query($sql);
        
        return $retval;     
    }

    /**
     * Add the Tweets As Posts admin screen to the WP settings menu
     **/
    add_action('admin_menu', 'tap_admin_actions');
     
    /**
     * Add the Tweets As Posts link to 
     *
     * Returns an numerically indexed array either to 
     * a theme template or the admin screen
     *
     * @return  Numerically indexed array containing collected tweets
     **/
    function tap_admin_actions() {
        add_options_page('Tweets As Posts', 'Tweets As Posts', 'manage_options', 'tweets-as-posts-admin', 'tap_admin');	    
	};
	
	/**
     * Callback function to include the Tweets As Posts Admin panel
     *
     * @return  None
     **/
	function tap_admin() {
	    include('tweets-as-posts-admin.php');
	} 
	
	/**
     * On plugin activation add the sudo cron job
     **/
    register_activation_hook(__FILE__, 'tap_activation');
    add_action('tap_hourly_update_action', 'tap_hourly_update');
    
    /**
     * Schedule the hourly update event and install the log table
     *
     * @return  None
     **/
    function tap_activation() {
		wp_schedule_event(current_time( 'timestamp' ), 'hourly', 'tap_hourly_update_action'); 
    	tap_db_install($tap_version);
    }

    /**
     * Callback function for scheduled event
     *
     * @return  None
     **/
    function tap_hourly_update() {
        if(get_option("tap_add_news_to_db")) {
    	    tap_get_news($add_news_to_db = TRUE);
    	};
    }
    
    /**
     * On deactivation of plugin clear the scheduled event
     **/
    register_deactivation_hook(__FILE__, 'tap_deactivation');

    /**
     * Callback function for plugin deactivation
     *
     * @return  None
     **/
    function tap_deactivation() {
    	wp_clear_scheduled_hook('tap_hourly_update_action');
    }

    /**
     * Adds the tap_log table to the database
     *
     * @return  None
     **/
    function tap_db_install($tap_version) {
        global $wpdb;
        global $tap_db_version;
        
        $table_name = $wpdb->prefix."tap_log";
        if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

            $sql =  "CREATE TABLE " . $table_name . " (
    	            id mediumint(9) NOT NULL AUTO_INCREMENT,
    	            time bigint(11) DEFAULT '0' NOT NULL,
                	comment text NOT NULL,
                	UNIQUE KEY id (id)
    	            );";

          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          dbDelta($sql);
          add_option("tap_version", $tap_version);
       };
    }       
?>