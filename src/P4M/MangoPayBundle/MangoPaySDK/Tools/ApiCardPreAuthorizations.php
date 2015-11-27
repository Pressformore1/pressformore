<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for pre-authorization process
 */
class ApiCardPreAuthorizations extends ApiBase {

    /**
     * Create new pre-authorization object
     * @param \P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization $cardPreAuthorization PreAuthorization object to create
     * @return \P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization PreAuthorization object returned from API
     */
    public function Create($cardPreAuthorization) {
        return $this->CreateObject('preauthorization_create', $cardPreAuthorization, '\P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization');
    }
    
    /**
     * Get pre-authorization object
     * @param int $cardPreAuthorizationId PreAuthorization identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization Card registration  object returned from API
     */
    public function Get($cardPreAuthorizationId) {
        return $this->GetObject('preauthorization_get', $cardPreAuthorizationId, '\P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization');
    }
    
    /**
     * Update pre-authorization object
     * @param \P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization $preAuthorization PreAuthorization object to save
     * @return \P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization PreAuthorization object returned from API
     */
    public function Update($cardPreAuthorization) {
        return $this->SaveObject('preauthorization_save', $cardPreAuthorization, '\P4M\MangoPayBundle\MangoPaySDK\CardPreAuthorization');
    }
}