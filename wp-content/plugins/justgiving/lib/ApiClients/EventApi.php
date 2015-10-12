<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class EventApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		=	$justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();        
	}

	public function Create($event)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/event";
		$url = $this->BuildUrl($locationFormat);
		$payload = json_encode($event);
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
		$this->curlWrapper->addOption(CURLOPT_POSTFIELDS, $payload);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '.$this->BuildAuthenticationValue() ));        
		$json = $this->curlWrapper->post($url);	
        //file_put_contents('/var/www/html/doe/doe_error.log',print_R($this->curlWrapper,true), FILE_APPEND);
        if($this->curlWrapper->getTransferInfo('http_code') == 201)		            
		{
			return json_decode($json);
		} else return false;
	}
	
	public function Retrieve($eventId)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/event/" . $eventId;
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
	
	public function RetrievePages($eventId, $pageSize=50, $pageNumber=1)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/event/" . $eventId . "/pages?PageSize=".$pageSize."&PageNum=".$pageNumber;
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
