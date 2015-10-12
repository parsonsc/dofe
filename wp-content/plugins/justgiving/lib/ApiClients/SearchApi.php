<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class SearchApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		=	$justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();        
	}
	
	public function CharitySearch($searchTerms, $pageSize=50, $pageNumber=1)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/charity/search?q=".urlencode($searchTerms)."&PageSize=".$pageSize."&PageNum=".$pageNumber;
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
    
	public function EventSearch($searchTerms, $pageSize=50, $pageNumber=1)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/event/search?q=".urlencode($searchTerms)."&PageSize=".$pageSize."&Page=".$pageNumber;
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