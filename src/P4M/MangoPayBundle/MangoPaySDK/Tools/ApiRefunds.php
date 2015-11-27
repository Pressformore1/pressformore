<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for refunds
 */
class ApiRefunds extends ApiBase {
   
    /**
     * Get refund object
     * @param string $refundId Refund Id
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Refund object returned from API
     */
    public function Get($refundId) {
        return $this->GetObject('refunds_get', $refundId, '\P4M\MangoPayBundle\MangoPaySDK\Refund');
    }
}