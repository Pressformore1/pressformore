<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for hooks and notifications
 */
class ApiHooks extends ApiBase {
    
    /**
     * Create new hook
     * @param Hook $hook
     * @return \P4M\MangoPayBundle\MangoPaySDK\Hook Hook object returned from API
     */
    public function Create($hook) {
        return $this->CreateObject('hooks_create', $hook, '\P4M\MangoPayBundle\MangoPaySDK\Hook');
    }
    
    /**
     * Get hook
     * @param type $hookId Hook identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Hook Wallet object returned from API
     */
    public function Get($hookId) {
        return $this->GetObject('hooks_get', $hookId, '\P4M\MangoPayBundle\MangoPaySDK\Hook');
    }
    
    /**
     * Save hook
     * @param type $hook Hook object to save
     * @return \P4M\MangoPayBundle\MangoPaySDK\Hook Hook object returned from API
     */
    public function Update($hook) {
        return $this->SaveObject('hooks_save', $hook, '\P4M\MangoPayBundle\MangoPaySDK\Hook');
    }
    
    /**
     * Get all hooks
     * @return \P4M\MangoPayBundle\MangoPaySDK\Hook[] Array with obects returned from API
     */
    public function GetAll(& $pagination = null) {
        return $this->GetList('hooks_all', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Hook');
    }
}