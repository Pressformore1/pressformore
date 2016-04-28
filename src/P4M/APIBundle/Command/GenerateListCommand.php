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

class GenerateListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:generate:list')
            ->setDescription('Génère la list d\'url pour l\'api');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $container = $this->getContainer();
        $file_root = $container->get('kernel')->getRootDir() . '/../web/api/list/Rewardlist.json';
        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')
            ->createQueryBuilder('P')
            ->where('P.author IS NOT NULL')
            ->getQuery()->getResult();
        $posts_tmpAuthor = $em->getRepository('P4MCoreBundle:Post')
            ->createQueryBuilder('P')
            ->join('P.tempAuthor', 'T')
            ->where('T.id IS NOT NULL')
            ->getQuery()->getResult();
        foreach ($posts as $post) {
            $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data[$post->getId()]['slug'] = $post->getSlug();
            $author = $post->getAuthor();
            if ($author !== null) {
                $data[$post->getId()]['author']['username'] = $author->getUsername();
                $data[$post->getId()]['author']['producerKey'] = $author->getProducerKey();
                $data[$post->getId()]['author']['picture'] = $author->getPicture()->getFile();
            }
            $count++;
        }
        foreach ($posts_tmpAuthor as $post) {
            $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data[$post->getId()]['slug'] = $post->getSlug();
            $TempAuthor = $post->getTempAuthor();
            if($TempAuthor !== null ){
                $data[$post->getId()]['author']['email'] = $TempAuthor->getEmail();
                $data[$post->getId()]['author']['twitter'] = $TempAuthor->getTwitter();
            }
            $count++;
        }

        $list = json_encode($data);
        file_put_contents($file_root, $list);
        $output->writeln($count.' articles on été généré dans la liste');

    }


}