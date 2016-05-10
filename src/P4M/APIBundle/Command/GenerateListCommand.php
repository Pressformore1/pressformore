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
        $this->setName('api:generate:list')
            ->setDescription('Génère la list d\'url pour l\'api');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $container = $this->getContainer();
        $file_root = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlist.json';
        $file_root_full = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
        $file_root_tmp = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
        unlink($file_root);
        unlink($file_root_full);
        unlink($file_root_tmp);
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

        $posts_proposal = $em->getRepository('P4MCoreBundle:WantPressform')->findAll();
        
        foreach ($posts as $post) {
            $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data[$post->getId()]['slug'] = $post->getSlug();
            $author = $post->getAuthor();
            if ($author !== null) {
                $data[$post->getId()]['status'] = 'COMPLETE';
                $data[$post->getId()]['author']['username'] = $author->getUsername();
                $data[$post->getId()]['author']['producerKey'] = $author->getProducerKey();
                $data[$post->getId()]['author']['picture'] = $author->getPicture()->getFile();
            }
            $count++;
        }
        foreach ($posts_tmpAuthor as $post) {
            $data_tmp[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data_tmp[$post->getId()]['slug'] = $post->getSlug();
            $TempAuthor = $post->getTempAuthor();
            if($TempAuthor !== null ){
                $data_tmp[$post->getId()]['statuss'] = 'VALIDATE';
                $data_tmp[$post->getId()]['author']['email'] = $TempAuthor->getEmail();
                $data_tmp[$post->getId()]['author']['twitter'] = $TempAuthor->getTwitter();
            }
            $count++;
        }
        $p=0;
        foreach ($posts_proposal as $want){
            if(!($want->getPost()->getAuthor() instanceof User) && !($want->getPost()->getTempAuthor() instanceof  TempAuthor)){
                $post_id = $want->getPost()->getId();
                $data_tmp[$post_id]['sourceUrl'] = $want->getPost()->getSourceUrl();
                $data_tmp[$post_id]['slug'] = $want->getPost()->getSlug();
                $data_tmp[$post_id]['status'] = 'NO VALIDATE';
                $data_tmp[$post_id]['wantpress'][$want->getUser()->getId()]['email'] = $want->getEmail();
                $data_tmp[$post_id]['wantpress'][$want->getUser()->getId()]['twitter'] = $want->getTwitter();
                $p++;
            }
        }
        $list = json_encode($data);
        $list_tmp = json_encode($data_tmp);
        $list_full = json_encode(array_merge($data, $data_tmp));
        file_put_contents($file_root, $list);
        file_put_contents($file_root_tmp, $list_tmp);
        file_put_contents($file_root_full, $list_full);
        $output->writeln($count.' articles on été généré dans la liste'. 'et '.$p);

    }


}