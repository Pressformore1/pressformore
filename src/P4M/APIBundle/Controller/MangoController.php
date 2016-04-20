<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use P4M\MangoPayBundle\Entity\BankAccountIBAN;
use P4M\MangoPayBundle\Entity\WalletFill;
use P4M\MangoPayBundle\Form\BankAccountIBANType;
use P4M\MangoPayBundle\MangoPaySDK\Entities\Card;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class MangoController extends FOSRestController
{

    private $response = array();

    public function __construct()
    {
        $this->response = [
            'message' => '',
            'status_codes' => ''
        ];
    }

    /**
     * @Rest\Post("/bank/createiban")
     * @param Request $request
     * @return Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Bank",
     *     description="add a post",
     *     requirements={
     *          {"name"="iban", "dataType"="string", "required"=true, "description"="iban of your account"},
     *          {"name"="bic", "dataType"="string", "required"=true, "description"="bic of your account"},
     *          {"name"="ownerName", "dataType"="string", "required"=true, "description"="owner name"},
     *          {"name"="ownerAddress", "dataType"="string", "required"=true, "description"="owner address"},
     *          {"name"="label", "dataType"="string", "required"=true, "description"="Named your account"},
     *     }
     * )
     */
    public function postBankCreateIbanAction(Request $request){
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        $mangoUser = $user->getMangoUserNatural();
        $bankAccount = new BankAccountIBAN();
        $bankAccount->setMangoUser($mangoUser);

        $form = $this->get('form.factory')->create(new BankAccountIBANType(), $bankAccount);
        $data = $request->request->all();
        if($this->CheckKey($data, 'IBAN')){
            if($form->submit($data)->isValid()){
                $mangoAccount = $mango->createBankAccountIBAN($bankAccount);
                $this->response['message'] = 'Account has been created';
                $this->response['status_codes'] = 200;
            }
            else{
                $this->response['message'] = 'Invalid parameters';
                $this->response['status_codes'] = 500;
            }
        }
        else{
            $this->response['message'] = 'Need some parameters';
            $this->response['status_codes'] = 501;
        }
    }

    /**
     * @Rest\Get("bank/loadbycard")
     * @Rest\View()
     * @return Response
     * @ApiDoc(
     *  method="GET",
     *  description="Get information for load a wallet",
     *  resource="Bank"
     * )
     */
    public function getLoadByCardAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        $mangoUser= $user->getMangoUserNatural();
        if(null === $mangoUser){
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'vous devez terminez votre inscription';
            return $this->response;
        }
        $registerCard = new \P4M\MangoPayBundle\Entity\CardRegistration();
        $registerCard->setCurrency('EUR');
        $registerCard->setTag('Main Card');
        $registerCard->setMangoUser($mangoUser);
        $CreatedCardRegister = $mango->registerCard($registerCard);
        $this->response['clientId'] = $mango->getClientId();
        $this->response['baseURL'] = $mango->getBaseUrl();
        $this->response['cardId'] = $CreatedCardRegister->Id;
        $this->response['cardRegistrationURL'] = $CreatedCardRegister->CardRegistrationURL;
        $this->response['preregistrationData'] = $CreatedCardRegister->PreregistrationData;
        $this->response['accessKey'] = $CreatedCardRegister->AccessKey;
        $this->response['status_codes'] = 200;
        return $this->response;
    }

    /**
     * @Rest\Post("bank/loadbycard")
     * @Rest\View()
     * @param Request $request
     * @return Response
     * @ApiDoc(
     *  description="Load a wallet",
     *  resource="Bank",
     *  parameters={
     *      {"name"="amount", "dataType"="integer", "required"=true, "description"="amount"},
     *      {"name"="preAuthorisation", "dataType"="boolean", "required"=true, "description"="collect this amount each month"},
     *      {"name"="cardId", "dataType"="integer", "required"=true, "description"="card Id"},
     *     }
     * )
     */
    public function postLoadByCardAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $mango = $this->container->get('p4_m_mango_pay.util');
        $cardId = $request->request->get('cardId');
        $mangoUser = $user->getMangoUserNatural();

        $amount = $request->request->get('amount');
        $amount *= 100;
        $preAuthorisation = $request->request->get('preAuthorisation');

        // DONT NEED THIS WEB JAVASCRIPT TOOLKIT !!!
        //$updatedCardRegister = $mango->getCardRegistration($cardId,$data);
//        if($updatedCardRegister->Status != 'VALIDATED' || !isset($updatedCardRegister->CardId)){
//            $this->response['status_codes'] = '501';
//            $this->response['message'] = 'Something is not Valid,';
//            return $this->response;
//        }
        $wallets = $mango->getUserWallets($mangoUser);
        $wallet = $wallets[0];
        $returnURL = $this->generateUrl("p4_m_backoffice_homepage",[],true).'#wallet';
        $result = $mango->chargeWallet($mangoUser,$cardId,$wallet,$returnURL,$amount);
        if($result->Status !== 'SUCCEEDED'){
            $this->response['message'] = $result->ResultMessage;
            $this->response['status_codes'] = $result->Status;
            return $this->response;
        }
        $walletFill = new WalletFill();
        $walletFill->setRecurrent($preAuthorisation);
        $walletFill->setUser($user);
        $walletFill->setCardId($cardId);
        $walletFill->setAmount($amount);
        $em->persist($walletFill);
        $em->flush();
        $this->response['message'] = 'Tout c\'est bien terminer';
        $this->response['status_codes'] = 200;
        return $this->response;
    }

    /**
     * @return Response
     * @Rest\View()
     * @ApiDoc(
     *  description="get Bank Info",
     *  resource="Bank",
     * )
     */
    public function getBankInfoAction(){
        $mango = $this->container->get('p4_m_mango_pay.util');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $mangoUser = $user->getMangoUserNatural();
        $walletFill =  $em->getRepository('P4MMangoPayBundle:WalletFill')->findOneBy(['user' => $user]);
        $cards = $mango->getUserCards($mangoUser);
        $last_card = null;
        foreach($cards as $card){
            if($card->Validity == 'VALID')
                $last_card = $card;
            elseif($last_card instanceof Card && $last_card->CreationDate < $card->CreationDate && $card->Validity == 'VALID')
                $last_card = $card;
        }
        $wallets = $mango->getUserWallets($mangoUser);
        $wallet = $wallets[0];
        $this->response['wallet'] = $wallet;
        $this->response['last_card'] = $last_card;
        $this->response['status_codes'] = 200;
        return $this->response;
    }

    private function CheckKey($data, $type = null){
        if($type == null)
            return false;
        switch($type){
            case 'IBAN':
                if(!array_key_exists('iban', $data))
                    return false;
                if(!array_key_exists('bic', $data))
                    return false;
                if(!array_key_exists('ownerAddress', $data))
                    return false;
                if(!array_key_exists('ownerName', $data))
                    return false;
                if(!array_key_exists('label', $data))
                    return false;
                break;
        }
        return true;
    }
}
