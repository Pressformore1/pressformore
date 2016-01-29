<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MoneyController
 *
 * @author Jona
 */
namespace P4M\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



class ProducerController extends Controller {
    
    
    public function keyAction()
    {
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        
        $mangoUser= $user->getMangoUserNatural();
        
        $userUtils = $this->get('p4mUser.user_utils');
        
        
        
//        die();
        
        
        if (null ==$mangoUser && $userUtils->isMangoUserAvailable($user))
        {
            $mangoUser = $mango->createUser($user);
        }
        
        if ($userUtils->isMangoUserAvailable($user))
        {
        $wallet = $mango->getProducerWallet($mangoUser);
        }
        else
        {
            $wallet = null;
        }
        
        if (null != $mangoUser)
        {
            $availiable = true;
        }
        else
        {
            $availiable = false;
        }
        
        
        $params = ['user'=>$user,'wallet'=>$wallet,'available'=>$availiable];

        return $this->render('P4MBackofficeBundle:pages/producer:producer-key.html.twig',$params);
        
        
        
        
    }
    
    public function pressformsAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        
        $mangoUser= $user->getMangoUserNatural();
        if (null ==$mangoUser)
        {
            $mangoUser = $mango->createUser($user);
        }

        $wallet = $mango->getProducerWallet($mangoUser);
        
        $pressformsRepo = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Pressform');
        $pressforms = $pressformsRepo->findByRecipient($user);
        
        $params = ['user'=>$user,'wallet'=>$wallet,'pressforms'=>$pressforms];

        return $this->render('P4MBackofficeBundle:pages/producer:producer-pressforms.html.twig',$params);
    }
    
    
    public function payOutsAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        
        $mangoUser= $user->getMangoUserNatural();
        if (null ==$mangoUser)
        {
            $mangoUser = $mango->createUser($user);
        }
        
        $wallet = $mango->getProducerWallet($mangoUser);
//        $transactions = $mango->getProducerWallet($mangoUser);
        $bankAccounts = $mango->getBankAccounts($mangoUser);
        
        $request = $this->getRequest();
        if ($request->getMethod()=='POST')
        {
            if ($request->request->get('bankAccount'))
            {
                $bankAccount = $mango->getBankAccount($mangoUser,$request->request->get('bankAccount'));
                if ($bankAccount->getOwnerName())
                {
                    $result = $mango->createPayOut($mangoUser,$wallet,$request->request->get('bankAccount'));
                    
                    if ($result->Status == 'CREATED')
                    {
                        $this->get('session')->getFlashBag()->add(
                            'success',
                            'Pay out successfully submitted'
                        );
                        $returnUrl = $this->generateUrl('p4_m_backoffice_producer_money');
                        return $this->redirect($returnUrl);
                    }
                    else
                    {
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            'A problem occured, please try again'
                        );
                    }
                }
                
            }
            
        }
        
        $payouts = $mango->getPayOuts($mangoUser);
        
        $payoutPending = false;
        foreach($payouts as $payout)
        {
            if ($payout->Status=="CREATED")
            {
                $payoutPending = true;
            }
        }
        
        
        $params = ['user'=>$user,'wallet'=>$wallet,'bankAccounts'=>$bankAccounts,'payouts'=>$payouts,'payoutPending'=>$payoutPending];

        return $this->render('P4MBackofficeBundle:pages/producer:producer-money.html.twig',$params);
        
        
        
        
    }
    
    public function activateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userUtil = $this->container->get('p4mUser.user_utils');
        $mango = $this->container->get('p4_m_mango_pay.util');
        $user = $this->getUser();
        
        $mangoUser= $user->getMangoUserNatural();
        if (null ==$mangoUser)
        {
            $mangoUser = $mango->createUser($user);
        }
        
        $mango->createWallet($mangoUser,'Producer');
        
        $user->setProducerEnabled(true);
        $user->setProducerKey(md5(1000000000 - $user->getId()));
        
        $em->persist($user);
        $em->flush();
        
        $returnUrl = $this->generateUrl('p4_m_backoffice_producer_key');
        
        return $this->redirect($returnUrl);
        
    }
    
    
}
