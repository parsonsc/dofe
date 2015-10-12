<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class DonationApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		=	$justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();        
	}
	
	public function Retrieve($donationId, $user)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/donation/" . $donationId;
		$url = $this->BuildUrl($locationFormat);
        $this->curlWrapper->addOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $user, 
		    'Authorization' => 'Basic '. $user ));        
		$json = $this->curlWrapper->get($url);
        //print_R($this->curlWrapper);
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
            return json_decode($json); 
        else return False;            
	}	
	
	public function RetrieveStatus($donationId)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/donation/" . $donationId . "/status";
		$url = $this->BuildUrl($locationFormat);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));        
		$json = $this->curlWrapper->get($url);        
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
            return json_decode($json); 
        else return False;   
	}
}