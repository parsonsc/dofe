<?php
session_start();
function jg_add_page_fn(){
    include_once(JG_PLUGIN_DIR.'/lib/JustGivingClient.php');
    
    //wp_enqueue_script('jquery');
    //wp_enqueue_script('suggest');
    
    $wpjg_generalSettings = get_option('jg_general_settings');
    $client = new JustGivingClient(
        $wpjg_generalSettings['ApiLocation'],
        $wpjg_generalSettings['ApiKey'],
        $wpjg_generalSettings['ApiVersion'],
        $wpjg_generalSettings['TestUsername'], $wpjg_generalSettings['TestValidPassword']);    

    if ( 'POST' == $_SERVER['REQUEST_METHOD']) {
        $pageExists = $client->Page->IsShortNameRegistered($_POST['pageshortname']);        
        if (!$pageExists){
            $dto = array(
                'currency' => ($_SESSION['country'] == 'Ireland') ? 'EUR' : 'GBP',
                'pageShortName' => $_POST['pageshortname'],
                'charityId' =>  $wpjg_generalSettings['Charity'],
                'eventId' => $wpjg_generalSettings['Event'],
                'justGivingOptIn' => ((bool) $_POST['jgoptin']),
                'charityOptIn' => ((bool) $_POST['charityoptin']),
                'pageTitle' => stripslashes($_POST['pagetitle']),
                'charityFunded' => false,
                'pageStory' => $wpjg_generalSettings['pageStory'],
                'pageSummaryWhat' => $wpjg_generalSettings['pageSummaryWhat'],
                "pageSummaryWhy" => $wpjg_generalSettings['pageSummaryWhy'],
                "images" => array(array(
                    "caption" => get_bloginfo( 'name' ),                  
                    "isDefault" => true,
                    "url" => $wpjg_generalSettings['imageurl']
                )),
                "customCodes" => array( 
                    "customCode1" => $wpjg_generalSettings['cc1'],
                    "customCode2" => $wpjg_generalSettings['cc2'],
                    "customCode3" => $wpjg_generalSettings['cc3'],
                    "customCode4" => $wpjg_generalSettings['cc4'],
                    "customCode5" => $wpjg_generalSettings['cc5'],
                    "customCode6" => $wpjg_generalSettings['cc6']
                )
            );
            //print_R($dto)   ;
            $page = $client->Page->Create(trim($_SESSION['userEnc']), $dto);
        }
    }
    
    $url=$_SERVER['REQUEST_URI'];
?>
    <div class="wrap"><div id="icon-tools" class="icon32"></div><h2>Add Page</h2></div>
    <form method='post' action='<?php echo $url ?>'>
    <div class="form-item input-row">
        <label for="pageshortname">Your event name* <span class="hint" data-title="This is the address of your JustGiving fundraising page ie www.justgiving.com/johnsmith"></span></label>
        <input type="text" name="pageshortname" id="pageshortname" value="" class="input-text" validate="required:true" />
    </div>
    <div class="form-item input-row">
        <label for="pagetitle">Event page title* <span class="hint" data-title="This is the title that will appear at the top of your JustGiving fundraising page"></span></label>
        <input type="text" name="pagetitle" id="pagetitle" value="" class="input-text" validate="required:true" />
    </div>
    <div class="form-item radiogroup-row">
        <div class="label">I&rsquo;m happy for JustGiving to contact me after the event* <span class="hint" data-title="Stay up to date with with JustGiving's news, tips and inspiring stories"></span></div>
        <div class="radiogroup">
            <label><input type="radio" name="jgoptin" id="jgoptinyes" value="1"  validate="required:true" />Yes</label>    
            <label><input type="radio" name="jgoptin" id="jgoptinno" value="0"  validate="required:true" />No</label>    
        </div>
        <span class="error"></span>
    </div>
    <div class="form-item radiogroup-row">
        <div class="label">I&rsquo;m happy for the charity to get in touch again after event* <span class="hint" data-title="Stay up to date with your charity's news about how your support is helping"></span></div>
        <div class="radiogroup">
            <label><input type="radio" name="charityoptin" id="charityoptinyes" value="1" validate="required:true" />Yes</label>    
            <label><input type="radio" name="charityoptin" id="charityoptinno" value="0"  validate="required:true" />No</label>    
        </div>
        <span class="error"></span>
    </div>
      <input type="submit" id="jg_save_changes" class="button-primary" value="Save Changes" />

    </form>  
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#pageshortname").suggest(ajaxurl + "?action=jg_autocompletesearch", { delay: 500, minchars: 3 });
    });
    </script>
<?php    

}
?>   