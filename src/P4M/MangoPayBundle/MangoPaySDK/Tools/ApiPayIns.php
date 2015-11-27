<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for pay-ins
 */
class ApiPayIns extends ApiBase {
    
    /**
     * Create new pay-in object
     * @param \P4M\MangoPayBundle\MangoPaySDK\PayIn $payIn \P4M\MangoPayBundle\MangoPaySDK\PayIn object
     * @return \P4M\MangoPayBundle\MangoPaySDK\PayIn Object returned from API
     */
    public function Create($payIn) {
        $paymentKey = $this->GetPaymentKey($payIn);
        $executionKey = $this->GetExecutionKey($payIn);
        
//        die($executionKey);
        return $this->CreateObject('payins_' . $paymentKey . '-' . $executionKey . '_create', $payIn, '\P4M\MangoPayBundle\MangoPaySDK\Entities\PayIn');
    }
    
    /**
     * Get pay-in object
     * @param $payInId Pay-in identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\PayIn Object returned from API
     */
    public function Get($payInId) {
        return $this->GetObject('payins_get', $payInId, '\P4M\MangoPayBundle\MangoPaySDK\PayIn');
    }
    
    /**
     * Create refund for pay-in object
     * @param type $payInId Pay-in identifier
     * @param \P4M\MangoPayBundle\MangoPaySDK\Refund $refund Refund object to create
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function CreateRefund($payInId, $refund) {
        return $this->CreateObject('payins_createrefunds', $refund, '\P4M\MangoPayBundle\MangoPaySDK\Refund', $payInId);
    }

    /**
     * Get refund for pay-in object
     * @param type $payInId Pay-in identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function GetRefund($payInId) {
        return $this->GetObject('payins_getrefunds', $payInId, '\P4M\MangoPayBundle\MangoPaySDK\Refund');
    }
    
    private function GetPaymentKey($payIn) {

        if (!isset($payIn->PaymentDetails) || !is_object($payIn->PaymentDetails))
            throw new \Exception ('Payment is not defined or it is not object type');
        

        $className = substr(get_class($payIn->PaymentDetails),  strrpos(get_class($payIn->PaymentDetails), 'PayInPaymentDetails')+strlen('PayInPaymentDetails'));

        return strtolower($className);
    }
    
    private function GetExecutionKey($payIn) {
        
        if (!isset($payIn->ExecutionDetails) || !is_object($payIn->ExecutionDetails))
            throw new \Exception ('Execution is not defined or it is not object type');
        
        $className = substr(get_class($payIn->ExecutionDetails),strrpos(get_class($payIn->ExecutionDetails),'PayInExecutionDetails')+strlen('PayInExecutionDetails') );
        return strtolower($className);
    }
}