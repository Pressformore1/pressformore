<?php

namespace P4M\MangoPayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WalletController extends Controller
{
    public function chargeAction($cardId,$ammount,$preAuthorisation)
    {
        
        
        $user = $this->getUser();
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        $returnURL = $this->generateUrl("p4_m_backoffice_homepage",[],true).'#wallet';
        
        $wallets = $mango->getUserWallets($mangoUser);
        $wallet = $wallets[0];
        
        return $this->reallyCharge($mangoUser,$cardId,$wallet,$returnURL,$ammount*100,$preAuthorisation);
        
    }
    
    public function chargePostAction()
    {
        
        
        $user = $this->getUser();
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        
        
        $returnURL = $this->generateUrl("p4_m_backoffice_homepage",[],true).'#wallet';
        
        $wallet = $mango->getCustommerWallet($mangoUser);
        
//        die(print_r($wallet));
        
        $request = $this->getRequest();
        
        $cardId = $request->request->get('cardId');
        $preAuthorisation = false;
        if ($request->request->has('preAuthorisation'))
        {
            $preAuthorisation = true;
        }
        
        $ammount = intval(str_replace('€','', $request->request->get('ammount')))*100;
        
        return $this->reallyCharge($mangoUser,$cardId,$wallet,$returnURL,$ammount,$preAuthorisation);


    }
    
    private function reallyCharge($mangoUser,$cardId,$wallet,$returnURL,$ammount,$preAuthorisation = false)
    {
        
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $result = $mango->chargeWallet($mangoUser,$cardId,$wallet,$returnURL,$ammount);
        
        
        if ($result->Status == 'SUCCEEDED')
        {
            $walletFill = new \P4M\MangoPayBundle\Entity\WalletFill();
            $walletFill->setRecurrent($preAuthorisation);
            $walletFill->setUser($user);
            $walletFill->setCardId($cardId);
            $walletFill->setAmount($ammount);
            $em->persist($walletFill);
            $em->flush();
            $hash = '#wallet';
        }
        else
        {
            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add(
                    'chargeError',
                    $result->ResultMessage
                );
            $hash = '#wallet-charge';
//            die(print_r($result,true));
//            $this->getRequest()->s
        }
        

         return $this->redirect($this->generateUrl("p4_m_backoffice_homepage").$hash);
    }
}
