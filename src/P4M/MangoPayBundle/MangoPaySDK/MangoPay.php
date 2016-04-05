<?php

namespace P4M\MangoPayBundle\MangoPaySDK;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MangoPay
 *
 * @author Jona
 */

use P4M\MangoPayBundle\MangoPaySDK\Entities\CardRegistration;
use P4M\UserBundle\Entity\User;
use P4M\MangoPayBundle\MangoPaySDK\Entities\UserNatural;
use P4M\MangoPayBundle\MangoPaySDK\Entities\Wallet;
use P4M\MangoPayBundle\Entity\MangoUserNatural;
use P4M\MangoPayBundle\Entity\BankAccount;
use P4M\MangoPayBundle\Entity\BankAccountIBAN;
use P4M\MangoPayBundle\MangoPaySDK\Types\Money;

class MangoPay
{
    private $em;
    private $api;
   
    public function __construct(\Doctrine\ORM\EntityManager $em,  MangoPayApi $api)
    {
        $this->em = $em;
        $this->api = $api;
        
        $this->config();
    }
    
    private function config()
    {
//        die($_SERVER['DOCUMENT_ROOT']);

        $this->api->Config->ClientId = 'pfm-sandbox';
        $this->api->Config->ClientPassword = 'Qe9v3rZD0uYRMPdbK2Aky0esSLEkU5kmjagFuX5imc5jNRwAaO';
        $this->api->Config->TemporaryFolder = __DIR__.'/../../../../MP_tmp/';

        $this->api->Config->BaseUrl = 'https://api.sandbox.mangopay.com/';
    }
    public function getClientId(){
        return $this->api->Config->ClientId;
    }
    public function getBaseUrl(){
        return $this->api->Config->BaseUrl;
    }
    
    
    public function createUser(User $user)
    {
        $mangoUser = new MangoUserNatural();
        $mangoUser->hydrate($user);
        
        $naturalUser = $this->getAPIUser($mangoUser);
        
        
        try {

           
            $naturalUserResult = $this->api->Users->Create($naturalUser);
            
            $mangoUser->setMangoId($naturalUserResult->Id);
            $date = new \DateTime();
            $date->setTimestamp($naturalUserResult->CreationDate);
            $mangoUser->setCreationDate($date);
            $mangoUser->setTag($naturalUserResult->Tag);
            
            $this->em->persist($mangoUser);
            $this->em->flush();

            // display result
            // 
             
//            MangoPay\Logs::Debug('CREATED NATURAL USER', $naturalUserResult);


        } 
        catch (MangoPay\ResponseException $e)
        {

            MangoPay\Logs::Debug('MangoPay\ResponseException Code', $e->GetCode());
            MangoPay\Logs::Debug('Message', $e->GetMessage());
            MangoPay\Logs::Debug('Details', $e->GetErrorDetails());
            die();

        } 
        catch (MangoPay\Exception $e) 
        {

            MangoPay\Logs::Debug('MangoPay\Exception Message', $e->GetMessage());
            die();
        }
        
        
        return $mangoUser;
        
    }
    
    public function getAPIUser(MangoUserNatural $mangoUser)
    {
        $naturalUser = new UserNatural();
        $naturalUser->Email = $mangoUser->getUser()->getEmail();
        $naturalUser->FirstName = $mangoUser->getFirstName();
        $naturalUser->LastName = $mangoUser->getLastName();
        $naturalUser->Birthday =  $mangoUser->getBirthday();
        $naturalUser->Nationality = $mangoUser->getNationality();
        $naturalUser->CountryOfResidence = $mangoUser->getNationality();
        
        return $naturalUser;
        
        
    }
    
    public function getUserWallets(MangoUserNatural $mangoUser)
    {
        return $this->api->Users->GetWallets($mangoUser->getMangoId());
    }
    
    public function getProducerWallet(MangoUserNatural $mangoUser)
    {
        $wallets = $this->api->Users->GetWallets($mangoUser->getMangoId());
        foreach ($wallets as $wallet)
        {
            if ($wallet->Description == 'Producer')
            {
                return $wallet;
            }
            
        }
        
        return null;
        
    }
    
    public function getCustommerWallet(MangoUserNatural $mangoUser)
    {
        $wallets = $this->api->Users->GetWallets($mangoUser->getMangoId());
        foreach ($wallets as $wallet)
        {
            if ($wallet->Description == 'Base wallet')
            {
                return $wallet;
            }
            
        }
        
        return null;
        
    }
    
