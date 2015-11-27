<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WalletController extends Controller
{
    
    const WALLET_DEBITER = 'jona';
    
    public function listAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('P4MUserBundle:User');
        
        $entities = $repo->findAll();
        
        
        
        return $this->render('SKCMSAdminBundle:Page:wallet-list.html.twig',['entities'=>$entities]);
    }
    
    public function chargeAction($id, \Symfony\Component\HttpFoundation\Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('P4MUserBundle:User');
        
        $mango = $this->get('p4_m_mango_pay.util');
        $userUtils = $this->get('p4mUser.user_utils');
        
        $charger = $repo->findOneBy(['username'=>  self::WALLET_DEBITER]);
        $chargerMango = $charger->getMangoUserNatural();
        
        $targetUser = $repo->find($id);
        if (null === $targetUser)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Pas d\'user avec cet id');
        }
        //Mango User
        $targetMangoUser = $targetUser->getMangoUserNatural();
        if (null ==$targetMangoUser && $userUtils->isMangoUserAvailable($targetUser))
        {
            $targetMangoUser = $mango->createUser($targetUser);
        }
        else if(!$userUtils->isMangoUserAvailable($targetUser))
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User info pas complet');
        }
        
        //TargetWallets
        $targetWallet = $mango->getCustommerWallet($targetMangoUser);
        
        if (null === $targetWallet)
        {
            $targetWallet = $mango->createWallet($targetMangoUser);
           
        }
        //Debiter Wallet
        $debiterWallet = $mango->getCustommerWallet($chargerMango);
        
        
        //Transactions
        $transactions = $mango->getUserTransactions($chargerMango);
        
        $fromDebiterTransactions = [];
        $i = 1;
        foreach ($transactions as $transaction)
        {

            if ($transaction->CreditedUserId == $targetMangoUser->getMangoId())
            {
                $fromDebiterTransactions[] = $transaction;
            }
            $i++;
        }
        
        
        $form = $this->createFormBuilder()->add('amount')->getForm();
        
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $formData = $form->getData();
            $result = $mango->walletToWalletTransfert($debiterWallet,$targetWallet,$formData['amount']*100);
            $url =$this->generateUrl('sk_admin_charge_wallet');
            return $this->redirect($url);
        }
        
        return $this->render('SKCMSAdminBundle:Page:wallet-charge-form.html.twig',[
            'form'=>$form->createView(),
            'targetUser'=>$targetUser,
            'transactions'=>$fromDebiterTransactions,
            'debiterWallet'=>$debiterWallet
                
                ]);

    }
}
