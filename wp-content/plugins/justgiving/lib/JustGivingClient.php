<?php
set_time_limit(300);
include_once 'ApiClients/PageApi.php';
include_once 'ApiClients/AccountApi.php';
include_once 'ApiClients/CharityApi.php';
include_once 'ApiClients/DonationApi.php';
include_once 'ApiClients/SearchApi.php';
include_once 'ApiClients/EventApi.php';
include_once 'ApiClients/TeamApi.php';
include_once 'ApiClients/CountryApi.php';
include_once 'ApiClients/UserApi.php';

class JustGivingClient
{	
	public $ApiKey;
	public $ApiVersion;
	public $Username;
	public $Password;
	public $RootDomain;
    public $Debug;
	
	public $Page;
	public $Account;
	public $Charity;
	public $Donation;
	public $Search;
	public $Event;
	public $Team;
    public $User;

	public function __construct($rootDomain, $apiKey, $apiVersion, $username="", $password="", $debug = false)
	{
		$this->RootDomain   	= (string) $rootDomain; 
		$this->ApiKey     		= (string) $apiKey;
		$this->ApiVersion     	= (string) $apiVersion;
		$this->Username     	= (string) $username;
		$this->Password     	= (string) $password;
		$this->Debug			= (bool) $debug;
		$this->curlWrapper		= new CurlWrapper();
		
		// Init API clients
		$this->Page				= new PageApi($this);
		$this->Account			= new AccountApi($this);
		$this->Charity			= new CharityApi($this);
		$this->Donation			= new DonationApi($this);
		$this->Search			= new SearchApi($this);
		$this->Event			= new EventApi($this);
		$this->Team			    = new TeamApi($this);
		$this->Country			= new CountryApi($this);
		$this->User			    = new UserApi($this);
	}
}