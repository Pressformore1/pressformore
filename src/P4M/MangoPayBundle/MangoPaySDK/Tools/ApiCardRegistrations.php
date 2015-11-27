<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for card registrations
 */
class ApiCardRegistrations extends ApiBase {

    /**
     * Create new card registration
     * @param \P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration $cardRegistration Card registration object to create
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration Card registration object returned from API
     */
    public function Create($cardRegistration) {
        return $this->CreateObject('cardregistration_create', $cardRegistration, '\P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration');
    }
    
    /**
     * Get card registration
     * @param int $cardRegistrationId Card Registration identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration Card registration  object returned from API
     */
    public function Get($cardRegistrationId) {
        return $this->GetObject('cardregistration_get', $cardRegistrationId, '\P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration');
    }
    
    /**
     * Update card registration
     * @param \P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration $cardRegistration Card registration object to save
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration Card registration object returned from API
     */
    public function Update($cardRegistration) {
        return $this->SaveObject('cardregistration_save', $cardRegistration, '\P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration');
    }
}