    public function createWallet(MangoUserNatural $mangoUser,$description="Base wallet" )
    {
        $Wallet = new Wallet();
        $Wallet->Owners = array($mangoUser->getMangoId());
        $Wallet->Description = $description;
        $Wallet->Currency = "EUR";
        
        
        return $this->api->Wallets->Create($Wallet);
    }
    
    public function getBankAccounts(MangoUserNatural $mangoUser)
    {
        $toReturn = array();
        $mangoAccounts = $this->api->Users->GetBankAccounts($mangoUser->getMangoId());
//        foreach ($mangoAccounts as $mangoAccount)
//        {
////            die(print_r($mangoAccount,true));
//            $bankAccount = new BankAccount();
//            $bankAccount->hydrate($mangoAccount);
//            $toReturn[] = $bankAccount;
//        }
//        
//        
//        return $toReturn;
//        print_r($mangoAccounts);
//        die();
        return $mangoAccounts;
        }
    public function getBankAccount(MangoUserNatural $mangoUser,$bankAccountId)
    {
        $mangoAccount = $this->api->Users->GetBankAccount($mangoUser->getMangoId(),$bankAccountId);
        $bankAccount = new BankAccount();
        $bankAccount->hydrate($mangoAccount);
        return $bankAccount;
    }
    
    public function createBankAccount(BankAccount $bankAccount)
    {
        //Build the parameters for the request
        $UserId = $bankAccount->getMangoUser()->getMangoId();
        $BankAccountMango = new \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount();
        $BankAccountMango->Type = $bankAccount->getType();
        
        switch ($BankAccountMango->Type)
        {
            case 'OTHER':
                $BankAccountMango->Details = new BankAccountDetailsOTHER();
                $BankAccountMango->Details->BIC = $bankAccount->getBIC();
                $BankAccountMango->Details->AccountNumber = $bankAccount->getAccountNumber();
            break;
        
            case 'IBAN':
                $BankAccountMango->Details = new BankAccountDetailsIBAN();
                $BankAccountMango->Details->BIC = $bankAccount->getBIC();
                $BankAccountMango->Details->IBAN = $bankAccount->getIBAN();
            break;
        
            case 'CA':
                $BankAccountMango->Details = new BankAccountDetailsCA();
                $BankAccountMango->Details->BankName = $bankAccount->getBankName();
                $BankAccountMango->Details->InstitutionNumber = $bankAccount->getInstitutionNumber();
                $BankAccountMango->Details->BranchCode = $bankAccount->getBranchCode();
                $BankAccountMango->Details->AccountNumber = $bankAccount->getAccountNumber();
            break;
        
            case 'CA':
                $BankAccountMango->Details = new BankAccountDetailsGB();
                $BankAccountMango->Details->SortCode = $bankAccount->getSortCode();
                $BankAccountMango->Details->AccountNumber = $bankAccount->getAccountNumber();
            break;
        
            case 'US':
                $BankAccountMango->Details = new BankAccountDetailsUS();
                $BankAccountMango->Details->Country = $bankAccount->getSortCode();
                $BankAccountMango->Details->AccountNumber = $bankAccount->getAccountNumber();
            break;
        }
        
        $BankAccountMango->OwnerName = $bankAccount->getOwnerName();
        $BankAccountMango->OwnerAddress = $bankAccount->getOwnerAddress();

        //Send the request
        $result = $this->api->Users->CreateBankAccount($UserId, $BankAccountMango);
        
        return $result;
    }
    public function createBankAccountIBAN(BankAccountIBAN $bankAccount)
    {
        //Build the parameters for the request
        $UserId = $bankAccount->getMangoUser()->getMangoId();
        $BankAccountMango = new \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount();
        $BankAccountMango->Type = 'IBAN';
        $BankAccountMango->TAg = $bankAccount->getTag();
        
        $BankAccountMango->Details = new \P4M\MangoPayBundle\MangoPaySDK\Types\BankAccountDetailsIBAN();
        $BankAccountMango->Details->BIC = $bankAccount->getBIC();
        $BankAccountMango->BIC = $bankAccount->getBIC();
        $BankAccountMango->Details->IBAN = $bankAccount->getIBAN();
        $BankAccountMango->IBAN = $bankAccount->getIBAN();
            
        $BankAccountMango->OwnerName = $bankAccount->getOwnerName();
        $BankAccountMango->OwnerAddress = $bankAccount->getOwnerAddress();

        //Send the request
        $result = $this->api->Users->CreateBankAccount($UserId, $BankAccountMango);
        
        return $result;
    }
    
    
    public function registerCard(\P4M\MangoPayBundle\Entity\CardRegistration $cardRegistration)
    {
        $mangoCardRegistration = new Entities\CardRegistration();
        $mangoCardRegistration->UserId = $cardRegistration->getMangoUser()->getMangoId();
        $mangoCardRegistration->Currency = $cardRegistration->getCurrency();  
        $mangoCardRegistration->Tag = $cardRegistration->getTag();
        $CreatedCardRegister = $this->api->CardRegistrations->Create($mangoCardRegistration);  
        
        return $CreatedCardRegister;
    }
    
