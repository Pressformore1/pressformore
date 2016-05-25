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
        $file_root = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlist.json';
        $file_root_full = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
        $file_root_tmp = $container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
        if (file_exists($file_root))
            unlink($file_root);
        if (file_exists($file_root_full))
            unlink($file_root_full);
        if (file_exists($file_root_tmp))
            unlink($file_root_tmp);
        $em = $container->get('doctrine')->getManager();
        $posts = $em->getRepository('P4MCoreBundle:Post')
            ->createQueryBuilder('P')
            ->leftJoin('P.wantPressforms', 'W')
            ->leftJoin('W.user', 'S')
            ->leftJoin('S.picture', 'SP')
            ->addSelect('W')
            ->addSelect('S')
            ->addSelect('SP')
            ->where('P.author IS NOT NULL')
            ->getQuery()->getResult();
        $posts_tmpAuthor = $em->getRepository('P4MCoreBundle:Post')
            ->createQueryBuilder('P')
            ->join('P.tempAuthor', 'T')
            ->addSelect('T')
            ->leftJoin('P.wantPressforms', 'W')
            ->addSelect('W')
            ->leftJoin('W.user', 'WU')
            ->addSelect('WU')
            ->leftJoin('WU.picture', 'WUP')
            ->addSelect('WUP')
            ->where('T.id IS NOT NULL')
            ->getQuery()->getResult();

        // $posts_proposal = $em->getRepository('P4MCoreBundle:WantPressform')->findAll();

        $posts_proposal = $em->getRepository('P4MCoreBundle:WantPressform')
            ->createQueryBuilder('W')
            ->select('W')
            ->join('W.post', 'post')
            ->leftJoin('post.tempAuthor', 'T')
            ->where('T.id IS NULL')
            ->getQuery()->getResult();

        foreach ($posts as $post) {
            $data[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data[$post->getId()]['slug'] = $post->getSlug();
            $author = $post->getAuthor();
            if ($author !== null) {
                $data[$post->getId()]['status'] = 'COMPLETE';
                $data[$post->getId()]['author']['username'] = $author->getUsername();
                $data[$post->getId()]['author']['producerKey'] = $author->getProducerKey();
                $data[$post->getId()]['author']['picture'] = $author->getPicture()->getWebPath();
                foreach ($post->getPressforms() as $pressform){
                        $sender = $pressform->getSender();
                        $data[$post->getId()]['pressform'][$sender->getUsername()] = $sender->getPicture()->getWebPath();
                }
            }
            $count++;
        }
        foreach ($posts_tmpAuthor as $post) {
            $data_tmp[$post->getId()]['sourceUrl'] = $post->getSourceUrl();
            $data_tmp[$post->getId()]['slug'] = $post->getSlug();
            $TempAuthor = $post->getTempAuthor();
            if ($TempAuthor !== null) {
                $data_tmp[$post->getId()]['status'] = 'VALIDATE';
                $data_tmp[$post->getId()]['author']['email'] = $TempAuthor->getEmail();
                $data_tmp[$post->getId()]['author']['twitter'] = $TempAuthor->getTwitter();
            }
            if($post instanceof  Post){
                foreach ($post->getWantPressforms() as $wantPressform){
                        $data_tmp[$post->getId()]['wantpress'][$wantPressform->getUser()->getUsername()] = $wantPressform->getUser()->getPicture()->getWebPath();

                }
            }

            $count++;
        }

        foreach ($posts_proposal as $want) {
            $post_id = $want->getPost()->getId();
            $data_tmp[$post_id]['sourceUrl'] = $want->getPost()->getSourceUrl();
            $data_tmp[$post_id]['slug'] = $want->getPost()->getSlug();
            $data_tmp[$post_id]['status'] = 'NO VALIDATE';
            $data_tmp[$post_id]['wantpress'][$want->getUser()->getUsername()]['email'] = $want->getEmail();
            $data_tmp[$post_id]['wantpress'][$want->getUser()->getUsername()]['twitter'] = $want->getTwitter();
            $data_tmp[$post_id]['wantpress'][$want->getUser()->getUsername()]['picture'] = $want->getUser()->getPicture()->getWebPath();
            $count++;
        }
        $list = json_encode($data);
        $list_tmp = json_encode($data_tmp);
        $list_full = json_encode($data + $data_tmp);
        file_put_contents($file_root, $list);
        file_put_contents($file_root_tmp, $list_tmp);
        file_put_contents($file_root_full, $list_full);
        $output->writeln('Les listes ont été générer avec '. $count . ' articles
        '.$file_root .'
        ' .$file_root_full. '
        ' .$file_root_tmp);

    }


}