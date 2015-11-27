<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\BackOfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BackOfficeController extends Controller
{
    
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $userUtil = $this->container->get('p4mUser.user_utils');
        
        
        $mangoUser= $user->getMangoUserNatural();
        if (null ==$mangoUser && $userUtil->isMangoUserAvailable($user)) //On a les infos mais pas encore de mangoUser, oin le cree et on cree son wallet.
        {
            $mangoUser = $mango->createUser($user);
            $wallets = $mango->createWallet($mangoUser);
        }
        
        
        
        
        $wallsRepo = $em->getRepository('P4MCoreBundle:Wall');
        $walls = $wallsRepo->findByUser($user);
        
        $notifRepo = $em->getRepository('P4MNotificationBundle:Notification');
        $notifications = $notifRepo->findByUserPaginated($user);
        
        $mangoInfos = [];
        
        if (null != $mangoUser) //On a les infos nécessaires pour se connceter à MangoPay
        {
            $mangoUserRepo = $em->getRepository('P4MMangoPayBundle:MangoUserNatural');
            $userRepo =  $em->getRepository('P4MUserBundle:User');
            $walletFillRepo  = $em->getRepository('P4MMangoPayBundle:WalletFill');
            
            $walletFill = $walletFillRepo->findActiveByUser($user);
            
            
            $cards = $mango->getValidUserCards($mangoUser);
//            die('i want carts');
            
            
            $wallet = $mango->getCustommerWallet($mangoUser);
            
            $request = $this->getRequest();
            $bankAccount = new \P4M\MangoPayBundle\Entity\BankAccount();
            $bankAccount->setMangoUser($mangoUser);
            $form = $this->createForm(new \P4M\MangoPayBundle\Form\BankAccountType(),$bankAccount);



            $card = new \P4M\MangoPayBundle\Entity\Card();
            $cardRegisterUrl = $this->generateUrl('p4_m_mango_pay_register_card');

            $cardForm = true;

            if ($request->getMethod() == 'POST') 
            {

              $form->bind($request);
//              $registerCardForm->bind($request);

                if ($form->isValid()) 
                {
                    $mangoAccount =$mango->createBankAccount($bankAccount);
                    $url = $this->generateUrl('p4_m_backoffice_money_bankAccount');
                    $this->redirect($url);
                }

                
                


            }
            
            $mangoTransactions = array_reverse($mango->getUserTransactions($mangoUser));
//            die('<pre>'.print_r($mangoTransactions,true).'</pre>');
            $transactions = [];
            $transferTransaction = new \stdClass();
            $transferTransaction->DebitedFunds = new \stdClass();
            $transferTransaction->DebitedFunds->Amount= 0;
            foreach ($mangoTransactions as $transaction)
            {
//                die($transaction->CreditedUserId);
                $creditedUser = null;
                if ($transaction->Type == 'TRANSFER')
                {
                    $transferTransaction->DebitedFunds->Amount+= $transaction->DebitedFunds->Amount;
                    $transferTransaction->CreationDate= $transaction->CreationDate;
                    $transferTransaction->Type= $transaction->Type;
                
                }
                else
                {
                    if ($transferTransaction->DebitedFunds->Amount >0)
                    {
                        $transactions[] = 
                        [
                            'type'=>$transferTransaction->Type,
                            'ammount'=>$transferTransaction->DebitedFunds->Amount/100,
                            'date'=>$transferTransaction->CreationDate,
                        ];
                    }
                    $transferTransaction->DebitedFunds->Amount= 0;
                    $transactions[] = 
                    [
                        'type'=>$transaction->Type,
                        'ammount'=>$transaction->DebitedFunds->Amount/100,
                        'date'=>$transaction->CreationDate,
    //                    'creditedUser'=>$creditedUser
                    ];
            }
            
                
                
                
            }
            
            $mangoInfos = 
            [
                'walletFill'=>$walletFill,
                'wallet'=>$wallet,
                'bankForm'=>$form->createView(),
                'cardForm'=>$cardForm,
                'cards'=>$cards,
                "cardRegisterUrl"=>$cardRegisterUrl,
                'transactions'=>$transactions
            ];
        }
        
        
        
        
        $params = array
        (
            'notifications'=>$notifications,
            'tilesEdit'=>true,
            'results'=>$walls,
            'user'=>$user
            
            
        );
        
        $params = array_merge($params,$mangoInfos);
        
        return $this->render('P4MBackofficeBundle::back-office.html.twig',$params);
    }
    
    
    
    
    
    
    
    public function dashboardReaderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $votesRepo = $em->getRepository('P4MCoreBundle:Vote');
        $viewsRepo = $em->getRepository('P4MTrackingBundle:PostView');
        $commentRepo = $em->getRepository('P4MCoreBundle:Comment');
        
        $user = $this->getUser();
        
        
        
        
        
        return $this->render('P4MBackofficeBundle:pages:dashboard-reader.html.twig');
    }
    
}
