<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Default storage strategy implementation.
 */
class DefaultStorageStrategy implements IStorageStrategy {
    
    private $_prefixContent = '<?php exit(); ?>';
    private $_fileName = 'MangoPaySdkStorage.tmp.php';
    private $_config;
    
    function __construct($config) {
        $this->_config = $config;
    }
    
    /**
     * Gets the current authorization token.
     * @return \P4M\MangoPayBundle\MangoPaySDK\OAuthToken Currently stored token instance or null.
     */
    public function Get() {
        
        $filename = $this->GetPathToFile();
        if (!file_exists($filename))
            return null;
        
        $data = file_get_contents($filename);
        if ($data === false)
            return null;
        
        $serialized = str_replace($this->_prefixContent, '', $data);
        
//        die($filename);
        
        return unserialize($serialized);
    }

    /**
     * Stores authorization token passed as an argument.
     * @param \P4M\MangoPayBundle\MangoPaySDK\OAuthToken $token Token instance to be stored.
     */
    public function Store($token) {
        
//        die($this->GetPathToTemporaryFolder());
        if (!is_writable($this->GetPathToTemporaryFolder()))
            throw new \Exception('Cannot create or write to file ' . $this->GetPathToTemporaryFolder());
        
        $serialized = serialize($token);
        $result = file_put_contents($this->GetPathToFile(), $this->_prefixContent . $serialized, LOCK_EX);
        if ($result === false)
            throw new \Exception('Cannot put token to file');
    }
    
    /**
     * Get path to storage file 
     * @return string
     */
    private function GetPathToFile() {
        return $this->GetPathToTemporaryFolder() . DIRECTORY_SEPARATOR . $this->_fileName;
    }
    
    /**
     * Get path to temporary folder  
     * @return string
     */
    private function GetPathToTemporaryFolder() {
        
        if (is_null($this->_config->TemporaryFolder))
            throw new \P4M\MangoPayBundle\MangoPaySDK\Types\Exceptions\Exception('Path to temporary folder is not defined');
        
        return $this->_config->TemporaryFolder;
    }
}