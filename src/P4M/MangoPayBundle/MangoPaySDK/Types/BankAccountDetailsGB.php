<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Types;

/**
 * Class represents GB bank account type for in BankAccount entity
 */
class BankAccountDetailsGB extends Dto implements BankAccountDetails {
    
    /**
     * Account number 
     * @var string 
     */
    public $AccountNumber;
    
    /**
     * Sort code
     * @var string 
     */
    public $SortCode;
}