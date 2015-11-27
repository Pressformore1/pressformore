<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for cards
 */
class ApiCards extends ApiBase {
    
    /**
     * Get card
     * @param int $cardId Card identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Card object returned from API
     */
    public function Get($cardId) {
        return $this->GetObject('card_get', $cardId, '\P4M\MangoPayBundle\MangoPaySDK\Card');
    }
}