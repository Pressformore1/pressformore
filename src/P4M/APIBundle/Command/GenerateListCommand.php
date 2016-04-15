<?php
/**
 * Created by PhpStorm.
 * User: brynnlow
 * Date: 15/04/16
 * Time: 13:23
 */

namespace P4M\APIBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:generate:list')
            ->setDescription('Génère la list d\'url pour l\'api')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost('sandbox.pressformore.com');
        $context->setScheme('http');
        $container = $this->getContainer();
        $file_root = $container->get('kernel')->getRootDir() . '/../web/api/Rewardlist.json';
        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')->createQueryBuilder('P')->where('P.author IS NOT NULL')->getQuery()->getResult();
        $router= $container->get('router');
        foreach($posts as $post){
            $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data[$post->getId()]['url'] = $router->generate('p4m_core_post', ['postSlug' => $post->getSlug()], true);
        }
        $list = json_encode($data);
        file_put_contents($file_root, $list);
        $output->writeln('La list a bien été généré');

    }


}