<?php 
if (!current_user_can('manage_options')) {
    die('do one');
}
/**
 * Create Clean String
 *
 * Returns a clean comma separated array with no
 * trailing comma and all white space removed
 *
 * @return string
 **/
function create_clean_string($str) {
    $myarr = explode(",", $str);
    foreach ($myarr as $key => $value) {
        $value = trim($value);
        $value = str_replace(" ", "", $value);
        if (is_null($value) || $value=="") {
            unset($myarr[$key]);
        } else {
            $myarr[$key] = $value;
        };
    };
    $myarr = ( implode(",", array_values($myarr))  );    
    return $myarr;
}

// Process $_POST for form id="tnf_options_form"
if($_POST["tap_hidden"] == "Y") {
    
    $hashtags   = create_clean_string($_POST["tap_hashtags"]);
    $usernames  = create_clean_string($_POST["tap_usernames"]);
    $consumer_key  = create_clean_string($_POST["tap_consumer_key"]);
    $consumer_sec  = create_clean_string($_POST["tap_consumer_sec"]);
    $access_token  = create_clean_string($_POST["tap_access_token"]);
    $access_secret  = create_clean_string($_POST["tap_access_secret"]);
    $category   = $_POST["tap_category"];
    $user       = $_POST["tap_user"];
    
    if($_POST["tap_add_news_as_post"] == "Y") {
        $add_news_to_db = "checked";
        $add_news_to_db_boolean = TRUE;
    } else {
        $add_news_to_db = "";
        $add_news_to_db_boolean = FALSE;
    };

    if($hashtags !="" && $usernames !="") {
    
        update_option("tap_hashtags", $hashtags);
        update_option("tap_usernames", $usernames);
        update_option("tap_category", $category);
        update_option("tap_consumer_key", $consumer_key);
        update_option("tap_consumer_sec", $consumer_sec);
        update_option("tap_access_token", $access_token);
        update_option("tap_access_secret", $access_secret);
        update_option("tap_user", $user);
        update_option("tap_add_news_to_db", $add_news_to_db_boolean);
        
        $flash_success = "Tweets As Posts Options Updated";
    } else {
        $flash_error = "Please enter at least one Hashtag and one Twitter username";
    };
    
} else {
    
    // Get the options from the wp-options table using get_option($key);
    $hashtags = get_option("tap_hashtags");
    $usernames = get_option("tap_usernames");
    $exceptions = get_option("tap_exceptions");
    $category = get_option("tap_category");
    $user = get_option("tap_user");
    $consumer_key =	get_option("tap_consumer_key");
    $consumer_sec = get_option("tap_consumer_sec");
    $access_token = get_option("tap_access_token");
    $access_secret = get_option("tap_access_secret");
    // Add news as post?
    if(get_option("tap_add_news_to_db")) {
        $add_news_to_db = "checked";
    };
};

// Process $_POST for form id="tap_get_news_form"
if($_POST["tap_get_news_hidden"] == "Y" && get_option("tap_add_news_to_db")) {
    $retval = tap_get_news(TRUE);
    $flash_success = $retval." Tweet(s) added as Posts";
};    

// If tap_add_news_to_db == TRUE get the last 10 log entries

?>

<?php if(!$flash_success==""): ?>
<div class="updated">
    <p><?php _e($flash_success); ?></p>
</div>
<?php endif ?>

<?php if(!$flash_error==""): ?>
<div class="error">
    <p><?php _e($flash_error); ?></p>
</div>
<?php endif ?>

<div class="wrap">

<div id="icon-options-general" class="icon32"><br /></div>
<h2>Tweets As Posts Admin</h2>

<form name="tap_options_form" method="post" action="<?php echo str_replace( "%7E", "~", $_SERVER["REQUEST_URI"]); ?>">
	<input type="hidden" name="tap_hidden" value="Y">
	<h3>Settings</h3>
	<table class="form-table">
	    <tr>
	        <th><label for="tap_hashtags">Hashtag(s):</label></th>
	        <td><input type="text" name="tap_hashtags" id="tap_hashtags" value="<?php echo $hashtags; ?>" size="40"></input>&nbsp;<em>eg: news, events</em></input>
	    </tr>
	    <tr>
	        <th><label for="tap_usernames">Twitter Username(s):</label></th>
	        <td><input type="text" name="tap_usernames" id="tap_usernames" value="<?php echo $usernames; ?>" size="40">&nbsp;<em>eg: RoyalVolService (don't include the @)</em></td>
	    </tr>
	    <tr>
	        <th><label for="tap_add_news_as_post">Add Tweets as Posts:</label></th>
	        <td><input type="checkbox" name="tap_add_news_as_post" id="tap_add_news_as_post" value="Y" id="tap_add_news_as_post" <?php echo $add_news_to_db ?>></input>
	    </tr>
	    <tr>
	        <th><label for="tap_consumer_key">Consumer key:</label></th>
	        <td><input type="text" name="tap_consumer_key" id="tap_consumer_key" value="<?php echo $consumer_key; ?>" size="40"></td>
	    </tr>        
	    <tr>
	        <th><label for="tap_consumer_sec">Consumer secret:</label></th>
	        <td><input type="text" name="tap_consumer_sec" id="tap_consumer_sec" value="<?php echo $consumer_sec; ?>" size="40"></td>
	    </tr>        
	    <tr>
	        <th><label for="tap_access_token">Access Token:</label></th>
	        <td><input type="text" name="tap_access_token" id="tap_access_token" value="<?php echo $access_token; ?>" size="40"></td>
	    </tr>        
	    <tr>
	        <th><label for="tap_access_secret">Access Secret:</label></th>
	        <td><input type="text" name="tap_access_secret" id="tap_access_secret" value="<?php echo $access_secret; ?>" size="40"></td>
	    </tr>     
	    <tr>
	        <th><label for="name="tap_category">Post Category:</label></td>
	        <td><?php
                    $dropdown_options = array("show_option_all" => __("View all categories"), "hide_empty" => 0, "hierarchical" => 1, "show_count" => 1, "orderby" => "name", "name" => "tap_category", "selected" => $category);
                    wp_dropdown_categories($dropdown_options);
                ?>
            </td>
            <tr>
                <th><label for="name="tap_user">Default User:</label></td>
                <td><?php 
                        $dropdown_options = array("name" => "tap_user", "selected" => $user);
                        wp_dropdown_users($dropdown_options); 
                    ?>
                </td>
            </tr>
	    </tr>
	</table>
	<p class="submit"><input type="submit" name="Submit" value="Update Settings" /></p>
</form>	

<?php if(get_option("tap_add_news_to_db")): 

global $wpdb;
$sql = "SELECT id, time, comment FROM ".$wpdb->prefix."tap_log"." ORDER BY time DESC LIMIT 5";
$data = $wpdb->get_results($sql);        
?>		    
<form name="tap_get_news_form" method="post" action="<?php echo str_replace( "%7E", "~", $_SERVER["REQUEST_URI"]); ?>">
    <input type="hidden" name="tap_get_news_hidden" value="Y">
    <h3>Update History</h3>
    <ul>
    <?php foreach ($data as $item) : ?>
        <li><?php echo $item->comment; ?></li>
    <?php endforeach; ?>
    </ul>
    <p><em>...The next update is scheduled for <?php echo date("l jS \of F Y - H:i:s", wp_next_scheduled("tap_hourly_update_action")); ?></em></p>
	<h3>Run Update Now</h3>
	<p class="submit"><input type="submit" name="tap_get_news" value="Run Tweets As Posts Update" /></p>
<?php endif ?>	
</form>

</div>