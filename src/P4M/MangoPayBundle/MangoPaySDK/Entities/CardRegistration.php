<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * CardRegistration entity
 */
class CardRegistration extends EntityBase {
    
    /**
     * User Id
     * @var string 
     */
    public $UserId;
    
    /**
     * Access key
     * @var string 
     */
    public $AccessKey;
        
    /**
     * Preregistration data
     * @var string 
     */
    public $PreregistrationData;
        
    /**
     * Card registration URL
     * @var string 
     */
    public $CardRegistrationURL;
        
    /**
     * Card Id
     * @var string 
     */
    public $CardId;
        
    /**
     * Card registration data
     * @var string 
     */
    public $RegistrationData;
        
    /**
     * Result code
     * @var string 
     */
    public $ResultCode;
        
    /**
     * Currency
     * @var string 
     */
    public $Currency;
        
    /**
     * Status
     * @var string 
     */
    public $Status;
        
    /**
     * Get array with read-only properties
     * @return array
     */
    public function GetReadOnlyProperties() {
        $properties = parent::GetReadOnlyProperties();
        array_push( $properties, 'AccessKey' );
        array_push( $properties, 'PreregistrationData' );
        array_push( $properties, 'CardRegistrationURL' );
        array_push( $properties, 'CardId' );
        array_push( $properties, 'ResultCode' );
        array_push( $properties, 'Status' );
        return $properties;
    }
}