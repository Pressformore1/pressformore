<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * KYC document entity
 */
class KycDocument extends EntityBase {

    /**
     * Document type
     * @var \P4M\MangoPayBundle\MangoPaySDK\KycDocumentType 
     */
    public $Type;
    
    /**
     * Document status
     * @var \P4M\MangoPayBundle\MangoPaySDK\KycDocumentStatus 
     */
    public $Status;
    
    /**
     * Refused reason type
     * @var type string
     */
    public $RefusedReasonType;
    
    /**
     * Refused reason message
     * @var type string
     */
    public $RefusedReasonMessage;
}