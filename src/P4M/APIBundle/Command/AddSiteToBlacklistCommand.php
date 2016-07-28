<?php


namespace P4M\APIBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddSiteToBlacklistCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:blacklist:site')
            ->setDescription('Génère la list d\'url pour l\'api')
            ->addOption('url', 'u', InputOption::VALUE_REQUIRED, 'Quel site vous voulez traitez', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $blackListManager = $container->get('api.blacklist_manager');

        $site = $input->getOption('url');
        if(empty($site)){
            $output->writeln('Vous devez indiquez une url avec le paramètre -url= ou -u=');
        }
        else{
            $blackListManager->addSite($site)->save();
            $output->writeln('Le site '.$site.' a bien été ajouté a la liste');
        }
    }


}