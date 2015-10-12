<?php
session_start();
function delete_jgteam_caches() {
    delete_transient('justgiving_teams');    
}
function jg_add_team_fn(){
    global $wpdb;
    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Teams</h2>';
	echo '</div>';

    if (!current_user_can('add_users') ) 
    {
        $teamRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgteams WHERE `owner`='". get_current_user_id() . "' ;");
    }
    else
    {
        $teamRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgteams ;");    
    }
    
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true); 
    
    
    $url=$_SERVER['REQUEST_URI'];
    if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Cached Teams') {
            delete_jgteam_caches();
            echo "<div class='updated'><p><strong>Cached data deleted successfully.</strong></p></div>";
    }
    if (isset($_POST['user-name']))
    {
        $hasJGAccount = $client->Account->IsEmailRegistered(trim($_POST['user-name']));
        if($hasJGAccount){
            /* if login JG change password */
            $ret = $client->Account->ValidateAccount(trim($_POST['user-name']), trim($_POST['password']) );
            if ($ret->isValid ){  
                $client->Username = trim($_POST['user-name']);
                $client->Password = trim($_POST['password']);
                $_SESSION['email'] = trim($_POST['user-name']);
                $_SESSION['userEnc'] = base64_encode($_POST['user-name'].':'.trim($_POST['password']));    
            }
        }
    }
?>
<form action="" method="post" class="user-forms" id="cache_form" name="cacheForm">
<input type="submit" name="submit" class="button-secondary" value="Delete Cached Teams"/>
</form>
            <div class="postbox-container" style="width:69%; margin-right:1%">
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <table class='form-table'>
<?php 
    foreach ($teamRows as $team)                     
    {
?>    
                        <tr valign='top'>
                           <td><a href="<?php echo admin_url( 'admin.php?page=jg_view_edit_teams_page&shrt='.$team->teamshortname); ?>"><?php echo $team->teamshortname ?></td>
                           <td><?php echo $team->teamshortname ?></td>
                           <td><?php echo $team->teamname ?></td>
                           <td><?php echo $team->teamstory ?></td>
                           <td><?php echo $team->teamtype ?></td>
                           <td><?php echo $team->teamtarget ?></td>
                           <td><?php
                           if ($team->teamfbpage != ''){
                            ?>
                                <a href="<?php echo $team->teamfbpage ?>">Facebook</a>
                            <?php
                           }
                           if ($team->teamtwpage != ''){
                            ?>
                                &nbsp;<a href="<?php echo $team->teamtwpage ?>">Twitter</a>
                            <?php
                           }
                           ?></td>
                           <td><?php echo $team->teamtargettype ?></td> 
                        </tr>  
<?php
    }
        
    $ret = $client->Account->GetUser($_SESSION['userEnc']);
    if ($ret->activePageCount > 0 ){  
        echo '<a href="' . admin_url( 'admin.php?page=jg_view_edit_teams_page&shrt=') .'">Create team as current user</a>';
    }
    elseif ($ret->activePageCount == 0 && trim($_SESSION['userEnc']) != '' ){
        echo '<a href="' . admin_url( 'admin.php?page=jg_view_add_page') .'">Create page as current user</a>';
    }
    elseif(!$ret){
?>
<form action="" method="post" class="user-forms" id="login_form" name="loginForm">
		<div class="col-xs-* col-xs-12">
            <div class="form-item input-row">
                <label for="user-name">Your email address*</label>
   
                <input type="email" name="user-name" id="user-name" class="input-text" value=""  validate="required:true" />                
            </div>
            <div class="form-item input-row">
                <label for="password">Password*</label>
                <span class="error"></span>
                <input type="password" name="password" id="password" class="input-text"  validate="required:true" />                
            </div>        
        </div>
        <input type="submit" name="submit" value="login" />
</form>        
<?php        
    }       
?>
                        </table>
                  </div>
               </div>
            </div>
<?php            
}
?>