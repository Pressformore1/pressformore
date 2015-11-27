<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for wallets
 */
class ApiWallets extends ApiBase {
    
    /**
     * Create new wallet
     * @param Wallet $wallet
     * @return \P4M\MangoPayBundle\MangoPaySDK\Wallet Wallet object returned from API
     */
    public function Create($wallet) {
        return $this->CreateObject('wallets_create', $wallet, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Wallet');
    }
    
    /**
     * Get wallet
     * @param type $walletId Wallet identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\Wallet Wallet object returned from API
     */
    public function Get($walletId) {
        return $this->GetObject('wallets_get', $walletId, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Wallet');
    }
    
    /**
     * Save wallet
     * @param type $wallet Wallet object to save
     * @return \P4M\MangoPayBundle\MangoPaySDK\Wallet Wallet object returned from API
     */
    public function Update($wallet) {
        return $this->SaveObject('wallets_save', $wallet, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Wallet');
    }

    /**
     * Get transactions for the wallet
     * @param type $walletId Wallet identifier
     * @param \P4M\MangoPayBundle\MangoPaySDK\Pagination $pagination Pagination object
     * @param \P4M\MangoPayBundle\MangoPaySDK\FilterTransactions $filter Object to filter data
     * @return \P4M\MangoPayBundle\MangoPaySDK\Transaction[] Transactions for wallet returned from API
     */
    public function GetTransactions($walletId, & $pagination = null, $filter = null) {
        return $this->GetList('wallets_alltransactions', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Transaction', $walletId, $filter);
    }
}