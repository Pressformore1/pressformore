<?php

namespace P4M\ConsoleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReapairPostPictureNameCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('post:repairPicturename')
            ->setDescription('Rescanne les posts pour mettre à jour l\'image si elle contient un espace')
            ->addArgument('postId', InputArgument::OPTIONAL, 'Quel post updater? ');
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
        
//        $postUtils = $container->get('p4mCore.post_utils');
        
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
//        $qb =$postRepo->createQueryBuilder('p');
//        $posts = $qb ->where($qb->expr()->like('p.localPicture',':space'))
//                    ->setParameter('space','% %')
//                    ->getQuery()
//                    ->getResult();
        if ($input->getArgument('postId')) {
            $posts = $postRepo->findBy(['id'=>$input->getArgument('postId')]);
        }
        else
        {
            $posts = $postRepo->findAll();
        }
        
//        $updated = 0;
        $scanned = 0;
        foreach ($posts as $post)
        {
            $scanned ++;
            
            
            $post->setLocalPicture($post->getLocalPicture().' ');
            
            
            $em->persist($post);
            $output->writeln($post->getId() . ' scanned with success '.$post->getLocalPicture());
        }
        
        
        
//        $dir = __DIR__.'../../../web/images/posts';
//        
//        $images = scandir($dir);
//        
//        foreach ($images as $image)
//        {
//            $oldName = $image;
//            $newName = $this->normalize($image);
//            
//            $post = $postRepo->createQueryBuilder('p')
//                        ->where('p.localPicture = '.$oldName)
//                        ->getQuery()
//                        ->getOneOrNullResult();
//            if ($post !== null)
//            {
//                $post->setLocalPicture('images/posts/'.$newName);
//                rename(__DIR__.'../../../web/images/posts/'.$oldName,__DIR__.'../../../web/images/posts/'.$newName);
//                $em->persist($post);
//                $updated ++;
//            }
//            
//        }
//        
        
        $em->flush();
        $output->writeln($scanned . ' scanned with success ');
        exit();
    }
    
    
}