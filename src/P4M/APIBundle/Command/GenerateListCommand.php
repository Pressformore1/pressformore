<?php
/**
 * Created by PhpStorm.
 * User: brynnlow
 * Date: 15/04/16
 * Time: 13:23
 */

namespace P4M\APIBundle\Command;


use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\TempAuthor;
use P4M\CoreBundle\Entity\WantPressform;
use P4M\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:generate:list')->setAliases(['generate:list:api'])
            ->setDescription('Génère la list d\'url pour l\'api');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')->findAll();
        $updaterList = $container->get('api.updater_list');

        foreach ($posts as $post){
            $updaterList->updateList($post);
            $count++;
        }
        
        $updaterList->save();
        $output->writeln('Les listes ont été générer avec '. $count . ' articles');
    }


}