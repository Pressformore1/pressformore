<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * PayOut entity
 */
class PayOut extends Transaction {
    
    /**
     * Debited wallet Id
     * @var int 
     */
    public $DebitedWalletId;
    
    /**
     * PaymentType (BANK_WIRE, MERCHANT_EXPENSE, AMAZON_GIFTCARD)
     * @var string  
     */
    public $PaymentType;
    
    /**
     * One of PayOutPaymentDetails implementations, depending on $PaymentType
     * @var object 
     */
    public $MeanOfPaymentDetails;
    
    
    /**
     * Get array with mapping which property depends on other property  
     * @return array
     */
    public function GetDependsObjects() {
        return array(
            'PaymentType' => array(
                '_property_name' => 'MeanOfPaymentDetails',
                'BANK_WIRE' => '\P4M\MangoPayBundle\MangoPaySDK\Types\PayOutPaymentDetailsBankWire',
                // ...and more in future...
            )
        );
    }
    
    /**
     * Get array with read-only properties
     * @return array
     */
    public function GetReadOnlyProperties() {
        $properties = parent::GetReadOnlyProperties();
        array_push( $properties, 'PaymentType' );
        
        return $properties;
    }
}