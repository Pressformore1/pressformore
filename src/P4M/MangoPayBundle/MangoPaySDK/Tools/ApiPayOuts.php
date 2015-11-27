<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for pay-outs
 */
class ApiPayOuts extends ApiBase {
    
    /**
     * Create new pay-out
     * @param PayOut $payOut
     * @return \P4M\MangoPayBundle\MangoPaySDK\PayOut Object returned from API
     */
    public function Create($payOut) {
        $paymentKey = $this->GetPaymentKey($payOut);
        return $this->CreateObject('payouts_' . $paymentKey . '_create', $payOut, '\P4M\MangoPayBundle\MangoPaySDK\Entities\PayOut');
    }
    
    /**
     * Get pay-out object
     * @param $payOutId PayOut identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\PayOut Object returned from API
     */
    public function Get($payOutId) {
        return $this->GetObject('payouts_get', $payOutId, '\P4M\MangoPayBundle\MangoPaySDK\Entities\PayOut');
    }
    
    /**
     * Create refund for pay-out object
     * @param type $payOutId Pay-out identifier
     * @param \P4M\MangoPayBundle\MangoPaySDK\Refund $refund Refund object to create
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function CreateRefund($payOutId, $refund) {
        return $this->CreateObject('payouts_createrefunds', $payOutId, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Refund', $refund);
    }

    /**
     * Get refund for pay-out object
     * @param type $payOutId Pay-out identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function GetRefund($payOutId) {
        return $this->GetObject('payouts_getrefunds', $payOutId, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Refund');
    }
    
    private function GetPaymentKey($payOut) {
        
        if (!isset($payOut->MeanOfPaymentDetails) || !is_object($payOut->MeanOfPaymentDetails))
            throw new Exception('Mean of payment is not defined or it is not object type');
        
        $className = str_replace('P4M\MangoPayBundle\MangoPaySDK\Types\PayOutPaymentDetails', '', get_class($payOut->MeanOfPaymentDetails));
//        die(get_class($payOut->MeanOfPaymentDetails));
        return strtolower($className);
    }
}