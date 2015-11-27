<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostDefineIframeAllowedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('post:define:iframe_allowed')
            ->setDescription('Rescanne les posts pour mettre à jour l\'auteur');
//            ->addOption(
//               'force',
//               null,
//               InputOption::VALUE_NONE,
//               'Si défini, On distribue tous les wallets actifs'
//            )
//            ->addArgument('name', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
//            ->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $container = $this->getContainer();
        
        $postUtils = $container->get('p4mCore.post_utils');
        
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $posts = $postRepo->findAll();
        
        $updated = 0;
        $scanned = 0;
        foreach ($posts as $post)
        {
            if ($post->getEmbed()== null || !strlen($post->getEmbed()))
            {
                $allowed = $postUtils->isIframeAllowed($post->getSourceUrl());
            
                if ($allowed != $post->getIframeAllowed())
                {
                    $updated ++;
                    $post->setIframeAllowed($allowed);
                    $em->persist($post);
                }
                $output->writeln(' Post scanned : allowed'.$allowed? 'ok' : 'false');
            }
            
            
            
            $scanned ++;
        }
            $em->flush();
            
         
        

        $output->writeln($scanned . ' scanned with success '.$updated.' postUpdated');
        exit();
    }
}