<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

 /**
 * Base filter object
 */
class FilterBase extends \P4M\MangoPayBundle\MangoPaySDK\Types\Dto {
    /**
     * Start date in unix format:
     * return only records that have CreationDate BEFORE this date
     * @var time 
     */
    public $BeforeDate;
    
    /**
     * End date in unix format:
     * return only records that have CreationDate AFTER this date
     * @var time 
     */
    public $AfterDate;
}