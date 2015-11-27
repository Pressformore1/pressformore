<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Filter for transaction list
 */
class FilterTransactions extends FilterBase {
    
    /**
     * TransactionStatus {CREATED, SUCCEEDED, FAILED}
     * @var string  
     */
    public $Status;
    
    /**
     * TransactionType {PAYIN, PAYOUT, TRANSFER}
     * @var string 
     */
    public $Type;
    
    /**
     * TransactionNature { REGULAR, REFUND, REPUDIATION }
     * @var string
     */
    public $Nature;
    
    /**
     * TransactionDirection {DEBIT, CREDIT}
     * @var string 
     */
    public $Direction;
}