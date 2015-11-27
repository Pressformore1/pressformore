<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for transfers
 */
class ApiTransfers extends ApiBase {
    
    /**
     * Create new transfer
     * @param \P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer $transfer
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer Transfer object returned from API
     */
    public function Create($transfer) {
        return $this->CreateObject('transfers_create', $transfer, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer');
    }
    
    /**
     * Get transfer
     * @param type $transferId Transfer identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer Transfer object returned from API
     */
    public function Get($transfer) {
        return $this->GetObject('transfers_get', $transfer, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer');
    }
    
    /**
     * Create refund for transfer object
     * @param type $transferId Transfer identifier
     * @param \P4M\MangoPayBundle\MangoPaySDK\Refund $refund Refund object to create
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function CreateRefund($transferId, $refund) {
        return $this->CreateObject('transfers_createrefunds', $refund, '\P4M\MangoPayBundle\MangoPaySDK\Refund', $transferId);
    }
    
    /**
     * Get refund for transfer object
     * @param type $transferId Transfer identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Refund Object returned by REST API
     */
    public function GetRefund($transferId) {
        return $this->GetObject('transfers_getrefunds', $transferId, '\P4M\MangoPayBundle\MangoPaySDK\Refund');
    }
}
