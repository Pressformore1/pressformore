<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for users
 */
class ApiUsers extends ApiBase {
    
    /**
     * Createa new user
     * @param UserLegal/UserNatural $user
     * @return UserLegal/UserNatural User object returned from API
     */
    public function Create($user) {
        
        $className = get_class($user);
        if ($className == 'P4M\MangoPayBundle\MangoPaySDK\Entities\UserNatural')
            $methodKey = 'users_createnaturals';
        elseif ($className == 'P4M\MangoPayBundle\MangoPaySDK\Entities\UserLegal')
            $methodKey = 'users_createlegals';
        else
            throw new \Exception('Wrong entity class for user');
        
        $response = $this->CreateObject($methodKey, $user);
        return $this->GetUserResponse($response);
    }
    
    /**
     * Get all users
     * @param \P4M\MangoPayBundle\MangoPaySDK\Pagination $pagination Pagination object
     * @return array Array with users
     */
    public function GetAll(& $pagination = null) {
        $usersList = $this->GetList('users_all', $pagination);
        
        $users = array();
        if (is_array($usersList)) {
            foreach ($usersList as $user) {
                array_push($users, $this->GetUserResponse($user));
            }
        }
        return $users;
    }
    
    /**
     * Get natural or legal user by ID
     * @param Int/GUID $userId User identifier
     * @return UserLegal/UserNatural User object returned from API
     */
    public function Get($userId) {
        
        $response = $this->GetObject('users_get', $userId);
        return $this->GetUserResponse($response);
    }
    
    /**
     * Get natural user by ID
     * @param Int/GUID $userId User identifier
     * @return UserLegal/UserNatural User object returned from API
     */
    public function GetNatural($userId) {
        
        $response = $this->GetObject('users_getnaturals', $userId);
        return $this->GetUserResponse($response);
    }
    
    /**
     * Get legal user by ID
     * @param Int/GUID $userId User identifier
     * @return UserLegal/UserNatural User object returned from API
     */
    public function GetLegal($userId) {
        
        $response = $this->GetObject('users_getlegals', $userId);
        return $this->GetUserResponse($response);
    }
    
    /**
     * Save user
     * @param UserLegal/UserNatural $user
     * @return UserLegal/UserNatural User object returned from API
     */
    public function Update($user) {
        
        $className = get_class($user);
        if ($className == 'P4M\MangoPayBundle\MangoPaySDK\UserNatural')
            $methodKey = 'users_savenaturals';
        elseif ($className == 'P4M\MangoPayBundle\MangoPaySDK\UserLegal')
            $methodKey = 'users_savelegals';
        else
            throw new Exception('Wrong entity class for user');
        
        $response = $this->SaveObject($methodKey, $user);
        return $this->GetUserResponse($response);        
    }
    
    /**
     * Create bank account for user
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount $bankAccount Entity of bank account object
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount Create bank account object
     */
    public function CreateBankAccount($userId, $bankAccount) {
        $type = $this->GetBankAccountType($bankAccount);
        return $this->CreateObject('users_createbankaccounts_' . $type, $bankAccount, '\P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount', $userId);
    }
    
    /**
     * Get all bank accounts for user
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\Pagination $pagination Pagination object
     * @return array Array with bank account entities
     */    
    public function GetBankAccounts($userId, & $pagination = null) {
        return $this->GetList('users_allbankaccount', $pagination, 'P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount', $userId);
    }
    
    /**
     * Get bank account for user
     * @param int $userId User Id
     * @param int $bankAccountId Bank account Id
     * @return \P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount Entity of bank account object
     */
    public function GetBankAccount($userId, $bankAccountId) {
        return $this->GetObject('users_getbankaccount', $userId, 'P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount', $bankAccountId);
    }
    
