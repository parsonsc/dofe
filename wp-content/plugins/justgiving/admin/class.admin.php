<?php if (!defined('JUSTGIVING_VERSION')) exit('No direct script access allowed');
class JG_Admin{
    private $version = NULL;

    function __construct(){
        $this->version = JUSTGIVING_VERSION;
    }
    function justgiving_initialize(){
        // check for activation
        $check = get_option( 'justgiving_activation' );
        register_setting( 'jg_general_settings', 'jg_general_settings' );
        // redirect on activation
        if ($check != "set") {   
            // add theme options
            add_option( 'justgiving_activation', 'set');
            // load DB activation function if updating plugin
            $this->justgiving_activate();
            // Redirect
            wp_redirect( admin_url().'users.php?page=JustGivingOptionsAndSettings' );
        }
        return false;
    }
    
    function justgiving_deactivate() {
        // remove activation check & version
        delete_option( 'justgiving_activation' );
        delete_option( 'justgiving_version' );
        
        // Flush rewrite rules when deactivated
        flush_rewrite_rules();
    }
    
    function justgiving_admin(){  
        // create menu item
        $justgiving_options = add_submenu_page( 'users.php', 'JustGiving', 'JustGiving', 'delete_users', 'JustGivingOptionsAndSettings', array( $this, 'justgiving_options_page' ) );
        // add menu item
        //add_action( "admin_print_styles-$justgiving_options", array( $this, 'justgiving_load' ) );
    }

    function justgiving_options_page() {
        // Grab Options Page
        include( JG_PLUGIN_DIR.'/admin/options.php' );
    }    
    
    function justgiving_activate(){
        $wppb_default_settings = array(
            'ApiLocation' => 	"https://api-sandbox.justgiving.com/",
            'ApiKey' => 	"decbf1d2",
            'TestUsername' 	=> "david.gurney@thegoodagency.co.uk",
            'TestValidPassword' => "DGcp!091",
            'TestInvalidPassword' => "badPassword",
            'ApiVersion' => 1,
            'Charity' => '2050',
            'Event' => '',
            'pageStory' => "I'm saying farewell to fizzy pop, bye-bye to beer and laters to lattes and drinking nothing but water for 2 weeks to raise money for the RNLI and their lifesavers at sea who drop everything at a moment's notice to save lives on the water.",
            'pageSummaryWhat' => 'taking the H2Only challenge for 2 weeks',
            'pageSummaryWhy' => 'I want to raise money for the RNLI!',
            'imageurl' => "https://s3.amazonaws.com/h2only.3sidedcube.com/profile-image.png",
            'targetAmount' => 150,
            'cc1' => '',            
            'cc2' => '',            
            'cc3' => '',            
            'cc4' => '',            
            'cc5' => '',            
            'cc6' => '',
            'paidaccess' => 0,
            'payamount' => 0,
            'paypal_email' => 'david.gurney@gmail.com',
            'paypal_femail' => 'david.gurney@gmail.com',
            'fbappid' => '1452796035009634',
            'mguser' => 'postmaster@sandboxe7036c41314545d9bf71a372e89ad22f.mailgun.org',
            'mgpass' => '4r8hrkso1he2',
            'useSMTP' => true,
            'smtp_server' => "smtp.mailgun.org",
            'smtp_port' => 25,
            'smtp_helo' => "mailgun.org",
            'smtp_uname' => "postmaster@sandboxe7036c41314545d9bf71a372e89ad22f.mailgun.org",
            'smtp_pword' => "4r8hrkso1he2",
            'mailer_id' => "UndieRun mail client",
            'email_from' => "hello@theundierun.co.uk",
            'reply_to' => "hello@theundierun.co.uk",
            'friendly_name' => "Thank you",
            'lolagrove' => 0,
            'stripekey' => '',
            'stripepkey' => '',
        );
        add_option( 'jg_general_settings', $wppb_default_settings);        
    }
}
?>