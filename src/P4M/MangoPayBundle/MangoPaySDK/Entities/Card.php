<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * Card entity
 */
class Card extends EntityBase {
    
    /**
     * Expiration date
     * @var string 
     */
    public $ExpirationDate;
    
    /**
     * Alias
     * @var string 
     */
    public $Alias;
    
    /**
     * Card type
     * @var string 
     */
    public $CardType;
    
    /**
     * Product
     * @var string 
     */
    public $Product ;
    
    /**
     * Bank code
     * @var string 
     */
    public $BankCode;
    
    /**
     * Active
     * @var bool 
     */
    public $Active;
    
    /**
     * Currency
     * @var string 
     */
    public $Currency;
    
    /**
     * Validity
     * @var string 
     */
    public $Validity;
}