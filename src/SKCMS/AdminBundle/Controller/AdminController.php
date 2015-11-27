<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $users = $userRepo->findAll();
        $producers = $userRepo->findBy(['producerEnabled'=>true]);
        $goodGuys = $userRepo->findGoodGuys();
        $lastMonthUsers = $userRepo->findLastMonthRegistration();
        
        $pressformRepo = $em->getRepository('P4MCoreBundle:Pressform');
        $pressforms = $pressformRepo->findAll();
        
        
        $postViewsRepo = $em->getRepository('P4MTrackingBundle:PostView');
        $postViews = $postViewsRepo->findLastMonthPostRead();
        
        $formattedPostViews = [];
        foreach ($postViews as $postView )
        {
            $date = $postView->getDatein()->format('m-d');
            if (array_key_exists($date, $formattedPostViews))
            {
                $formattedPostViews[$date] ++;
    }
            else
            {
                $formattedPostViews[$date] = 1;
            }
        }
    
        $walletRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        $walletFills = $walletRepo->findLastMonth();
        
        $formattedWalletFills =[];
                
        foreach ($walletFills as $walletFill )
        {
            $date = $walletFill->getDate()->format('m-d');
            if (array_key_exists($date, $formattedWalletFills))
            {
                $formattedWalletFills[$date] ++;
            }
            else
            {
                $formattedWalletFills[$date] = 1;
            }
        }
        
        $formattedNewUsers =[];
                
        foreach ($lastMonthUsers as $lastMonthUser )
        {
            $date = $lastMonthUser->getDateCreated()->format('m-d');
            if (array_key_exists($date, $formattedNewUsers))
            {
                $formattedNewUsers[$date] ++;
            }
            else
            {
                $formattedNewUsers[$date] = 1;
            }
        }
        
        
        
        
        $params = 
                [
                    'user'=>$users,
                    'producer'=>$producers,
                    'goodGuys'=>$goodGuys,
                    'pressforms'=>$pressforms,
                    'postViewData'=>$formattedPostViews,
                    'walletFillsData'=>$formattedWalletFills,
                    'newUsersData'=>$formattedNewUsers,
                    
                ];
        
        return $this->render('SKCMSAdminBundle:Page:index.html.twig',$params);
    }
    
    public function leftNavAction()
    {
//        $entities = 
//        [
//            [
//                'bundle'=>'SKCore',
//                'name'=>'SlideShowElement',
//                'beautyName'=>'Slideshow Elements'
//            ]
//        ];
//        
        $entities = $this->container->getParameter('skcms_admin.entities');
        $menuGroups = $this->container->getParameter('skcms_admin.menuGroups');
        
        return $this->render('SKCMSAdminBundle:Element/Nav:left-bar.html.twig',['entititesType'=>$entities,'menuGroups'=>$menuGroups]);
    }
    public function topNavAction()
    {
        $websitePath = $this->generateUrl('p4m_core_home');
        return $this->render('SKCMSAdminBundle:Element/Nav:top-bar.html.twig',['websitePath'=>$websitePath]);
    }
}
