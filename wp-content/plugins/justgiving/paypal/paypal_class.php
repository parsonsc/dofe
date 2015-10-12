<?php
/**
 */

define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('SSL_SAND_URL','https://www.sandbox.paypal.com/cgi-bin/webscr');

class paypal_class {
	
	private $ipn_status;                // holds the last status
	public $admin_mail; 				// receive the ipn status report pre transaction
	public $paypal_mail;				// paypal account, if set, class need to verify receiver
	public $txn_id;						// array: if the txn_id array existed, class need to verified the txn_id duplicate
	public $ipn_log;                    // bool: log IPN results to text file?
	private $ipn_response;              // holds the IPN response from paypal   
	public $ipn_data = array();         // array contains the POST values for IPN
	private $fields = array();          // array holds the fields to submit to paypal
	private $ipn_debug; 				// ipn_debug
	
	// initialization constructor.  Called when class is created.
	function __construct() {

		$this->ipn_status = '';
		$this->admin_mail = null;
		$this->paypal_mail = null;
		$this->txn_id = null;
		$this->tax = null;
		$this->ipn_log = true;
		$this->ipn_response = '';
		$this->ipn_debug = false;
	}

	// adds a key=>value pair to the fields array, which is what will be 
	// sent to paypal as POST variables. 
	public function add_field($field, $value) {
		$this->fields["$field"] = $value;
	}


	// this function actually generates an entire HTML page consisting of
	// a form with hidden elements which is submitted to paypal via the 
	// BODY element's onLoad attribute.  We do this so that you can validate
	// any POST vars from you custom form before submitting to paypal.  So 
	// basically, you'll have your own form which is submitted to your script
	// to validate the data, which in turn calls this function to create
	// another hidden form and submit to paypal.
		
	// The user will briefly see a message on the screen that reads:
	// "Please wait, your order is being processed..." and then immediately
	// is redirected to paypal.
	public function submit_paypal_post() {

		$paypal_url = ($_REQUEST['sandbox'] == 1) ? SSL_SAND_URL : SSL_P_URL;
		echo "<html>\n";
		echo "<head><title>Processing Payment...</title></head>\n";
		echo "<body onLoad=\"document.forms['paypal_form'].submit();\">\n";
        echo "<body >\n";
		echo "<center><h2>Please wait, your order is being processed and you";
		echo " will be redirected to the paypal website.</h2></center>\n";
		echo "<form method=\"post\" name=\"paypal_form\" ";
		echo "action=\"".$paypal_url."\">\n";
		if (isset($this->paypal_mail))echo "<input type=\"hidden\" name=\"business\" value=\"$this->paypal_mail\"/>\n";
		foreach ($this->fields as $name => $value) {
			echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		echo "<center><br/><br/>If you are not automatically redirected to ";
		echo "paypal within 5 seconds...<br/><br/>\n";
		echo "<input type=\"submit\" value=\"Click Here\"></center>\n";
		
		echo "</form>\n";
		echo "</body></html>\n";
        exit;
	}
   
/**
 * validate the	IPN
 * 
 * @return bool IPN validation result
 */
	public function validate_ipn() {
        if(empty($IPN)){
            $IPN = $_POST;
        }
        if(empty($IPN['verify_sign'])){
            return null;
        }
        $IPN['cmd'] = '_notify-validate';
        $PaypalHost = (empty($IPN['test_ipn']) ? 'www' : 'www.sandbox').'.paypal.com';
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cURL, CURLOPT_URL, "https://{$PaypalHost}/cgi-bin/webscr");
        curl_setopt($cURL, CURLOPT_ENCODING, 'gzip');
        curl_setopt($cURL, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($cURL, CURLOPT_POST, true); // POST back
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $IPN); // the $IPN
        curl_setopt($cURL, CURLOPT_HEADER, false);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($cURL, CURLOPT_FORBID_REUSE, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
        curl_setopt($cURL, CURLINFO_HEADER_OUT, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Connection: close',
            'Expect: ',
        ));
        $Response = curl_exec($cURL);
        $Status = (int)curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        if(empty($Response) or !preg_match('~^(VERIFIED|INVALID)$~i', $Response = trim($Response)) or !$Status){
            $this->ipn_status = 'IPN Validation Failed';
			return false;
        }
        if(intval($Status / 100) != 2){
            $this->ipn_status = 'IPN Validation Failed';
			return false;
        }
        if (!strcasecmp($Response, 'VERIFIED'))
        {
			$this->ipn_status = 'IPN VERIFIED';
			return true;
        }
        else{
			$this->ipn_status = 'Validation Failed';
			return false;
        }    
	} 
   
	public function send_report($subject) {
		$body .= "from " . $this->ipn_data ['payer_email'] . " on " . date ( 'm/d/Y' );
		$body .= " at " . date ( 'g:i A' ) . "\n\nDetails:\n" . $this->ipn_status;
		mail ( $this->admin_mail, $subject, $body );
	}

	public function print_report(){
		$find [] = "\n";
		$replace [] = '<br/>';
		$html_content = str_replace ( $find, $replace, $this->ipn_status );
		echo $html_content;
	}
	
	public function dump_fields() {
 
		// Used for debugging, this function will output all the field/value pairs
		// that are currently defined in the instance of the class using the
		// add_field() function.
		echo "<h3>paypal_class->dump_fields() Output:</h3>";
		echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>"; 
		ksort($this->fields);
		foreach ($this->fields as $key => $value) {echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";}
		echo "</table><br>"; 
	}

	private function debug($msg) {
		
		if (! $this->ipn_debug)
			return;
		
		$today = date ( "Y-m-d H:i:s " );
		$myFile = ".ipn_debugs.log";
		$fh = fopen ( $myFile, 'a' ) or die ( "Can't open debug file. Please manually create the 'debug.log' file and make it writable." );
		$ua_simple = preg_replace ( "/(.*)\s\(.*/", "\\1", $_SERVER ['HTTP_USER_AGENT'] );
		fwrite ( $fh, $today . " [from: " . $_SERVER ['REMOTE_ADDR'] . "|$ua_simple] - " . $msg . "\n" );
		fclose ( $fh );
		chmod ( $myFile, 0600 );
	}

}         
?>