<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for users
 */
class ApiClients extends ApiBase {
   
    /**
     * Get client data for Basic Access Authentication 
     * @param string $clientId Client identifier
     * @param string $clientName Beautiful name for presentation
     * @param string $clientEmail Client's email
     * @return \P4M\MangoPayBundle\MangoPaySDK\Client Client object
     */
    public function Create($clientId, $clientName, $clientEmail) {

        $urlMethod = $this->GetRequestUrl('authentication_base');
        $requestType = $this->GetRequestType('authentication_base');
        $requestData = array(
            'ClientId' => $clientId,
            'Name' => $clientName,
            'Email' => $clientEmail,
        );
        
        $rest = new RestTool(false, $this->_root);
        $response = $rest->Request($urlMethod, $requestType, $requestData);
        return $this->CastResponseToEntity($response, '\P4M\MangoPayBundle\MangoPaySDK\Client');
    }
}