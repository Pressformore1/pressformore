<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMangoUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mango:create:users')
            ->setDescription('Cree des user mango ,pour chaque user du site. (avec wallets)')
//            ->addArgument('name', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
//            ->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $container = $this->getContainer();
        $mango = $container->get('p4_m_mango_pay.util');
        $userUtils = $container->get('p4mUser.user_utils');
        
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        
        
        $users = $userRepo->findAll();
        
        $transfertsSucceeded = 0;
        
        
        foreach ($users as $user)
        {
            
            $mangoUser= $user->getMangoUserNatural();
            if (null ==$mangoUser && $userUtils->isMangoUserAvailable($user))
            {
                $mangoUser = $mango->createUser($user);
    //            $user->setMangoUserNatural($mangoUser);
                
                $transfertsSucceeded ++;
            }
            
            if (null !==$mangoUser)
            {
                $wallets = $mango->getUserWallets($mangoUser);
        
                if (!count($wallets))
                {
                    $mango->createWallet($mangoUser);

                }
                
                if ($user->getProducerKey() !== null)
                {
                    $producerWallet = $mango->getProducerWallet($mangoUser);
                    
                    if ($producerWallet == null)
                    {
                        $mango->createWallet($mangoUser,'Producer');
                        $transfertsSucceeded ++;
                    }
                }
                

            }
                         
        }
        
        

        $output->writeln($transfertsSucceeded . ' mango users created with success');
    }
}