    /**
     * Get all wallets for user
     * @param int $userId User Id
     * @return \P4M\MangoPayBundle\MangoPaySDK\Wallet[] Array with obects returned from API
     */
    public function GetWallets($userId, & $pagination = null) {
        return $this->GetList('users_allwallets', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Wallet', $userId);
    }
        
    /**
     * Get all transactions for user
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\Pagination $pagination Pagination object
     * @param \P4M\MangoPayBundle\MangoPaySDK\FilterTransactions $filter Object to filter data
     * @return \P4M\MangoPayBundle\MangoPaySDK\Transaction[] Transactions for user returned from API
     */
    public function GetTransactions($userId, & $pagination = null, $filter = null) {
        
        return $this->GetList('users_alltransactions', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Transaction', $userId, $filter);
    }
    
    /**
     * Get all cards for user
     * @param int $userId User Id
     * @return \P4M\MangoPayBundle\MangoPaySDK\Card[] Cards for user returned from API
     */
    public function GetCards($userId, & $pagination = null) {
        return $this->GetList('users_allcards', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Entities\Card', $userId);
    }
    
    /**
     * Create new KYC document
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\KycDocument $kycDocument
     * @return \P4M\MangoPayBundle\MangoPaySDK\KycDocument Document returned from API
     */
    public function CreateKycDocument($userId, $kycDocument) {
        return $this->CreateObject('kyc_documents_create', $kycDocument, '\P4M\MangoPayBundle\MangoPaySDK\KycDocument', $userId);
    }
    
    /**
     * Get KYC document
     * @param int $userId User Id
     * @param string $kycDocumentId Document identifier
     * @return \P4M\MangoPayBundle\MangoPaySDK\KycDocument Document returned from API
     */
    public function GetKycDocument($userId, $kycDocumentId) {
        return $this->GetObject('kyc_documents_get', $userId, '\P4M\MangoPayBundle\MangoPaySDK\KycDocument', $kycDocumentId);
    }
    
    /**
     * Save KYC document
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\KycDocument $kycDocument Document to save
     * @return \P4M\MangoPayBundle\MangoPaySDK\KycDocument Document returned from API
     */
    public function UpdateKycDocument($userId, $kycDocument) {
        return $this->SaveObject('kyc_documents_save', $kycDocument, '\P4M\MangoPayBundle\MangoPaySDK\KycDocument', $userId);
    }
    
    /**
     * Create page for Kyc document
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\KycPage $page Kyc
     */
    public function CreateKycPage($userId, $kycDocumentId, $kycPage) {
        
        try{
            $this->CreateObject('kyc_page_create', $kycPage, null, $userId, $kycDocumentId);
        } catch (\P4M\MangoPayBundle\MangoPaySDK\ResponseException $exc) {
            if ($exc->getCode() != 204)
                throw $exc;
        }
    }
    
    /**
     * Create page for Kyc document from file
     * @param int $userId User Id
     * @param \P4M\MangoPayBundle\MangoPaySDK\KycPage $page Kyc
     */
    public function CreateKycPageFromFile($userId, $kycDocumentId, $file) {
        
        $filePath = $file;
        if (is_array($file)) {
            $filePath = $file['tmp_name'];
        }
        
        if (empty($filePath))
            throw new \P4M\MangoPayBundle\MangoPaySDK\Exception('Path of file cannot be empty');
        
        if (!file_exists($filePath))
            throw new \P4M\MangoPayBundle\MangoPaySDK\Exception('File not exist');
        
        $kycPage = new \P4M\MangoPayBundle\MangoPaySDK\KycPage();
        $kycPage->File = base64_encode(file_get_contents($filePath));
        
        if (empty($kycPage->File))
            throw new \P4M\MangoPayBundle\MangoPaySDK\Exception('Content of the file cannot be empty');
        
        $this->CreateKycPage($userId, $kycDocumentId, $kycPage);
    }
    
    /**
     * Get correct user object
     * @param object $response Response from API
     * @return UserLegal/UserNatural User object returned from API
     * @throws \P4M\MangoPayBundle\MangoPaySDK\Exception If occur unexpected response from API 
     */
    private function GetUserResponse($response) {
        
        if (isset($response->PersonType)) {
            
            switch ($response->PersonType) {
                case PersonType::Natural:
                    return $this->CastResponseToEntity($response, '\P4M\MangoPayBundle\MangoPaySDK\Entities\UserNatural');
                case PersonType::Legal:
                    return $this->CastResponseToEntity($response, '\P4M\MangoPayBundle\MangoPaySDK\Entities\UserLegal');
                default:
                    throw new Exception('Unexpected response. Wrong PersonType value');
            }            
        } else {
            throw new Exception('Unexpected response. Missing PersonType property');
        }
    }
    
    private function GetBankAccountType($bankAccount) {
        
        if (!isset($bankAccount->Details) || !is_object($bankAccount->Details))
            throw new Exception ('Details is not defined or it is not object type');
        
        $className = str_replace('P4M\MangoPayBundle\MangoPaySDK\Types\BankAccountDetails', '', get_class($bankAccount->Details));
        return strtolower($className);
    }
}
