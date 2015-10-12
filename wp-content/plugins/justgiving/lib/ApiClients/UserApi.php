<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class UserApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		=	$justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();        
	}
	
	public function Create($createAccountRequest)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account";
		$url = $this->BuildUrl($locationFormat);
		$payload = json_encode($createAccountRequest);	

		$fh = fopen('php://temp', 'r+');
		fwrite($fh, $payload);
		rewind($fh);
		
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
		$this->curlWrapper->addOption(CURLOPT_INFILE, $fh);
		$this->curlWrapper->addOption(CURLOPT_INFILESIZE, strlen($payload)); 
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
        
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json', 
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));            
		$json = $this->curlWrapper->put($url);	
            
		return json_decode($json); 
	}
	
	public function ListAllPages($email)
	{		
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account/" . $email . "/pages";
		$url = $this->BuildUrl($locationFormat);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
        
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json', 
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));            
		$json = $this->curlWrapper->get($url);		
		return json_decode($json); 
	}
    
	
	public function IsEmailRegistered($email)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account/" . $email;
		$url = $this->BuildUrl($locationFormat);
		$this->curlWrapper->resetAll();
		$this->curlWrapper->setDefaults();        
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
        
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));            
		$this->curlWrapper->head($url);
		if($this->curlWrapper->getTransferInfo('http_code') == 200)		
		{
			return true;
		}
		else if($this->curlWrapper->getTransferInfo('http_code') == 404)
		{
			return false;
		}
		else		
		{
			throw new Exception('IsEmailRegistered returned a status code it wasn\'t expecting. Returned ' . $this->curlWrapper->getTransferInfo('http_code'));
		}
	}
       
	
	public function RequestPasswordReminder($email)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account/" . $email . "/requestpasswordreminder";
		$url = $this->BuildUrl($locationFormat);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
        
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));            
		$json = $this->curlWrapper->get($url);		
		return json_decode($json); 		
	}
}