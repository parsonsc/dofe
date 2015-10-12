<?php
if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}
function jg_teamlist(){
    global $wpdb;
    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Teams</h2>';
	echo '</div>';
    $userRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgusers WHERE `pageurl`<>'';"); 

    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword'], true);   
    
    $url=$_SERVER['REQUEST_URI'];
    $fndit = False;
    $defpage = '';
    $defteam = '';
    $shortteamname = '';
    $teamstory = '';
    $deftype = '';
    $deftargettype = '';
    $deffbpage = '';    
    $deftwpage = '';
    if (!isset($_SESSION['userEnc']))
    {
        if (!isset($_POST['user-name']))
        {
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
            exit;
        }
        else
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
    }    
    //print_R($_SESSIOn);
    if(isset($_REQUEST['shrt']) && isset($_POST['jg_team_id']) && 
            'POST' == $_SERVER['REQUEST_METHOD']){
        $myteam = $wpdb->get_row(" SELECT * FROM {$wpdb->prefix}jgteams WHERE `teamshortname`='".addslashes($_POST['jg_team_shortname']). "' ;");        
        //print_R($_POST);
        /*
        Array ( 
            [jg_page_id] => test2964 
            [jg_team_id] => 
            [jg_team_name] => 
            [jg_team_shortname] => 
            [jg_teamstory] => 
            [jg_teamtype] => ByInvitationOnly 
            [jg_team_target] => 
            [jg_teamtargettype] => Fixed 
            )
        */

        if ($myteam != null && !$client->Team->Exists($_POST['jg_team_shortname'])) {
            //update
            $ret = $wpdb->update( 
                $wpdb->prefix."jgteams", 
                array( 
                    'owner' => get_current_user_id(),
                    'teamname' => addslashes($_POST['jg_team_name']),
                    'teamfriendly' => addslashes($_POST['jg_team_friendly']),
                    'teamstory' => addslashes($_POST['jg_teamstory']),
                    'teamtargettype' => addslashes($_POST['jg_teamtargettype']),
                    'teamtarget' => addslashes($_POST['jg_team_target']),
                    'teamtype' => addslashes($_POST['jg_teamtype']),
                    'teamfbpage' => addslashes($_POST['jg_teamfbpage']),
                    'teamtwpage' => addslashes($_POST['jg_teamtwpage']),                   
                ), 
                array( 'teamshortname' => $_POST['jg_team_shortname'] )
            );           
            $request = array();
            $request['teamShortName'] = $_POST['jg_team_shortname'] ;
            $request['name'] = $_POST['jg_team_name'];
            $request['story'] = $_POST['jg_teamstory'];
            $request['targetType'] = $_POST['jg_teamtargettype'];
            $request['teamTarget'] = $_POST['jg_team_target'];
            $request['teamType'] = $_POST['jg_teamtype'];
            $request['teamMembers'] = array(array(
                'pageShortName' => $_POST['jg_page_id']
            ));
            $response = $client->Team->Create($request, trim($_SESSION['userEnc']));
        }
        elseif(!$client->Team->Exists($_POST['jg_team_shortname'])){
            //insert
            $wpdb->insert(
                $wpdb->prefix . "jgteams",
                array(
                    'owner' => get_current_user_id(),
                    'teamname' => addslashes($_POST['jg_team_name']),
                    'teamshortname' => addslashes($_POST['jg_team_shortname']),
                    'teamstory' => addslashes($_POST['jg_teamstory']),
                    'teamtargettype' => addslashes($_POST['jg_teamtargettype']),
                    'teamtarget' => addslashes($_POST['jg_team_target']),
                    'teamtype' => addslashes($_POST['jg_teamtype']),
                    'teamfbpage' => addslashes($_POST['jg_teamfbpage']),
                    'teamtwpage' => addslashes($_POST['jg_teamtwpage']),                   
                    'submittedtime' => date('Y-m-d H:i:s')
                )
            );
            $request = array();
            $request['teamShortName'] = $_POST['jg_team_shortname'] ;
            $request['name'] = $_POST['jg_team_name'];
            $request['story'] = $_POST['jg_teamstory'];
            $request['targetType'] = $_POST['jg_teamtargettype'];
            $request['teamTarget'] = $_POST['jg_team_target'];
            $request['teamType'] = $_POST['jg_teamtype'];
            $request['teamMembers'] = array(array(
                'pageShortName' => $_POST['jg_page_id']
            ));
            $response = $client->Team->Create($request, trim($_SESSION['userEnc']));            
        } 
        else
        {
            $defpage = $_POST['jg_page_id'];
            $defteam = $_POST['jg_team_shortname'];
            $teamname = $_POST['jg_team_name'];
            $teamstory = $_POST['jg_teamstory'];
            $deftype = $_POST['jg_teamtype'];
            $deffbpage = $_POST['jg_teamfbpage'];
            $deftwpage = $_POST['jg_teamtwpage'];                               
            $deftargettype =  $_POST['jg_teamtargettype'] ; 
            $teamtarget = $_POST['jg_team_target'];
        }
    }
    elseif(isset($_REQUEST['jg_page_id']) && 'POST' == $_SERVER['REQUEST_METHOD'])
    {
        if(!$client->Team->Exists($_POST['jg_team_shortname'])){
            //insert

            $request = array();
            $request['teamShortName'] = $_POST['jg_team_shortname'] ;
            $request['name'] = $_POST['jg_team_name'];
            $request['story'] = $_POST['jg_teamstory'];
            $request['targetType'] = $_POST['jg_teamtargettype'];
            $request['teamTarget'] = $_POST['jg_team_target'];
            $request['teamType'] = $_POST['jg_teamtype'];
            $request['teamMembers'] = array(array(
                'pageShortName' => $_POST['jg_page_id']
            ));
            //print_R($request);
            $response = $client->Team->Create($request, trim($_SESSION['userEnc']));
            //print_R($response);
            
            if ($response){
                $wpdb->insert(
                    $wpdb->prefix . "jgteams",
                    array(
                        'owner' => get_current_user_id(),
                        'teamname' => addslashes($_POST['jg_team_name']),
                        'teamshortname' => addslashes($_POST['jg_team_shortname']),
                        'teamstory' => addslashes($_POST['jg_teamstory']),
                        'teamtargettype' => addslashes($_POST['jg_teamtargettype']),
                        'teamtarget' => addslashes($_POST['jg_team_target']),
                        'teamtype' => addslashes($_POST['jg_teamtype']),
                        'teamfbpage' => addslashes($_POST['jg_teamfbpage']),
                        'teamtwpage' => addslashes($_POST['jg_teamtwpage']),                   
                        'submittedtime' => date('Y-m-d H:i:s')
                    )
                );            
            }
            
        }    
    }
    if (!current_user_can('add_users') ) 
    {
        $teamRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgteams WHERE `owner`='". get_current_user_id() . "' ;");
    }
    else
    {
        $teamRows = $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}jgteams ;");    
    }    
    if (isset($_REQUEST['shrt'])){
        foreach ($teamRows as $team){
            if ($team->teamshortname == $_REQUEST['shrt'] && trim($_REQUEST['shrt']) !== '' ) {
                $fndit = true;
                $defteam = $team->teamshortname;
                $teamname = $team->teamname;
                $teamstory = $team->teamstory;
                $deftype = $team->teamtype;
                $deffbpage = $team->teamfbpage;
                $deftwpage = $team->teamtwpage;                   
                $deftargettype = $team->teamtargettype ;
                $teamtarget = $team->teamtarget;
            }
        }
    }

    $cpages = array();
    if (!$fndit && !current_user_can('add_users'))
    {
        $pages = $client->Account->ListAllPages($_SESSION['email']);
        foreach($pages as $page) {
            if ($page->pageShortName && $page->eventId == $wpjg_generalSettings['Event']  && $page->charityId == $wpjg_generalSettings['Charity'] ) {
                $cpages[] = $page;                
            }
        }
    }
    elseif(!$fndit){
        $pages = $client->Event->RetrievePages($wpjg_generalSettings['Event']  );
        foreach ($pages->fundraisingPages as $page)
        {
            $cpages[] = $page;
            $fndit = false;
        }       
    }
    if(!isset($_REQUEST['shrt']) && !$fndit && count($cpages) == 0 ){
        return '';
    }    
    if (count($cpages) == 0 && !$fndit)
    {
    echo '<a href="' . admin_url( 'admin.php?page=jg_add_page') .'">Create page as current user</a>';    
    }
    else
    {
?>

         <form method='post' action='<?php echo $url ?>'>
            <div class="postbox-container" style="width:69%; margin-right:1%">
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <table class='form-table'>
<?php
if (!$fndit)
{
?>
    
                        <tr valign='top'>
                           <th scope='row'>Start team with page</th>
                           <td>
                           <select id="jg_page" name="jg_page_id" >                          
<?php

    foreach($cpages as $page) {
        if ($page->pageShortName == $defpage)
            echo '<option value="'. $page->pageShortName .'" selected="selected">'. $page->pageTitle .'</option>';
        else
            echo '<option value="'. $page->pageShortName .'">'. $page->pageTitle .'</option>';
    }
?>                        
                           </select></td>
                        </tr>                     
<?php
}
?>
<script type="text/javascript">
function loadTeamSettings() {
    var newAdditionalURL = "";
    var tempArray = window.location.href.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != 'shrt'){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var rows_txt = '';
    var shrtName = document.getElementById('jg_team').value;
    
    rows_txt = temp + "" + "shrt=" + shrtName;
    window.location.href = baseURL + "?" + newAdditionalURL + rows_txt;
}
</script>
                        <tr valign='top'>
                           <th scope='row'>Select Team to Edit</th>
                           <td>
                           <select id="jg_team" name="jg_team_id" onchange="loadTeamSettings()">
                           <option value="">New team</option>
                           
<?php
    foreach($teamRows as $team) {
        if ($team->teamshortname) {
            if ($team->teamshortname == $defteam)
                echo '<option value="'. $team->teamshortname .'" selected="selected">'. $team->teamname .'</option>';
            else
                echo '<option value="'. $team->teamshortname .'">'. $team->teamname .'</option>';
        }
    }
?>                        
                           </select></td>
                        </tr>
                        <tr valign='top'>
                           <th scope='row'>Team name</th>
                           <td>
                           <input type="text" id="jg_teamname" name="jg_team_name" value="<?php echo $teamname ?>" /></td>
                        </tr>                                                
                        <tr valign='top'>
                           <th scope='row'>Team shortname</th>
                           <td>
                           <input type="text" id="jg_teamshortname" name="jg_team_shortname" <?php echo ($fndit) ? 'readonly="readonly"' : ''; ?> value="<?php echo $defteam ?>" /></td>
                        </tr>                          
                        <tr valign='top'>
                           <th scope='row'>Team story</th>
                           <td>
                           <textarea id="jg_teamstory" name="jg_teamstory"><?php echo $teamstory ?></textarea></td>
                        </tr>
                        <tr valign='top'>
                           <th scope='row'>Team type</th>
                           <td>
<?php
echo '<select name="jg_teamtype">';
$result =  $wpdb->get_row(" SELECT COLUMN_TYPE as ct FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = '{$wpdb->prefix}jgteams' AND COLUMN_NAME = 'teamtype';");
$enumList = explode(",", str_replace("'", "", substr($result->ct, 5, (strlen($result->ct)-6))));
foreach($enumList as $value)
{
    if ($value == $deftype) 
    {
        echo '<option value="'.$value.'" selected="selected">'.$value.'</option>';
    }
    else
    {
        echo '<option value="'.$value.'">'.$value.'</option>';
    }
}
echo '</select>';
?>                                
                           </td>
                        </tr>
                        <tr valign='top'>
                           <th scope='row'>Team target</th>
                           <td>
                           <input type="text" id="jg_teamtarget" name="jg_team_target" value="<?php echo $teamtarget ?>" /></td>
                        </tr>                        
                        <tr valign='top'>
                           <th scope='row'>Team target type</th>
                           <td>
<?php
echo '<select name="jg_teamtargettype">';
$result =  $wpdb->get_row(" SELECT COLUMN_TYPE as ct FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = '{$wpdb->prefix}jgteams' AND COLUMN_NAME = 'teamtargettype';");
$enumList = explode(",", str_replace("'", "", substr($result->ct, 5, (strlen($result->ct)-6))));
foreach($enumList as $value)
{
    if ($value == $deftargettype) 
    {
        echo '<option value="'.$value.'" selected="selected">'.$value.'</option>';
    }
    else
    {
        echo '<option value="'.$value.'">'.$value.'</option>';
    }
}
echo '</select>';
?>                                                      
                           </td>
                        </tr>  
                        <tr valign='top'>
                           <th scope='row'>FB Page</th>
                           <td>
                           <input type="text" id="jg_teamfbpage" name="jg_teamfbpage" value="<?php echo $deffbpage ?>" /></td>
                        </tr>  
                        <tr valign='top'>
                           <th scope='row'>TW Page</th>
                           <td>
                           <input type="text" id="jg_teamtwpage" name="jg_teamtwpage" value="<?php echo $deftwpage ?>" /></td>
                        </tr>  
                        </table>
                      <input type="submit" id="jg_save_changes" class="button-primary" value="Save Changes" />
                  </div>
               </div>
            </div>
         </form>
<?php          
    }
    /*
    if ($someimpossiblething)
    {
        $uniqueId = uniqid();            
        $request = array();
        $request['teamShortName'] = "team".$uniqueId ;
        $request['name'] = "team".$uniqueId;
        $request['story'] = "story".$uniqueId;
        $request['targetType'] = "Aggregate";
        $request['teamType'] = "ByInvitationOnly";
        $request['teamMembers'] = array(array(
            'pageShortName' => $page->pageShortName
        ));
        $response = $client->Team->Create($request, trim($_SESSION['userEnc']));
        if ($response == 1){
            $response = $client->Team->Get($request['teamShortName']);
            print_R($response);
        }               
    }
    */
}