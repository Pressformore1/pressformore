<?php

namespace P4M\MangoPayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CardController extends Controller
{
    
    public function registerAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        
        $user = $this->getUser();
        
        $registerCard = new \P4M\MangoPayBundle\Entity\CardRegistration();
        $registerCard->setCurrency('EUR');
        $registerCard->setTag('Main Card');
        $registerCard->setMangoUser($user->getMangoUserNatural());
        
        
        $CreatedCardRegister = $mango->registerCard($registerCard);
        
        $request = $this->getRequest();
        
        $ammount = $request->request->get('ammount');
        $preAuthorisation = $request->request->get('preAuthorisation');
        
        $jsonResponse = 
        [
            'action'=>$CreatedCardRegister->CardRegistrationURL,
            'data'=>$CreatedCardRegister->PreregistrationData,
            'accessKeyRef'=>$CreatedCardRegister->AccessKey,
            'returnURL'=>$this->generateUrl('p4_m_mango_pay_register_card_return',['cardId'=>$CreatedCardRegister->Id,'ammount'=>$ammount,'preAuthorisation'=>$preAuthorisation],true)
        ];
        
//        die('<pre>'.print_r($jsonResponse).'</pre>');
        
        return new \Symfony\Component\HttpFoundation\Response(json_encode($jsonResponse));
    }
    
    
    public function registerReturnAction($cardId,$ammount,$preAuthorisation)
    {
        $request = $this->getRequest();
        
        $user = $this->getUser();
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        
         //update the card with the tokenised data
//        $CardRegister = $mango->getCardRegistration($cardId);
//        $CardRegister = $mangoPayApi->CardRegistrations->Get($_GET["CreatedCardRegisterId"]);
        
       
        $updatedCardRegister = $mango->getCardRegistration($cardId,$request->query->all());

         if ($updatedCardRegister->Status != 'VALIDATED' || !isset($updatedCardRegister->CardId)) {
             $errorCodes = 
                     [
                         '09101'=>'Username/Password is incorrect',
                         '09102'=>'Account is locked or inactive',
                         '02624'=>'Card expired',
                         '09104'=>'Client certificate is disabled',
                         '09201'=>'You do not have permissions to make this API call',
                         '02631'=>'Delay exceeded',
                         '02101'=>'Internal Error'
                     ];
             
             
             $session = $request->getSession();
             $session->getFlashBag()->add(
                    'chargeError',
                    $errorCodes[$request->query->get('errorCode')]
                );
            $hash = '#wallet-charge';
            return $this->redirect($this->generateUrl("p4_m_backoffice_homepage").$hash);
         }
         
//         die($cardId);
//         die($this->generateUrl("p4_m_mango_pay_wallet_charge",['cardId'=>$cardId,'ammount'=>$ammount,'preAuthorisation'=>$preAuthorisation]));
         
         return $this->redirect($this->generateUrl("p4_m_mango_pay_wallet_charge",['cardId'=>$updatedCardRegister->CardId,'ammount'=>$ammount,'preAuthorisation'=>$preAuthorisation]));

        //now for the actual preauthorization
        $CardPreAuthorization = new \MangoPay\CardPreAuthorization();
        $CardPreAuthorization->AuthorId = $testUserID;
        $CardPreAuthorization->DebitedFunds = new \MangoPay\Money();
        $CardPreAuthorization->DebitedFunds->Currency = "EUR";
        $CardPreAuthorization->DebitedFunds->Amount = $amountToAuthorise;
        $CardPreAuthorization->SecureMode = "DEFAULT";
        $CardPreAuthorization->CardId = $UpdatedCardRegister->CardId;
        $CardPreAuthorization->SecureModeReturnURL = "http://www.example.com/file.php";

        $CreatePreAuthorization = $mangoPayApi->CardPreAuthorizations->Create($CardPreAuthorization);
        echo "<pre>";
        var_dump($CreatePreAuthorization);//Analyse the request
        echo "</pre>";


        //and now to take the payment (this could obviously happen any time from now up to 7 days - it wouldnt be done immediately like this, that would just be silly)
        $PayIn = new \MangoPay\PayIn();
        $PayIn->CreditedWalletId = $mangoEscrowWalletID;
        $PayIn->AuthorId = $testUserID;
        $PayIn->PaymentType = "PREAUTHORIZED";
        $PayIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsPreAuthorized();
        $PayIn->PaymentDetails->PreauthorizationId = $CreatePreAuthorization->Id;
        $PayIn->DebitedFunds = new \MangoPay\Money();
        $PayIn->DebitedFunds->Currency = "EUR";
        $PayIn->DebitedFunds->Amount = $amountToAuthorise;
        $PayIn->Fees = new \MangoPay\Money();
        $PayIn->Fees->Currency = "EUR";
        $PayIn->Fees->Amount = 0;
        $PayIn->ExecutionType = "DIRECT";
        $PayIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsDirect();
        $PayIn->ExecutionDetails->ReturnURL = "http://www.example.com/file.php";
        $PayIn->ExecutionDetails->CardId = $UpdatedCardRegister->CardId; 


        $CreatePayIn = $mangoPayApi->PayIns->Create($PayIn);//Send the request

        echo "<pre>";
        var_dump($CreatePayIn);//Analyse the request
        echo "</pre>";
    }
}
