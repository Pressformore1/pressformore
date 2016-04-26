<?php
namespace P4M\APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateApiTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:generate:token')
        ->setDescription('Génère un client_id et un token pour l\'api')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(array('password', 'refresh_token'));
        $reponse = $clientManager->updateClient($client);
        $output->writeln('Le client et le token on été générer pour l\'api');


    }
}