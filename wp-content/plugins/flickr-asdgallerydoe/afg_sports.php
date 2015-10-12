<?php
include_once('afg_libs.php');


function afg_view_edit_sports() {
?>
<div class='wrap'>
<h2><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/>Sports | Flickr Gallery</h2>

<?php
    global $wpdb;     
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}flickrsports" );   

        if (isset($_POST['submit']) && $_POST['submit'] == 'Submit') {
            foreach($results as $row) {
                if (isset($_POST['delete_sport_' . $row->id]) && $_POST['delete_sport_' . $row->id] == 'on') {
                    $wpdb->update( 
                        $wpdb->prefix."flickrsports" , 
                        array( 
                            'published' => ($row->published == 1) ? 0 : 1
                        ), 
                        array( 'id' => $row->id), 
                        array( 
                            '%d'	// value2
                        ), 
                        array( '%d' ) 
                    );
                }
            }
            if (trim($_POST['sport_new']) != ''){
                $wpdb->insert( 
                     $wpdb->prefix."flickrsports", 
                    array( 
                        'sport' => trim($_POST['sport_new']), 
                        'published' => 1 
                    ), 
                    array( 
                        '%s', 
                        '%d' 
                    ) 
                );
            }
        }
        

?>

<?php
    echo afg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
?>

      <form method='post' action='<?php echo $url ?>'>
         <div class="postbox-container" style="width:69%; margin-right:1%">
            <div id="poststuff">
               <div class="postbox" style='box-shadow:0 0 2px'>
                  <h3>Sports</h3>
                  <table class='form-table' style='margin-top:0'>
                     <tr style='border:1px solid Gainsboro' valign='top'>
                        <th scope='row'></th>
                        <th scope='row'><strong>ID</strong></th>
                        <th scope='row'><strong>Sport</strong></th>
                        <th scope='row'><strong>Published</strong></th>
                     </tr>
                     
<?php
$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."flickrsports" );    
foreach ( $results as $row ) 
{
?>
        <tr style='border:1px solid Gainsboro' valign='top'>
            <td style='width:4%'><input type='checkbox' name='delete_sport_<?php echo $row->id;?>' id='delete_sport_<?php echo $row->id;?>' /></td>
            <td style='width:12%'><?php echo $row->id;?></td>
            <th style='width:22%'><?php echo $row->sport;?></th>
            <td style='width:4%'><?php echo ($row->published == 1) ? 'X': '';?></td>
        </tr>    
<?php      
}
?>
        <tr style='border:1px solid Gainsboro' valign='top'>
            <td style='width:4%'></td>
            <td style='width:12%'></td>
            <th style='width:22%'><input type="text" name="sport_new" /></th>
            <td style='width:4%'></td>
        </tr> 
                  </table>
            </div></div>
            <input type="submit" name="submit" class="button" value="Submit" />
         </div>
         <div class="postbox-container" style="width: 29%;">
            <?php echo afg_usage_box('the Gallery Code');
    //echo afg_donate_box();
    //echo afg_share_box();
 ?>
         </div>
      </form>
<?php
}