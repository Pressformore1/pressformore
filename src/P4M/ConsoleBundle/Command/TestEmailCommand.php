<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('email:test')
            ->setDescription('Test l\'envoi d\'emails')
            ->addArgument('email', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
//            ->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $container = $this->getContainer();
        
        $emailTo = 'jona303@gmail.com';
        if ($input->getArgument('email')) {
            $emailTo = $input->getArgument('email');
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Helo Email' )
            ->setFrom($this->getContainer()->getParameter('mailer_user'))
            ->setTo($emailTo)
            ->setBody('Just a test email to check that postfix is doing well', 'text/html')
            ;
        
        $test = $this->getContainer()->get('mailer')->send($message);
        
        

        $output->writeln($test . ' email');
    }
}