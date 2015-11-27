<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DistributeWalletCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wallet:distribute')
            ->setDescription('Distribue l\'argent des portefeuilles en fonction des pressforms')
            ->addOption(
               'force',
               null,
               InputOption::VALUE_NONE,
               'Si défini, On distribue tous les wallets actifs'
            )
//            ->addArgument('name', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
//            ->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $container = $this->getContainer();
        $mango = $container->get('p4_m_mango_pay.util');
        
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $pressformRepo = $em->getRepository('P4MCoreBundle:Pressform');
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        
        if ($input->getOption('force')) 
        {
//            $criteria = new \Doctrine\Common\Collections\Criteria();
//            $criteria->where($criteria->expr()->gt('dateExpire', new \DateTime()));
//
//            $walletFills = $walletFillRepo->matching($criteria);
            $walletFills = $walletFillRepo->findBy(['expired'=>false]);
            
            
        }
        else
        {
            $walletFills = $walletFillRepo->findBy(['dateExpire'=>new \DateTime(),'expired'=>false]);
        }
        
        
//        $users = $userRepo->findAll();
        
        $transfertsSucceeded = 0;
        $walletFilled = 0;
        $walletFilledFailed = 0;
        
        
        
        foreach ($walletFills as $walletFill)
        {
            $pressforms = $walletFill->getUser()->getUnpayedSentPressforms();
            $nombrePressforms = count($pressforms);
           
            if ($nombrePressforms>0)
            {
                $mangoUser = $walletFill->getUser()->getMangoUserNatural();

                $wallet = $mango->getCustommerWallet($mangoUser);
                $balance = $wallet->Balance;
//                 die('w'.$balance->Amount);
                if ($balance->Amount)
                {
                    $balancePerPressform = $balance->Amount / $nombrePressforms;
                
//                    if (count($pressforms))
//                    {
                        foreach ($pressforms as $pressform)
                        {
                            $receiver = $pressform->getPost()->getAuthor();

        //                    die('test'.$receiver->getMangoUserNatural()->getMangoId());
                            if (null != $receiver)
                            {
                                $result = $mango->walletTransfert($mangoUser,$receiver->getMangoUserNatural(),$balancePerPressform);
//                               
                                if ($result->Status = 'SUCCEEDED')
                                {
                                    $transfertsSucceeded ++;
                                    $pressform->setPayed(true);
                                    $em->persist($pressform);

                                    
                                }
                                else
                                {
                                    $output->writeln(print_r($result,true));
                                    exit();
                                }

                            }

                        }
                        if ($walletFill->getRecurrent())
                        {
                            $result = $mango->chargeWallet($mangoUser,$walletFill->getCardId(),$wallet,'http://pressformore.com/my-account/#wallet',$walletFill->getAmount());
//                                        $result = $mango->chargeWallet($mangoUser,$walletFill->getCardId(),$wallet,'http://localhost/Press4More/0.2/web/app_dev.php/my-account/#wallet',$walletFill->getAmount());
                            if ($result->Status == 'SUCCEEDED')
                            {
                                $walletFilled ++;
                                $walletFill->update();
                                $em->persist($walletFill);
                            }
                            else
                            {
                                $walletFilledFailed ++;
                            }
                        }else
                        {
                            $output->writeln('wallet now expired');
                            $walletFill->setExpired(true);
                            $em->persist($walletFill);
                        }
//                    }
                    
                    
                }

//                die($user->getUsername());
//                die(print_r($wallets));
                
                    
            }
            else
            {
//                die('test');
                $walletFill->update('+1 day',false);
                 $output->writeln('wallet updated + 1day');
                $em->persist($walletFill);
            }
            
            
        }
        $em->flush();
        

        $output->writeln($transfertsSucceeded . ' executed with success '.$walletFilled.' wallets refilled '.$walletFilledFailed.' wallets failed to refill');
        exit();
    }
}