    public function getCardRegistration($cardId,$_get)
    {
        $CardRegister = $this->api->CardRegistrations->Get($cardId);
        $CardRegister->RegistrationData = isset($_get['data']) ? 'data=' . $_get['data'] : 'errorCode=' . $_get['errorCode'];
        $UpdatedCardRegister = $this->api->CardRegistrations->Update($CardRegister);
        
        return $UpdatedCardRegister;
    }

    /**
     * Get registration data from Payline service
     * @param $cardRegistration
     * @return mixed
     * @throws \Exception
     */
    protected function getPaylineCorrectRegistartionData($cardRegistration) {

        /*
         ****** DO NOT use this code in a production environment - it is just for unit tests. In production you are not allowed to have the user's card details pass via your server (which is what is required to use this code here) *******
         */
        $data = 'data=' . $cardRegistration->PreregistrationData .
            '&accessKeyRef=' . $cardRegistration->AccessKey .
            '&cardNumber=4970100000000154' .
            '&cardExpirationDate=1224' .
            '&cardCvx=123';
        $curlHandle = curl_init($cardRegistration->CardRegistrationURL);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curlHandle);
        if ($response === false && curl_errno($curlHandle) != 0)
            throw new \Exception('cURL error: ' . curl_error($curlHandle));
        curl_close($curlHandle);
        return $response;
    }


    public function getUserCards(MangoUserNatural $mangoUser)
    {
        return $this->api->Users->GetCards($mangoUser->getMangoId());
    }
    public function getValidUserCards(MangoUserNatural $mangoUser)
    {
        $validCards = [];
        $cards = $this->api->Users->GetCards($mangoUser->getMangoId());
        
        foreach ($cards as $card)
        {
            if ($card->Validity == 'VALID' || $card->Validity == 'UNKNOWN')
            {
                $validCards[] = $card;
            }
        }
        
        return $validCards;
    }
    
    
    public function chargeWallet(MangoUserNatural $mangoUser,$cardId,$wallet,$returnUrl,$ammount)
    {
//        die($wallet->Id);
        $PayIn = new Entities\PayIn();
        $PayIn->CreditedWalletId = $wallet->Id;
        $PayIn->AuthorId = $mangoUser->getMangoId();
        
        $PayIn->PaymentType = "CARD";
        $PayIn->PaymentDetails = new Types\PayInPaymentDetailsCard();
        $PayIn->PaymentDetails->CardType = "CB_VISA_MASTERCARD";
        $PayIn->PaymentDetails->CardId = $cardId;
        $PayIn->DebitedFunds = new Types\Money();
        $PayIn->DebitedFunds->Currency = "EUR";
        $PayIn->DebitedFunds->Amount = $ammount;
        $PayIn->CreditedFunds = new Types\Money;
        $PayIn->CreditedFunds->Currency = "EUR";
        $PayIn->CreditedFunds->Amount = $ammount;
        $PayIn->Fees = new Types\Money();
        $PayIn->Fees->Currency = "EUR";
        $PayIn->Fees->Amount = 0;
        $PayIn->ExecutionType = "DIRECT";
        $PayIn->ExecutionDetails = new Types\PayInExecutionDetailsDirect();
        $PayIn->ExecutionDetails->SecureMode = "DEFAULT";
        $PayIn->ExecutionDetails->SecureModeReturnURL = $returnUrl;
        $PayIn->ExecutionDetails->CardId = $cardId;
        $PayIn->Tag = "blabla";
        
        $PayIn->CreditedUserId = $mangoUser->getMangoId();
        $PayIn->SecureModeReturnURL = $returnUrl;
        $PayIn->SecureMode = "DEFAULT";
        $PayIn->cardId = $cardId;

        
//        die('<pre>'.print_r($PayIn,true).'</pre>');
//
        //Send the request
        $result = $this->api->PayIns->Create($PayIn);
        
        return $result;
        
    }
    
     public function walletToWalletTransfert($debitedWallet,$creditedWallet,$ammount,$feePercent = 0)
    {
    
        $fee = floor($ammount/100 * $feePercent);
    
        
        
        //Build the parameters for the request
        $Transfer = new \P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer();
        $Transfer->AuthorId = $debitedWallet->Owners[0];
//        $Transfer->CreditedUserId = $receiver->getMangoId();
        $Transfer->DebitedFunds = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->DebitedFunds->Currency = "EUR";
        $Transfer->DebitedFunds->Amount = $ammount;
        $Transfer->Fees = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->Fees->Currency = "EUR";
        $Transfer->Fees->Amount = $fee;
        $Transfer->DebitedWalletID = $debitedWallet->Id;
        $Transfer->CreditedWalletId = $creditedWallet->Id;
        $Transfer->CreditedFunds = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->CreditedFunds->Currency = "EUR";
        $Transfer->CreditedFunds->Amount = $ammount-$fee;
        $Transfer->Tag = "blabla";


        //Send the request
        $result = $this->api->Transfers->Create($Transfer);
        
        return $result;
    }
    
    public function walletTransfert(MangoUserNatural $sender,MangoUserNatural $receiver,$ammount)
    {
        $fee = floor($ammount/10);
        
//        $senderWallets = $this->getUserWallets($sender);
//        $debitedWallet = $senderWallets[0];
        $debitedWallet = $this->getCustommerWallet($sender);
        
        
        $creditedWallet = $this->getProducerWallet($receiver);
       
        if ($creditedWallet === null)
        {
            return null;
        }
        
//        die('zet'.$ammount);
        
        
        //Build the parameters for the request
        $Transfer = new \P4M\MangoPayBundle\MangoPaySDK\Entities\Transfer();
        $Transfer->AuthorId = $sender->getMangoId();
//        $Transfer->CreditedUserId = $receiver->getMangoId();
        $Transfer->DebitedFunds = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->DebitedFunds->Currency = "EUR";
        $Transfer->DebitedFunds->Amount = $ammount;
        $Transfer->Fees = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->Fees->Currency = "EUR";
        $Transfer->Fees->Amount = $fee;
        $Transfer->DebitedWalletID = $debitedWallet->Id;
        $Transfer->CreditedWalletId = $creditedWallet->Id;
        $Transfer->CreditedFunds = new \P4M\MangoPayBundle\MangoPaySDK\Types\Money();
        $Transfer->CreditedFunds->Currency = "EUR";
        $Transfer->CreditedFunds->Amount = $ammount-$fee;
        $Transfer->Tag = "blabla";


        //Send the request
        $result = $this->api->Transfers->Create($Transfer);
        
        return $result;
    }
    
    function getUserTransactions(MangoUserNatural $user)
    {
        
        $UserId = $user->getMangoId();
        $Pagination = new Types\Pagination(1,100);
        

        $Filter = new Tools\FilterBase();
        $result = $this->api->Users->GetTransactions($UserId, $Pagination, $Filter);
        
        return $result;
    }
    
    public function createPayOut(MangoUserNatural $user,$wallet,$bankAccountId)
    {
        $PayOut = new Entities\PayOut();
        $PayOut->AuthorId = $user->getMangoId();
        $PayOut->DebitedWalletID = $wallet->Id;
        $PayOut->DebitedFunds = new Money();
        $PayOut->DebitedFunds->Currency = "EUR";
        $PayOut->DebitedFunds->Amount = $wallet->Balance->Amount;
        $PayOut->Fees = new Money();
        $PayOut->Fees->Currency = "EUR";
        $PayOut->Fees->Amount = 0;
        $PayOut->PaymentType = "BANK_WIRE";
        $PayOut->MeanOfPaymentDetails = new Types\PayOutPaymentDetailsBankWire();
        $PayOut->MeanOfPaymentDetails->BankAccountId = $bankAccountId;
        $PayOut->BankAccountId = $bankAccountId;
        $PayOut->BankWireRef = 'Pressformore pay out';


        //Send the request
        $result = $this->api->PayOuts->Create($PayOut);
        
        return $result;
    }
    
    public function getPayOuts(MangoUserNatural $user)
    {
        $toReturn = [];
        $transactions = $this->getUserTransactions($user);
        
        foreach ($transactions as $transaction)
        {
            if ($transaction->Type=="PAYOUT")
            {
                $toReturn[]=$transaction;
            }
        }
        
        return $toReturn;
    }


}
