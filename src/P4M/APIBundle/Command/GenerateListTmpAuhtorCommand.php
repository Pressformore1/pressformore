<?php
/**
 * Created by PhpStorm.
 * User: brynnlow
 * Date: 15/04/16
 * Time: 13:23
 */

namespace P4M\APIBundle\Command;


use P4M\CoreBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateListTmpAuhtorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:generate:listtmpauthor')->setAliases(['generate:list:tmpauthor'])
            ->setDescription('Génère la list d\'url avec auteur temporaire pour l\'api')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $file_root = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')
            ->createQueryBuilder('P')
            ->join('P.tempAuthor', 'T')
            ->where('T.id IS NOT NULL')
            ->getQuery()->getResult();
        foreach($posts as $post){
                $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
                $data[$post->getId()]['slug'] = $post->getSlug();
                $TempAuthor = $post->getTempAuthor();
                if($TempAuthor !== null ){
                    $data[$post->getId()]['author']['email'] = $TempAuthor->getEmail();
                    $data[$post->getId()]['author']['twitter'] = $TempAuthor->getTwitter();
                }
        }
        $list = json_encode($data);
        file_put_contents($file_root, $list);
        $output->writeln('La list des auteurs temporaires a bien été généré');

    }


}