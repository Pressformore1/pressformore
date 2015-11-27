<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Types;

/**
 * Class represents Web type for execution option in PayIn entity
 */
class PayInExecutionDetailsDirect extends Dto implements PayInExecutionDetails {
    
    /**
     * SecureMode { DEFAULT, FORCE }
     * @var string 
     */
    public $SecureMode;
    
    /**
     * SecureModeReturnURL
     * @var string 
     */
    public $SecureModeReturnURL;
}