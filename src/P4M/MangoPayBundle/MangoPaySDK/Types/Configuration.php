<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Types;

/**
 * Configuration settings
 */
class Configuration {
	
    /**
     * Client Id
     * @var string 
     */
    public $ClientId;
	
    /**
     * Client password
     * @var string 
     */
    public $ClientPassword;
	
    /**
     * Base URL to MangoPay API
     * @var string 
     */
    public $BaseUrl = 'https://api.sandbox.mangopay.com';
    
    /**
    * Path to folder with temporary files (with permissions to write)
    */
    public $TemporaryFolder = null;
    
    /**
     * [INTERNAL USAGE ONLY] 
     * Switch debug mode: log all request and response data
     */
    public $DebugMode = false;
    
    /**
     * Set the logging class if DebugMode is enabled
     */
    public $LogClass = 'MangoPay\Logs';
}