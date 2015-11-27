<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * Bank Account entity
 */
class BankAccount extends EntityBase {
    
    /**
     * User identifier
     * @var LeetchiId 
     */
    public $UserId;
    
    /**
     * Type of bank account
     * @var string 
     */
    public $Type;
    
    /**
     * Owner name
     * @var string 
     */
    public $OwnerName;
    
    /**
     * Owner address
     * @var string 
     */
    public $OwnerAddress;
    
     /**
     * One of BankAccountDetails implementations, depending on $Type
     * @var object 
     */
    public $Details;
    
    /**
     * Get array with mapping which property depends on other property  
     * @return array
     */
    public function GetDependsObjects() {
        return array(
            'Type' => array(
                '_property_name' => 'Details',
                'IBAN' => '\P4M\MangoPayBundle\MangoPaySDK\Types\BankAccountDetailsIBAN',
                'GB' => '\P4M\MangoPayBundle\MangoPaySDK\BankAccountDetailsGB',
                'US' => '\P4M\MangoPayBundle\MangoPaySDK\BankAccountDetailsUS',
                'CA' => '\P4M\MangoPayBundle\MangoPaySDK\BankAccountDetailsCA',
                'OTHER' => '\P4M\MangoPayBundle\MangoPaySDK\BankAccountDetailsOTHER',
            )
        );
    }
    
    /**
     * Get array with read-only properties
     * @return array
     */
    public function GetReadOnlyProperties() {
        $properties = parent::GetReadOnlyProperties();
        array_push( $properties, 'UserId' );
        array_push( $properties, 'Type' );
        return $properties;
    }
}
