<?php
include_once 'ClientBase.php';
include_once 'Http/CurlWrapper.php';

class TeamApi extends ClientBase
{		
	public $Parent;
	public $curlWrapper;
	
	public function __construct($justGivingApi)
	{
		$this->Parent		=	$justGivingApi;
		$this->curlWrapper	= new CurlWrapper();
		$this->curlWrapper->setDefaults();        
	}

	public function Create($team, $user)
	{

		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/" . $team['teamShortName'];

		$url = $this->BuildUrl($locationFormat);

		$payload = json_encode($team);
        
		$fh = fopen('php://temp', 'r+');
		fwrite($fh, $payload);
		rewind($fh);
        
		$this->curlWrapper->resetAll();
		$this->curlWrapper->setDefaults();
        
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);	        
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
		$this->curlWrapper->addOption(CURLOPT_PUT, true);
		$this->curlWrapper->addOption(CURLOPT_INFILE, $fh);
		$this->curlWrapper->addOption(CURLOPT_INFILESIZE, strlen($payload));

		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $user , 
		    'Authorization' => 'Basic '. $user ));  

		$json = $this->curlWrapper->put($url);	
        $cntent  = print_R($this->curlWrapper, true);
        
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
            //updated
			return true;
		}
        elseif($this->curlWrapper->getTransferInfo('http_code') == 201)
		{
            //created
			return true;
		}
		else 
		{
			return false; //$this->curlWrapper->getTransferInfo();
		}               
		
	}
    
    public function Exists($team)
	{
        $locationFormat = '';
        if (!is_array($team)){
            $locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/" . $team;
        }
        else{
            $locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/" . $team['teamShortName'];
        }
		$url = $this->BuildUrl($locationFormat);
        			
		$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);

		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '. $this->BuildAuthenticationValue() ));            
            

        $this->curlWrapper->head($url);	
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return true;
		}
		else 
		{
			return false;
		}        
	}        
    
	public function Join($teamShortName, $user, $page)
	{
		$locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/join/" . $teamShortName;
		$url = $this->BuildUrl($locationFormat);     
		$payload = json_encode($page);
        
		$fh = fopen('php://temp', 'r+');
		fwrite($fh, $payload);
		rewind($fh);
			
		$this->curlWrapper->resetAll();
		$this->curlWrapper->setDefaults();            
            
		if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addOption(CURLOPT_PUT, true);            
		$this->curlWrapper->addOption(CURLOPT_INFILE, $fh);
		$this->curlWrapper->addOption(CURLOPT_INFILESIZE, strlen($payload)); 

		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $user , 
		    'Authorization' => 'Basic '. $user ));            
		$this->curlWrapper->put($url);	
        //$cntent  = print_R($this->curlWrapper, true);
        //file_put_contents( '/xampp/htdocs/cruk_undie/out.txt' , $cntent, FILE_APPEND );    

        if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return true;
		}
		else 
		{
			return false;
		}        
	}  

    public function Search($searchArray = array())
    {
        $qstring = array();
        $locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/search";     
        foreach ($searchArray as $k => $v)
        {
            $qstring[] = $k.'='.$v;            
        }
        if (count($qstring) > 0)
        {
            $locationFormat .= '?'. implode('&amp;', $qstring);
        }
        
		$url = $this->BuildUrl($locationFormat); 
        $this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json', 
		    'Authorize' => 'Basic '. $this->BuildAuthenticationValue() , 
		    'Authorization' => 'Basic '. $this->BuildAuthenticationValue() ));        
		$json = $this->curlWrapper->get($url);   
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return json_decode($json);
		}
		else 
		{
			return false;
		}                       
    }
    
    public function Get($teamShortName = '')
    {
        $qstring = array();
        $locationFormat = $this->Parent->RootDomain . "{apiKey}/v{apiVersion}/team/". $teamShortName;            
		$url = $this->BuildUrl($locationFormat); 
        
        //$this->curlWrapper->addOption(CURLOPT_USERPWD, $this->Parent->Username.":".$this->Parent->Password);
        if ($this->Parent->Debug) $this->curlWrapper->addOption(CURLOPT_VERBOSE, 1);
		$this->curlWrapper->addHeader(array(
            'Accept' => 'application/json',
		    'Content-type' => 'application/json' ));        
		$json = $this->curlWrapper->get($url);    
        if($this->curlWrapper->getTransferInfo('http_code') == 200)
		{
			return json_decode($json);
		}
		else 
		{
			return false;
		}                       
    }    
}