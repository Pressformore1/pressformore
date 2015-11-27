<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Types;

/**
 * Class represents BankWire type for mean of payment in PayIn entity
 */
class PayInPaymentDetailsBankWire extends Dto implements PayInPaymentDetails {
    
    /**
     * Declared debited funds
     * @var \P4M\MangoPayBundle\MangoPaySDK\Types\Money
     */
    public $DeclaredDebitedFunds;

    /**
     * Declared fees
     * @var \P4M\MangoPayBundle\MangoPaySDK\Types\Money
     */
    public $DeclaredFees;

    /**
     * Bank account details
     * @var \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount
     */
    public $BankAccount;
    
    /**
     * Wire reference
     * @var string 
     */
    public $WireReference;
    
    /**
     * Get array with mapping which property is object and what type of object 
     * @return array
     */
    public function GetSubObjects() {
        return array(
            'DeclaredDebitedFunds' => '\P4M\MangoPayBundle\MangoPaySDK\Types\Money' ,
            'DeclaredFees' => '\P4M\MangoPayBundle\MangoPaySDK\Types\Money' ,
            'BankAccount' => '\P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount'
        );
    }
}
