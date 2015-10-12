<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class AccountApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		= $justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();
	}
    
    public function GetUser($user)
    {
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account";
		$url = $this->BuildUrl($locationFormat);
		$this->curlWrapper->resetAll();
		$this->curlWrapper->setDefaults();
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(
			array(
                'Accept' => 'application/json',            
                'Content-type' => 'application/json', 
                'Authorize' => 'Basic '. $user, 
                'Authorization' => 'Basic '.$user ));        
		$json = $this->curlWrapper->get($url);
        
       // file_put_contents('/var/www/html/doe/doe_error.log',print_R($this->curlWrapper, true), FILE_APPEND);
        if($this->curlWrapper->getTransferInfo('http_code') == 200)		
		{
			return json_decode($json);
		}
		else if($this->curlWrapper->getTransferInfo('http_code') == 401)
		{
			return false;
		}
    }
	
	public function Create($createAccountRequest)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account";
		$url = $this->BuildUrl($locationFormat);
		$payload = json_encode($createAccountRequest);
        
		$fh = fopen('php://temp', 'r+');
		fwrite($fh, $payload);
		rewind($fh);
                
		$this->curlWrapper->resetAll();
		$this->curlWrapper->setDefaults();
        
		//print_R($this->Parent->Username.":".$this->Parent->Password);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
		
		if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addOption(CURLOPT_PUT, true);
		$this->curlWrapper->addOption(CURLOPT_INFILE, $fh);
		$this->curlWrapper->addOption(CURLOPT_INFILESIZE, strlen($payload));
		$this->curlWrapper->addHeader(array(            
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));
		$json = $this->curlWrapper->put($url);

        //$current = print_R($this->curlWrapper, true);
        //error_log($current);
        /*        
        $current = print_R(json_decode($json), true);
        file_put_contents('curldata.txt', $current, FILE_APPEND);
        */
		if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return json_decode($json);
		}            
		else if($this->curlWrapper->getTransferInfo('http_code') == 400)
		{
		    throw new Exception('Email address already in use');
		}
		else
		{
		    throw new Exception('Fake email addresses cannot be used');
		}
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
        //print_R(json_decode($json));
		return json_decode($json); 
	}
	
	public function IsEmailRegistered($email)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account/" . $email;
		$url = $this->BuildUrl($locationFormat);		

		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));       
		$this->curlWrapper->head($url);
        //error_log(print_R($this->curlWrapper, true));
		//print_R($this->curlWrapper->getTransferInfo());
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
			throw new Exception('IsEmailRegistered Returned ' . $this->curlWrapper->getTransferInfo('http_code') .' for '. $email);
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
		if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return true;
		}
		else if($this->curlWrapper->getTransferInfo('http_code') == 400)
		{
			return false;
		}
	}
    
	public function ValidateAccount($email, $password)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/account/validate";
		$url = $this->BuildUrl($locationFormat);
		$payload = json_encode(array('email'=> $email, 'password'=> $password));	
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addOption(CURLOPT_POSTFIELDS, $payload);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));        
		$json = $this->curlWrapper->post($url);
        //error_log(print_R($this->curlWrapper, true));
		return json_decode($json); 		
	}        
}