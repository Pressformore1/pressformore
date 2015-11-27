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
namespace P4M\BackOfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



class MoneyController extends Controller {
    
    
    public function indexAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        
        $user = $this->getUser();
        
        $mangoUser= $user->getMangoUserNatural();
        if (null ==$mangoUser)
        {
            
            $mangoUser = $mango->createUser($user);
//            $user->setMangoUserNatural($mangoUser);
        }
        
       
        
        $wallets = $mango->getUserWallets($mangoUser);
        
        if (!count($wallets))
        {
            $wallet = $mango->createWallet($mangoUser);
            $wallets[] = $wallet;
        }
        
//        die(print_r($wallets));
        $params = array('wallets'=>$wallets,'user'=>$user,'notifications'=>$user->getNotifications());
        return $this->render('P4MBackofficeBundle:pages/money:wallets.html.twig',$params);
        
        
        
        
    }
    
    
    
    public function accountsAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        
        $user = $this->getUser();
        $mangoUser = $user->getMangoUserNatural();
        
        $userBankAccounts = $mango->getBankAccounts($mangoUser);
        
        $params = array('accounts'=>$userBankAccounts,'user'=>$user);
        return $this->render('P4MBackofficeBundle:pages/money:bank-accounts.html.twig',$params);
        
    }
    
    
    public function accountCreateIbanAction()
    {
        $mango = $this->container->get('p4_m_mango_pay.util');
        $request = $this->getRequest();
        
        $user = $this->getUser();
        $mangoUser = $user->getMangoUserNatural();
        
        $bankAccount = new \P4M\MangoPayBundle\Entity\BankAccountIBAN();
        $bankAccount->setMangoUser($mangoUser);
        
        $form = $this->createForm(new \P4M\MangoPayBundle\Form\BankAccountIBANType(),$bankAccount);
        
        if ($request->getMethod() == 'POST') 
        {
            
          $form->bind($request);

            if ($form->isValid()) 
            {
                $mangoAccount =$mango->createBankAccountIBAN($bankAccount);
                $url = $this->generateUrl('p4_m_backoffice_producer_bankAccount');
                return $this->redirect($url);
            }
//            else
//            {
//                die($form->getErrorsAsString());
//            }
        }
        
        $params = array(
            'form'=>$form->createView(),
            'user'=>$user,
            'bankAccount'=>$bankAccount
        );
        
        return $this->render('P4MBackofficeBundle:pages/money:bank-account-form.html.twig',$params);
        
    }
    
}
