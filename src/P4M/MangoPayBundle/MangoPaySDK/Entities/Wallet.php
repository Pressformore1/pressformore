<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * Wallet entity
 */
class Wallet extends EntityBase {
    
    /**
     * Array with owners identites
     * @var array 
     */
    public $Owners;
    
    /**
     * Wallet description
     * @var string 
     */
    public $Description;
    
    /**
     * Money in wallet
     * @var Money 
     */
    public $Balance;
    
    /**
     * Currency code in ISO
     * @var string
     */
    public $Currency;
    
    /**
     * Get array with mapping which property is object and what type of object 
     * @return array
     */
    public function GetSubObjects() {
        return array( 'Balance' => '\P4M\MangoPayBundle\MangoPaySDK\Types\Money' );
    }
    
    /**
     * Get array with read-only properties
     * @return array
     */
    public function GetReadOnlyProperties() {
        $properties = parent::GetReadOnlyProperties();
        array_push( $properties, 'Balance' );
        
        return $properties;
    }
}
