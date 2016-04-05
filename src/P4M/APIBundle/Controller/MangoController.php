<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use P4M\MangoPayBundle\Entity\BankAccountIBAN;
use P4M\MangoPayBundle\Form\BankAccountIBANType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="bank",
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
     * @Rest\Post("bank/loadbycard")
     * @param Request $request
     * @return Response
     * @ApiDoc(
     *  method="GET",
     *  description="Load a wallet",
     *  resource="bank",
     *  parameters={
     *      {"name"="cardNumber", "dataType"="string", "required"=true, "description"="card number"},
     *      {"name"="cardExpirationDate", "dataType"="string", "required"=true, "description"="card expiration date"},
     *      {"name"="cardCvx", "dataType"="string", "required"=true, "description"="card Cvx"},
     *      {"name"="ammount", "dataType"="integer", "required"=true, "description"="ammount"},
     *      {"name"="preAuthorisation", "dataType"="boolean", "required"=true, "description"="collect this amount each month"},
     *     }
     * )
     */
    public function getChargeWalletAction(Request $request)
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        $mangoUser= $user->getMangoUserNatural();

        $registerCard = new \P4M\MangoPayBundle\Entity\CardRegistration();
        $registerCard->setCurrency('EUR');
        $registerCard->setTag('Main Card');
        $registerCard->setMangoUser($mangoUser);

        $CreatedCardRegister = $mango->registerCard($registerCard);
        $ammount = 5;  //$request->request->get('ammount');
        $preAuthorisation = false; //$request->request->get('preAuthorisation');
        $updatedCardRegister = null;
        if ($updatedCardRegister->Status != 'VALIDATED' || !isset($updatedCardRegister->CardId)){
            $this->response['message'] = $updatedCardRegister->Status;
            $this->response['status_codes'] = $updatedCardRegister->ResultCode;
        }
        else{
            $this->response['message'] = 'tout c\'est bien passer ?';
            $this->response['status_codes'] = 200;
        }



        $view = $this->view($this->response);
        return $this->handleView($view);
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
