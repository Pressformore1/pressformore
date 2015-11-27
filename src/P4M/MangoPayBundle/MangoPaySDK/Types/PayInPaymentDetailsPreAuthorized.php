<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Types;

/**
 * Class represents Web type for execution option in PayIn entity
 */
class PayInPaymentDetailsPreAuthorized extends Dto implements PayInPaymentDetails {
    
    /**
     * The ID of the Preauthorization object
     * @var string 
     */
    public $PreauthorizationId;
}