<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace P4M\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\Tag;
use P4M\CoreBundle\Entity\Lang;
use P4M\CoreBundle\Entity\Pressform;

use P4M\PinocchioBundle\Event\PinocchioEvents;
use P4M\PinocchioBundle\Event\PostActivityEvent;

/**
 * Description of AjaxController
 *
 * @author Jona
 */
class AjaxController extends Controller 
{
    
    public function indexAction()
    {
        return new Response('indexAjax');
    }
    
    public function ajaxAction($page)
    {
        $salt = $this->container->getParameter('ajax_salt');
        $noKeyActions = array('updatePostView','reportAjaxError');
        
        if ($this->getRequest()->request->get('action') && $this->getRequest()->request->get('params') )
        {
            $jsonParams = $this->getRequest()->request->get('params');
            $key = $this->getRequest()->request->get('key');
            $action = $this->getRequest()->request->get('action');
            if (in_array($action, $noKeyActions) || hash('sha512',$jsonParams.$salt)==$key || hash('sha512',$action.$salt)==$key  )
            {
                return $this->processRequest($action,  json_decode($jsonParams),$page);
            }
            else
            {
                $this->throw403('wrong key');
            }
        }
        else
        {
            $this->throw403('you must set all required params');
        }
//        die('test');
        
    }
    
    private function processRequest($action,$params,$page)
    {
        switch ($action)
        {
            case 'follow':
                return $this->follow($params);
            break;
            case 'reportAjaxError':
                return $this->reportAjaxError($params);
            break;
            case 'unfollow':
                return $this->unfollow($params);
                break;
            case 'postComment' : 
                return $this->postComment($params);
            break;
            case 'wallComment' : 
                return $this->wallComment($params);
            break;
            case 'vote':
                return $this->postVote($params);
            break;
            case 'wallVote':
                return $this->wallVote($params);
            break;
            case 'commentVote':
                return $this->commentVote($params);
            break;
            case 'addPost':
                return $this->addPostForm($params);
            break;
            case 'addPostForm':
                return $this->addPostFormSubmit($params);
            break;
            case 'editPost':
                return $this->editPostForm($params);
            break;
            case 'editPostForm':
                return $this->editPostFormSubmit($params);
            break;
            case 'updatePostView':
                return $this->updatePostView($params);
            break;
            case 'banPost':
                return $this->banPost($params);
            break;
            case 'readPostLater':
                return $this->readPostLater($params);
            break;
            case 'unReadPostLater':
                return $this->unReadPostLater($params);
            break;
            case 'reportPost':
                return $this->reportPost($params);
            break;
            case 'confirmFlag':
                return $this->flaggedPost($params);
            break;
            case 'reportUser':
                return $this->reportUser($params);
            break;
            case 'confirmUserFlag':
                return $this->confirmUserFlag($params);
            break;
            case 'deleteWall':
                return $this->deleteWall($params);
            break;
            case 'reportWall':
                return $this->reportWall($params);
            break;
            case 'confirmWallFlag':
                return $this->confirmWallFlag($params);
            break;
            case 'followWall':
                return $this->followWall($params);
            break;
            case 'unfollowWall':
                return $this->unfollowWall($params);
            break;
            case 'deletePost':
                return $this->deletePost($params);
            break;
            case 'getProfilePosts':
                return $this->getProfilePosts($params,$page);
            break;
            case 'loadUserWalls':
                return $this->loadUserWalls($params,$page);
            break;
            case 'loadAccountWalls':
                return $this->loadAccountWalls($params,$page);
            break;
            case 'loadWallMembers':
                return $this->loadWallMembers($params);
            break;
            case 'loadUserCommunity':
                return $this->loadUserCommunity($params);
            break;
            case 'categoryToWall':
                return $this->categoryToWall($params);
            break;
            case 'tagToWall':
                return $this->tagToWall($params);
            break;
            case 'tagToWall':
                return $this->tagToWall($params);
            break;
            case 'getWallTags':
                return $this->getWallTags($params);
            break;
            case 'getWallCategories':
                return $this->getWallCategories($params);
            break;
            case 'addPostToHome':
                return $this->addPostToHome($params);
            break;
            case 'removePostFromHome':
                return $this->removePostFromHome($params);
            break;
            case 'quarantainePost':
                return $this->quarantainePost($params);
            break;
            case 'unQuarantainePost':
                return $this->unQuarantainePost($params);
            break;
            case 'loadTrendyPosts':
                return $this->loadTrendyPosts($params);
            break;
            case 'loadPressablePosts':
                return $this->loadPressablePosts($params);
            break;
            case 'loadBlogPosts':
                return $this->loadBlogPosts($params);
            break;
            case 'loadLegal':
                return $this->loadLegal($params);
            break;
            case 'loadResume':
                return $this->loadResume($params);
            break;
            case 'setNotificationsRead':
                return $this->setNotificationsRead($params);
            break;
            case 'createInvitation':
                return $this->createInvitation($params);
            break;
            case 'pressform':
                return $this->pressform($params);
            break;
            case 'unpressform':
                return $this->unpressform($params);
            break;
            case 'unpressformType':
                return $this->unpressformType($params);
            break;
            case 'loadWalletView':
                return $this->loadWalletView($params);
            break;
            case 'loadWalletForm':
                return $this->loadWalletForm($params);
            break;
            case 'changeRecurrentWalletAmount':
                return $this->changeRecurrentWalletAmount($params);
            break;
            case 'stopWalletRecurrency':
                return $this->stopWalletRecurrency($params);
            break;
            case 'startWalletRecurrency':
                return $this->startWalletRecurrency($params);
            break;
            case 'wantPressform':
                return $this->wantPressform($params);
            break;
            case 'wantPressformInfos':
                return $this->wantPressformInfos($params);
            break;
            case 'alertWalletEmpty':
                return $this->alertWalletEmpty($params);
            break;
            case 'loadUserNotifications':
                return $this->loadUserNotifications($params);
            break;
            default :
                $this->throw403('you action is not supported by this api');
            break;
        }
    }
    
    
    
    private function loadUserNotifications($params)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $notifRepo = $em->getRepository('P4MNotificationBundle:Notification');
        $notifications = $notifRepo->findByUserPaginated($user,$params->page);
        
        $view = $this->renderView('P4MBackofficeBundle:pages/notification:notification.html.twig',['notifications'=>$notifications]);
        
        
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>['view'=>$view,'target'=>$params->target]
                

            ];
        return new Response(json_encode($json_response));
    }
    private function alertWalletEmpty($params)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        if (isset($params->alertEmptyWallet) && $params->alertEmptyWallet == 'on')
        {
//            die('true');
            $user->setAlertWalletEmpty(true);
        }
        else
        {
//            die('false');
            $user->setAlertWalletEmpty(0);
        }
        
        $em->persist($user);
        $em->flush();
        
        
        
        
        $json_response = 
            [
                'status'=>1,
//                'callBack'=>$params->callBack,
                

            ];
        return new Response(json_encode($json_response));
    }
    private function wantPressformInfos($params)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        $wantPressformRepo = $em->getRepository('P4MCoreBundle:WantPressform');
        $wantPressform = $wantPressformRepo->findOneBy(['user'=>$user,'post'=>$post]);
        
        if (null !== $wantPressform)
        {
//          $request = $this->getRequest();
            $wantPressform->setEmail($params->email);
            $wantPressform->setTwitter($params->twitter);

            $em->persist($wantPressform);
            $em->flush();
        }
        
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                

            ];
        return new Response(json_encode($json_response));
    }
    private function wantPressform($params)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        $wantPressformRepo = $em->getRepository('P4MCoreBundle:WantPressform');
        $wantPressform = $wantPressformRepo->findOneBy(['user'=>$user,'post'=>$post]);
        if (null === $wantPressform)
        {
            $wantPressform = new \P4M\CoreBundle\Entity\WantPressform();
            $wantPressform->setUser($user);
            $wantPressform->setPost($post);

            $em->persist($wantPressform);
            $em->flush();
        }
        
        
        
        
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                

            ];
        return new Response(json_encode($json_response));
    }
    private function startWalletRecurrency($params)
    {
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        $wallet = $mango->getCustommerWallet($mangoUser);
        
        $em= $this->getDoctrine()->getManager();
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        $walletFill = $walletFillRepo->findActiveByUser($user);
        $walletFill->setRecurrent(true);
        $em->persist($walletFill);
        $em->flush();
        
        
        $viewParams =
        [
            'wallet'=>$wallet,
            'walletFill'=>$walletFill
        ];
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>$this->renderView('P4MBackofficeBundle:pages/money:wallet-form.html.twig',$viewParams)

            ];
        return new Response(json_encode($json_response));
    }
    private function stopWalletRecurrency($params)
    {
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        $wallet = $mango->getCustommerWallet($mangoUser);
        
        $em= $this->getDoctrine()->getManager();
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        $walletFill = $walletFillRepo->findActiveByUser($user);
        $walletFill->setRecurrent(false);
        $em->persist($walletFill);
        $em->flush();
        
        
        $viewParams =
        [
            'wallet'=>$wallet,
            'walletFill'=>$walletFill
        ];
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>$this->renderView('P4MBackofficeBundle:pages/money:wallet-form.html.twig',$viewParams)

            ];
        return new Response(json_encode($json_response));
    }
    private function changeRecurrentWalletAmount($params)
    {
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        $wallet = $mango->getCustommerWallet($mangoUser);
        
        $em= $this->getDoctrine()->getManager();
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        $walletFill = $walletFillRepo->findActiveByUser($user);
        $walletFill->setAmount($params->ammount*100);
        $em->persist($walletFill);
        $em->flush();
        
        
        $viewParams =
        [
            'wallet'=>$wallet,
            'walletFill'=>$walletFill
        ];
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>$this->renderView('P4MBackofficeBundle:pages/money:wallet-form.html.twig',$viewParams)

            ];
        return new Response(json_encode($json_response));
    }
    
    
    private function loadWalletView($params)
    {
        
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $em= $this->getDoctrine()->getManager();
        $postViewRepo = $em->getRepository('P4MTrackingBundle:PostView');
        $pressRepo = $em->getRepository('P4MCoreBundle:PressForm');
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        
        
        $postReadNumber = $postViewRepo->findLastMonthPostReadNumberByUser($user);
        if (null  !== $mangoUser)
        {
            $wallet = $mango->getCustommerWallet($mangoUser);
        
            
            $pressFormNumber = $pressRepo->findLastMonthNumberByUser($user);
            if ($pressFormNumber)
            {
                $userPressRatio = ($postReadNumber/($postReadNumber/$pressFormNumber))*100;
            }
            else
            {
                $userPressRatio = 0;
            }
            
            $userPressForms = $pressRepo->findBy(['sender'=>$user]);

            
        }
        else
        {
            $wallet = null;
            $postReadNumber =0;
            $userPressRatio=0;
            $pressFormNumber =0;
            $userPressForms = [];
        }
        
        $userNumber = $userRepo->findActiveUserNumber();
        
        $globalPostReadNumber = $postViewRepo->findLastMonthPostReadNumber();
        $globalPressFormNumber = $pressRepo->findLastMonthNumber();
        
        $averagePostReadNumber = $userNumber == 0 ? 0 : $globalPostReadNumber/$userNumber;
        if ($globalPressFormNumber)
        {
            $averagePressRatio = ($averagePostReadNumber/($averagePostReadNumber/($globalPressFormNumber/$userNumber)))*100;
        }
        else
        {
            $averagePressRatio = 0;
        }
        
        $averageWalletBalance = $walletFillRepo->findAverageBalance()/100;
        
        $pressformersNumber = $pressRepo->findLastMonthPressformersNumber();
        $pressedContentNumber = $pressRepo->findLastMonthPressedContentNumber();
        
        
        $renderParams = 
        [
            'averagePostReadNumber'=>$averagePostReadNumber,
            'averagePressRatio'=>$averagePressRatio,
            'averageWalletBalance'=>$averageWalletBalance,
            'userPostReadNumber'=>$postReadNumber,
            'pressFormNumber'=>$pressFormNumber,
            'userPressRatio'=>$userPressRatio,
            'wallet'=>$wallet,
            'pressformersNumber'=>$pressformersNumber,
            'pressedContentNumber'=>$pressedContentNumber,
            'userPressForms'=>$userPressForms,
            'user'=>$user
        ];
        
        $view = $this->renderView('P4MBackofficeBundle:pages/money:wallet-graph.html.twig',$renderParams);
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>$view

            ];
        
        return new Response(json_encode($json_response));
    }
    
    
    private function loadWalletForm($params)
    {
        $user = $this->getUser();
        if (null === $user)
        {
            $this->throw403('This area is for logged user');
        }
        
        $mango = $this->container->get('p4_m_mango_pay.util');
        $mangoUser= $user->getMangoUserNatural();
        
        $wallet = $mango->getCustommerWallet($mangoUser);
        
        $em= $this->getDoctrine()->getManager();
        $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
        $walletFill = $walletFillRepo->findActiveByUser($user);
        
        
        $viewParams =
        [
            'wallet'=>$wallet,
            'walletFill'=>$walletFill
        ];
        
        
        $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>$this->renderView('P4MBackofficeBundle:pages/money:wallet-form.html.twig',$viewParams)

            ];
        return new Response(json_encode($json_response));
    }
    
    private function unpressformType($params)
    {
        $user = $this->getUser();
        
       $em = $this->getDoctrine()->getManager();
       $postRepo = $em->getRepository('P4MCoreBundle:Post');
       $unpressformRepo = $em->getRepository('P4MCoreBundle:Unpressform');
       $unpressformTypeRepo = $em->getRepository('P4MCoreBundle:UnpressformType');
       
       
       $post = $postRepo->find($params->postId);
       $unpressform = $unpressformRepo->findOneBy(['post'=>$post,'user'=>$user]);
       $type = $unpressformTypeRepo->find($params->typeId);
       
       if (null !== $unpressform && null !== $type)
       {
            $unpressform->setType($type);
            $em->persist($unpressform);

            $em->flush();
           
            $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>['id'=>$params->typeId],
            ];
            
            
        }
        else
        {
            $json_response = 
            [
                'status'=>0,
                
            ];
            
            
        }
       
        return new Response(json_encode($json_response));
       
    }
    private function unpressform($params)
    {
        $user = $this->getUser();
        
       $em = $this->getDoctrine()->getManager();
       $postRepo = $em->getRepository('P4MCoreBundle:Post');
       $pressformRepo = $em->getRepository('P4MCoreBundle:Pressform');
       
       $post = $postRepo->find($params->postId);
       
//       $pressform = $pressformRepo->findOneBy(['post'=>$post,'sender'=>$user]);
       $pressform = $pressformRepo->findOneBy(['post'=>$post,'sender'=>$user,'payed'=>false]);
       
       if (null !== $pressform)
       {
            $unpress = new \P4M\CoreBundle\Entity\Unpressform();
            $unpress->setPost($post);
            $unpress->setUser($user);
            $em->persist($unpress);

            $em->remove($pressform);
           
            $em->flush();
           
            $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>'',
            ];
            
            
        }
        else
        {
            $json_response = 
            [
                'status'=>0,
                
            ];
            
            
        }
       
        return new Response(json_encode($json_response));
       
    }
    private function pressform($params)
    {
        $user = $this->getUser();
        
       $em = $this->getDoctrine()->getManager();
       $postRepo = $em->getRepository('P4MCoreBundle:Post');
       $pressformRepo = $em->getRepository('P4MCoreBundle:Pressform');
       $unpressformRepo = $em->getRepository('P4MCoreBundle:Unpressform');
       
       $post = $postRepo->find($params->postId);
       
       $pressform = $pressformRepo->findOneBy(['post'=>$post,'sender'=>$user,'payed'=>false]);
       
       
       if (null === $pressform)
       {
           $unpressform = $unpressformRepo->findOneBy(['post'=>$post,'user'=>$user]);
           if (null !== $unpressform)
           {
               $em->remove($unpressform);
           }
           
           
           $pressform = new Pressform();
           $pressform->setSender($user);
           $pressform->setPost($post);
           
           $em->persist($pressform);
           
           $em->flush();
           
            $json_response = 
            [
                'status'=>1,
                'callBack'=>$params->callBack,
                'callBackParams'=>'',
            ];
        }
        else
        {
//            die($pressform->getDate());
            $json_response = 
            [
                'status'=>0
                
            ];
            
            
        }
       
        return new Response(json_encode($json_response));
       
    }
    
    
    private function createInvitation($params)
    {
        $em = $this->getDoctrine()->getManager();
        
        $invitRepo = $em->getRepository('P4MUserBundle:Invitation');
        $existInvit = $invitRepo->findOneByEmail($params->email);
        
        if (null === $existInvit)
        {
            $invitation = new \P4M\UserBundle\Entity\Invitation();
            $invitation->setEmail($params->email);
            $invitation->setMaxIterations(1);
            
            $em->persist($invitation);
            $em->flush();
            
            $message = "Thank you for your interest. We will keep you updated about any of our progress. You'll receive another email with an invitation-token as soon as we widen the access. "
                    . "<br />All the best, <br />Pressformore team";
        }
        else
        {
            $message = "Don't be too enthusiastic, your e-mail is already in our database ;)";
        }
        
        $json_response = 
        [
            'status'=>1,
            'action'=>'createInvitation',
//            'callBack'=>$params->callBack,
            'callBackParams'=>'',
            'data'=>
            [
                'message'=>$message
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    private function setNotificationsRead($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $notificationRepo = $em->getRepository('P4MNotificationBundle:Notification');
        if (count((array)$params->notifications))
        {
            $notifs = $notificationRepo->findById((array)$params->notifications);
        
            foreach ($notifs as $notif)
            {
                if ($notif->getUser() === $user)
                {
                    $notif->setRead(true);
                    $em->persist($notif);
                }
            }

            $em->flush();

        }
        
        
            
        $json_response = 
        [
            'status'=>1,
            'action'=>'homeRefresh',
            'callBack'=>$params->callBack,
            'callBackParams'=>'',
            'data'=>
            [
                
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    
    private function loadResume($params)
    {
        $user = $this->getUser();
       
        
        $view = $this->renderView('P4MBlogBundle:types:simple-iframe.html.twig',["iframeUrl"=>'http://blog.pressformore.com/home/index.html']);
            
        $json_response = 
        [
            'status'=>1,
            'action'=>'homeRefresh',
            'data'=>
            [
                'content'=>$view
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    private function loadTrendyPosts($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        

        
        
        $view = $this->renderView('P4MBlogBundle:types:simple-iframe.html.twig',["iframeUrl"=>'http://blog.pressformore.com/']);
            
        $json_response = 
        [
            'status'=>1,
            'action'=>'homeRefresh',
            'data'=>
            [
                'content'=>$view
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    private function loadPressablePosts($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
//        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
//        $posts = $postRepo->getPressablePosts();
//        $criteria = new \Doctrine\Common\Collections\Criteria();
//        $criteria->where($criteria->expr()->neq('author',null));

        $posts = $postRepo->matching($criteria);
        
//        die('debug');
        
        
        $params = array
            (
                'user'=>$user,
                'posts'=> $posts ,
                'nombrePages'=>1,
                'postPath'=>'',
                'filters'=>''
                
            );
        
        $response = array
            (
                'status'=>1,
                'action'=>'homeRefreshPosts',

                'data'=>array
                (
                    'content'=>$this->renderView('P4MCoreBundle:Wall:post-container.html.twig',$params),
                    'pagination'=>$this->renderView('P4MCoreBundle:Wall:pagination.html.twig',$params),


                )
        );
        
//        return $this->render('P4MCoreBundle:Wall:post-container.html.twig',$params);
        return new Response(json_encode($response));    
    }
    private function loadBlogPosts($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $view = $this->renderView('P4MBlogBundle:types:simple-iframe.html.twig',["iframeUrl"=>'http://blog.pressformore.com/']);
            
        $json_response = 
        [
            'status'=>1,
            'action'=>'homeRefresh',
            'data'=>
            [
                'content'=>$view
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    
    private function loadLegal($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $view = $this->renderView('P4MBlogBundle:types:simple-iframe.html.twig',["iframeUrl"=>'http://blog.pressformore.com/conditions-generales-dutilisation/']);
            
        $json_response = 
        [
            'status'=>1,
            'action'=>'homeRefresh',
            'data'=>
            [
                'content'=>$view
            ]
        ];
        
        return new Response(json_encode($json_response));
    }
    
    private function addPostToHome($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $post = $postRepo->find($params->postId);
            $post->setShowOnHome(true);
            $em->persist($post);
            $em->flush();
            

                $response = array
                (
                    'status'=>1,
                    'action'=>'postAddedToHome',

                    
                );

                if (isset($params->callBack))
                {
                    $response['callBack']= $params->callBack;
                }

                return new Response(json_encode($response));
            
        }
        else
        {
            $this->throw403("you don't have the privileges to do this");
        }
        
        
        
       
        
    }
    private function removePostFromHome($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $post = $postRepo->find($params->postId);
            $post->setShowOnHome(false);
            $em->persist($post);
            $em->flush();
            

                $response = array
                (
                    'status'=>1,
                    'action'=>'postAddedToHome',

                    
                );

                if (isset($params->callBack))
                {
                    $response['callBack']= $params->callBack;
                }

                return new Response(json_encode($response));
            
        }
        else
        {
            $this->throw403("you don't have the privileges to do this");
        }
        
        
        
       
        
    }
    private function quarantainePost($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $post = $postRepo->find($params->postId);
            $post->setQuarantaine(true);
            $em->persist($post);
            $em->flush();
            

                $response = array
                (
                    'status'=>1,
                    'action'=>'postQuarantaine',

                    
                );

                if (isset($params->callBack))
                {
                    $response['callBack']= $params->callBack;
                }

                return new Response(json_encode($response));
            
        }
        else
        {
            $this->throw403("you don't have the privileges to do this");
        }
        
        
        
       
        
    }
    private function unQuarantainePost($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $post = $postRepo->find($params->postId);
            $post->setQuarantaine(false);
            $em->persist($post);
            $em->flush();
            

                $response = array
                (
                    'status'=>1,
                    'action'=>'postQuarantaine',

                    
                );

                if (isset($params->callBack))
                {
                    $response['callBack']= $params->callBack;
                }

                return new Response(json_encode($response));
            
        }
        else
        {
            $this->throw403("you don't have the privileges to do this");
        }
        
        
        
       
        
    }
    private function getWallCategories($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $tagsRepo = $em->getRepository('P4MCoreBundle:Category');
        $tags = $tagsRepo->findAll();
        
        $twigParams = array('results'=>$tags);
        if (null !== $user)
        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
            
            if (isset($params->callBack))
            {
                $response['callBack']= $params->callBack;
            }
            
            return new Response(json_encode($response));
        }
       
        
    }
    
    private function getWallTags($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $tagsRepo = $em->getRepository('P4MCoreBundle:Tag');
        $tags = $tagsRepo->findPaginated();
//        $tags = $tagsRepo->createQueryBuilder('t')
//                            ->addSelect('COUNT(p.id) as HIDDEN nPosts')
//                            ->join('t.posts', 'p')
//                            ->orderBy("nPosts", 'DESC')
//                            ->setMaxResults(200)
//                            ->getQuery()
//                            ->getResult();
                            
        $twigParams = array('results'=>$tags);
        if (null !== $user)
        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
            
            if (isset($params->callBack))
            {
                $response['callBack']= $params->callBack;
            }
            
            return new Response(json_encode($response));
        }
       
        
    }
    
    private function tagToWall($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $tagRepo = $em->getRepository('P4MCoreBundle:Tag');
        $tag = $tagRepo->find($params->tagId);
        
        
        if ($params->remove == 1)
        {
            $wall->removeIncludedTag($tag);
        }
        else
        {
            $wall->addIncludedTag($tag);
        }
        
        $em->persist($wall);
        $em->flush();
        
        if (null !== $wall)
        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'tagToWall',
                'data'=>array()
            );
        }
       
        
        
        
        return new Response(json_encode($response));
        
        
    }
    
    private function categoryToWall($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $catRepo = $em->getRepository('P4MCoreBundle:Category');
        $cat = $catRepo->find($params->categoryId);
        
        
        if ($params->remove == 1)
        {
            $wall->removeIncludedCategorie($cat);
        }
        else
        {
            $wall->addIncludedCategorie($cat);
        }
        
        $em->persist($wall);
        $em->flush();
        
        
        $view = $this->renderView('P4MCoreBundle:Core/Category:category-popover-container.html.twig',['user'=>$user,'category'=>$cat]);
        
        if (null !== $wall)
        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'categoryToWall',
                'callBackParams'=>['view'=>$view,'formId'=>$params->formId],
                'callBack'=>$params->callBack,
                'data'=>array
                    (
                    
                    )
            );
        }
       
        
        
        
        return new Response(json_encode($response));
        
        
    }
    
    private function loadUserCommunity($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
//        
        $userRepo = $em->getRepository('P4MUserBundle:User');
//        $userLinkRepo = $em->getRepository('P4MUserBundle:UserLink');
        $userDisplayed = $userRepo->find($params->userId);
        
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $userRepoES = $repositoryManager->getRepository('P4MUserBundle:User');
        
        
        
        $options = [];
        if (isset ($params->rank))
        {
            $options['rank']=$params->rank;
        }
        
        if (in_array('followers', $params->dcommunity) && in_array('followed', $params->dcommunity) )
        {
            $community = $userRepoES->findCommunity($userDisplayed,$options); 
        }
        else if (in_array('followers', $params->dcommunity))
        {
            $community = $userRepoES->findFollowers($userDisplayed,$options);
        }
        else if (in_array('followed', $params->dcommunity) )
        {
            
            $community = $userRepoES->findFollowing($userDisplayed,$options);
        }
        
        $twigParams = array('results'=>$community,'user'=>$user);
//        if (null !== $user)
//        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
            
            if (isset($params->callBack))
            {
                $response['callBack']= $params->callBack;
            }
//        }
       
        
        
        
        return new Response(json_encode($response));
        
        
    }
    private function getProfilePosts($params,$page)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mUser.user_utils');
        
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $userDisplayed = $userRepo->find($params->userId);
        
        
        if (null !== $userDisplayed)
        {
            $repositoryManager = $this->container->get('fos_elastica.manager.orm');
            $postRepoES = $repositoryManager->getRepository('P4MCoreBundle:Post');
            
            $params = (array) $params;
            $params['showAction'] = true;
            
//            $results = $userUtils->getProfilePosts($userDisplayed,$page,(array) $params);
            $results = $postRepoES->findUserActionPosts($userDisplayed,$params,$page);

            $twigParams = array('results'=>$results['entities']);
        
        
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                'callBack'=>$params['callBack'],
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
        }
//       $response = [];
        
//        die('</body>');
        
        return new Response(json_encode($response));
//        return new Response(json_encode($response).'</body>');
        
        
    }
    
    private function loadAccountWalls($params,$page)
    {
        $user = $this->getUser();
        $params->userId = $user->getId();
        $params->tilesEdit = true;
        
        return $this->loadUserWalls($params, $page);
    }
    private function loadUserWalls($params,$page)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mUser.user_utils');
        
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $userDisplayed = $userRepo->find($params->userId);
        
        
//        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        
        if (null !== $userDisplayed)
        {
            $repositoryManager = $this->container->get('fos_elastica.manager.orm');
            $wallRepoES = $repositoryManager->getRepository('P4MCoreBundle:Wall');
            
//            $results = $userUtils->getProfileWalls($userDisplayed,$page,(array) $params);
            $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
            

            $results = $wallRepoES->findByUser($userDisplayed,(array) $params,$page,$nombrePostsParPage);
//            $results = $wallRepo->findByUserCustom($userDisplayed,$nombrePostsParPage,($page-1)*$nombrePostsParPage,(array) $params);
            
            $twigParams = array('results'=>$results,'user'=>$user);
        
            if (isset($params->tilesEdit))
            {
                $twigParams['tilesEdit'] = true;
            }
        
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
            
            if (isset($params->callBack))
            {
                $response['callBack'] = $params->callBack;
            }
        }
       
        
        
        
        return new Response(json_encode($response));
        
        
    }
    private function loadWallMembers($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $twigParams = array('results'=>$wall->getFollowers(),'user'=>$user);
        if (null !== $wall)
        {
            
            $response = array
            (
                'status'=>1,
                'action'=>'setWallPost',
                'callBack'=>$params->callBack,
                'data'=>array('posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$twigParams),)
            );
        }
       
        
        
        
        return new Response(json_encode($response));
        
        
    }
    private function deletePost($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        if (!count($post->getViews()))
        {
            $em->remove($post);
            $em->flush();
            
            $response = array
            (
                'status'=>1,
                'action'=>'deletePost',
                'data'=>array('modal'=>array('shortcutBar'=>$this->renderView('P4MCoreBundle:Modal:post-deleted.html.twig')))
            );
        }
        else
        {
            $response = array
            (
                'status'=>0,
                'action'=>'deletePost',
                'data'=>'This post has been viewed'
            );
        }
        
        
        
        
        
        return new Response(json_encode($response));
        
        
    }
    private function unfollowWall($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $wall->removeFollower($user);
        $user->removeWallsFollowed($wall);
        
        
        
        $em->persist($user);
        $em->persist($wall);
        $em->flush();
        
        $response = array
            (
                'status'=>1,
                'action'=>'unfollowWall',
                'callBack'=>$params->callBack,
                'callBackParams'=>$params->wallId,
                'listener'=>$params->listener,
                'data'=>array('shortcutBar'=>$this->renderView('P4MCoreBundle:Menu:shortcut-bar.html.twig',array('user'=>$user)))
            );
        
        return new Response(json_encode($response));
        
        
    }
    private function followWall($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $wall->addFollower($user);
        $user->addWallsFollowed($wall);
        
        $em->persist($wall);
        $em->persist($user);
        $em->flush();
        
        $response = array
            (
                'status'=>1,
                'action'=>'followWall',
                'callBack'=>$params->callBack,
                'callBackParams'=>$params->wallId,
                'listener'=>$params->listener,
                'data'=>array('shortcutBar'=>$this->renderView('P4MCoreBundle:Menu:shortcut-bar.html.twig',array('user'=>$user)))
            );
        
        return new Response(json_encode($response));
        
        
    }

    private function updatePostView($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $viewRepo = $em->getRepository('P4MTrackingBundle:PostView');
        $view = $viewRepo->find($params->viewId);
        if (($view->getUser() != null && $user === $view->getUser()) || ($view->getUser() === null && !$user instanceof \P4M\UserBundle\Entity\User))
        {
            $view->setDateout(new \DateTime());
        }
        
        $em->persist($view);
        $em->flush();
        
        $response = array
            (
                'status'=>1
            );
        
        return new Response(json_encode($response));
        
        
    }
    
    private function unReadPostLater($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
//        die($post->getId());
        $readPostRepo = $em->getRepository('P4MBackofficeBundle:ReadPostLater');
        $readPostLater = $readPostRepo->findOneBy(['user'=>$user,'post'=>$post]);
        
        if ($readPostLater !== null)
        {
            $post->removeReadLater($readPostLater);
            $em->persist($post);
            $em->remove($readPostLater);
            $em->flush();
            
            $response = array
            (
                'status'=>1,
                'action'=>'banPost',
                'data'=>array
                    (
                        
//                        'callBack'=>'unReadPostLater',
                        'postId'=>$post->getId(),
                        
                    )
            );
        }
        else
        {
            $response = array
            (
                'status'=>0,
            );
        
        }
        
        
        return new Response(json_encode($response));
        
        
    }
    private function readPostLater($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        $readPostLater = new \P4M\BackofficeBundle\Entity\ReadPostLater();
        $readPostLater->setUser($user);
        $readPostLater->setPost($post);
        $post->addReadLater($readPostLater);
        
        $em->persist($readPostLater);
        $em->persist($post);
        $em->flush();
        
        $response = array
            (
                'action'=>'readPostLater',
                'status'=>1,
                'data'=>array
                    (
                        'postId'=>$post->getId()
                    )
            );
        
        return new Response(json_encode($response));
        
        
    }
    
    private function banPost($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        $bannedPost = new \P4M\BackofficeBundle\Entity\BannedPost();
        $bannedPost->setUser($user);
        $bannedPost->setPost($post);
        
        $em->persist($bannedPost);
        $em->flush();
        
        $response = array
            (
                'action'=>'banPost',
                'status'=>1,
                'data'=>array
                    (
                        'postId'=>$post->getId()
                    )
            );
        
        return new Response(json_encode($response));
        
        
    }
    
    private function editPostFormSubmit($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        if (null === $post)
        {
            $response = array
                (
                    'status'=>0,
                    'error'=>'This post doesn\'t exists'
                );
        }
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
//        die('<pre>'.print_r($params,true).'</pre>');
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostType($post->getPictureList(),$post->getPicture()), $post);
        $form   ->remove('pictureList')
                ->remove('sourceUrl')
//                ->remove('author')
                ->remove('anchors')
                ->remove('blogPost')
                ;

        $postTags = $post->getTags();
        $postCategories = $post->getCategories();
        
        foreach ($postTags as $tag)
        {
            $tag->removePost($post);
            $em->persist($tag);
        }
        foreach ($postCategories as $cat)
        {
            $cat->removePost($post);
            $em->persist($cat);
        }
        $em->flush();
        
        $form->bind($params['p4m_corebundle_post']);
        
        if ($form->isValid()) {
            
            $em->persist($post);
            foreach($post->getTags() as $tag)
            {
                $tag->addPost($post);
                $em->persist($tag);
            }
            
            
            foreach($post->getCategories() as $cat)
            {
                $cat->addPost($post);
                $em->persist($cat);
            }
             
//            die(print_r($post->getTags()[0],true));
                $em->flush();
                
            $response = array
                (
                    'action'=>'postEdited',//Je laisse addPostForm, ça ne fait qu'une redirection du côté js
                    'status'=>1,
                    'data'=>
                    [
                        'url'=>$this->generateUrl('p4m_core_post',array('postSlug'=>$post->getSlug())),
                        'modal'=>$this->renderView('P4MCoreBundle:Modal:edit-post-confirm.html.twig',array('post'=>$post)),
                        'infos'=>$this->renderView('P4MCoreBundle:Menu/ActionBar/Post:post-editable-infos.html.twig',array('post'=>$post,'user'=>$user)),
                    ]
                );
        }
        else
        {
            $response = array
                (
                    'action'=>'editPostForm',
                    'status'=>0,
                    'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$form->createView()))
                );

        }

        return new Response(json_encode($response));
        
    }
    
    
    private function editPostForm($params)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        
        $post = $postRepo->find($params->postId);
        
        if ($post === null)
        {
            $response = array('status'=>0,'error'=>'post not found');
        }
        if (!count($post->getPictureList()))
        {
            $post->setPictureList([$post->getPicture()]);
        }
        
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostType($post->getPictureList(),$post->getPicture()), $post);
        
        $form   ->remove('pictureList')
                ->remove('sourceUrl')
//                ->remove('author')
                ->remove('anchors')
                ->remove('blogPost')
                ;
        
        $response = array
                    (
                        'action'=>'editPost',
                        'status'=>1,
                        'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$form->createView()))
                    );
        
        return new Response(json_encode($response));
        
        
    }
    private function addPostForm($params)
    {
        $userUtils = $this->get('p4mCore.post_utils');
        $postUrl = $params->addPostUrl;
        
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $langueRepo = $em->getRepository('P4MCoreBundle:Lang');
                
        $post = $postRepo->findOneBySourceUrl($postUrl);
        
        if (null === $post)
        {
            $metas = $userUtils->grabMetas($postUrl);
            
            $post = new Post();
            
            $post->setSourceUrl($postUrl);
            $post->setTitle($metas['title']);
            $post->setContent($metas['content']);
            $post->setPictureList($metas['sourcePictureList']);
            if (isset($metas['picture']))
            {
                $post->setPicture($metas['picture']);    
            }
            if (isset($metas['embed']))
            {
                $post->setEmbed($metas['embed']);    
            }
            if (isset($metas['type']))
            {
                $postTypeRepo = $em->getRepository('P4MCoreBundle:PostType');
                $postType = $postTypeRepo->findOneByName(ucfirst($metas['type']));
                if (null !== $postType)
                {
                    $post->setType($postType);    
                }
            
            }
            
            if (isset($metas['lang']))
            {
                $langue = $langueRepo->findOneByCode(strtolower($metas['lang']));
                $post->setLang($langue);
            }
//            die($metas['tags']);
            if (isset($metas['tags']))
            {
                
                $tagsRepo = $em->getRepository('P4MCoreBundle:Tag');
                
                foreach($metas['tags'] as $futureTag)
                {
                    $tag = $tagsRepo->findOneByName($futureTag);
                    if (null === $tag)
                    {
                        $tag = new Tag();
                        $tag->setName($futureTag);
                    }
                    $post->addTag($tag);
                }
                
            }
           
            
            $form = $this->createForm(new \P4M\CoreBundle\Form\PostType($metas['sourcePictureList'],''),$post);
            $form->remove('blogPost')
                    ->remove('anchors');
            
            $response = array
                    (
                        'action'=>'addPost',
                        'status'=>1,
                        'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$form->createView()))
                    );
            
        }else
        {
            $modalButtons = '<a href="'.$this->generateUrl('p4m_core_post',['postSlug'=>$post->getSlug()]).'" class="btn btn-default">Go to that link</a>';
            $response = array
                    (
                        'status'=>0,
                        'error'=>'post already exists',
                        'modalButtons'=>$modalButtons
                    );
        }
        
        return new Response(json_encode($response));
        
        
        
    }
    
    private function addPostFormSubmit($params)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = new Post();
        $post->setUser($user);
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
//        die('<pre>'.print_r($params,true).'</pre>');
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostType($params['p4m_corebundle_post']['pictureList']), $post);
        $form   ->remove('blogPost')
                ->remove('anchors');

        
        $form->bind($params['p4m_corebundle_post']);
        
        if ($form->isValid()) {
            
            $postUtils = $this->get('p4mCore.post_utils');
            
            $authorKey = $postUtils->getPostAuthorMeta($post->getSourceUrl());
            
            if (null !== $authorKey)
            {
                $userRepo = $em->getRepository('P4MUserBundle:User');
                $author = $userRepo->findOneByProducerKey($authorKey);
                
                if (null !== $author)
                {
                    $post->setAuthor($author);
                }
            }
            
            
            
            $em->persist($post);
            foreach($post->getTags() as $tag)
            {
                $tag->addPost($post);
                $em->persist($tag);
             }
            foreach($post->getCategories() as $cat)
            {
                $cat->addPost($post);
                $em->persist($cat);
            }
             
//             $score = new \P4M\PinocchioBundle\Entity\PostScore();
//             $score->setScore(1);
//             $score->setPost($post);
//             $post->setScore($score);
//             $em->persist($score);
//            die(print_r($post->getTags()[0],true));
                $em->flush();
                
            $response = array
                (
                    'action'=>'addPostForm',
                    'status'=>1,
                    'data'=>array('modal'=>$this->renderView('P4MCoreBundle:Modal:add-post-confirm.html.twig',array('post'=>$post)))
                );
        }
        else
        {
            $response = array
                (
                    'action'=>'addPostForm',
                    'status'=>0,
                    'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$form->createView()))
                );

        }

        return new Response(json_encode($response));
        
    }
    private function reportPost($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $postRepo = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params['postId']);
        
        $postFlag = new \P4M\ModerationBundle\Entity\PostFlag();
        $postFlag->setUser($user);
        $postFlag->setPost($post);
        
        $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\PostFlagType(),$postFlag);
        

        
        $reportForm->bind($params['p4m_moderationbundle_postflag']);
        
        if ($reportForm->isValid()) {
            
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($postFlag);
            $em->flush();
                
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on PFM']
                );
        }
        else
        {
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>0,
                    'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$reportForm->createView()))
                );

        }

        return new Response(json_encode($response));
        
    }
    
    private function flaggedPost($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $flagRepo = $this->getDoctrine()->getManager()->getRepository('P4MModerationBundle:PostFlag');
        $flag = $flagRepo->find($params['flagId']);
        
        
        $post = $flag->getPost();
        
        
        
        $confirmation = new \P4M\ModerationBundle\Entity\FlagConfirmation();
        
        $confirmation->setUser($user);
        $confirmation->setFlag($flag);
        
//        $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\FlagConfirmationType(),$confirmation);
        

        
//        $reportForm->bind($params['p4m_moderationbundle_flagconfirmation']);
        
        if ($params['confirmed']) 
        {
            $confirmation->setConfirmed(true);
        }
        else
        {
            $confirmation->setConfirmed(false);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($confirmation);
            $em->flush();
            
            $confirmations = $flag->getConfirmations();
            $confirmed = 0;
            foreach ($confirmations as $confirmation)
            {
                if ($confirmation->getConfirmed())
                {
                    $confirmed ++;
                }
                else
                {
                    $confirmed --;
                }
            }
            
//            if (count($confirmations)>15 && count($confirmations)/$confirmed>0)
//            {
//                $post->setConfirmedFlag(true);
//                $em->persist($post);
//                $em->flush();
//                
//            }
//            elseif (count($confirmations)>15)
//            {
//                $flagArchive = new \P4M\ModerationBundle\Entity\PostFlagArchive();
//                $flagArchive->setDescription($flag->getDescription());
//                $flagArchive->setPost($flag->getPost());
//                $flagArchive->setType($flag->getType());
//                $flagArchive->setUser($flag->getUser());
//                $flagArchive->setConfirmations($flag->getConfirmations());
//                
//                $em->persist($flagArchive);
//                foreach ($confirmations as $confirmation)
//                {
//                    $confirmation->setFlag(null);
//                    $confirmation->setFlagArchive($flagArchive);
//                    
//                    $em->persist($confirmation);
//                }
//                
//                $em->remove($flag);
//                
//                $em->flush();
//            }
                
            $response = array
                (
                    'action'=>'flaggedPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on Pressformore']
                );
        

        return new Response(json_encode($response));
        
    }
    
    private function reportUser($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $userRepo = $this->getDoctrine()->getManager()->getRepository('P4MUserBundle:User');
        $userDisplayed  = $userRepo->find($params['userId']);
        
        $userFlag = new \P4M\ModerationBundle\Entity\UserFlag();
        $userFlag->setUser($user);
        $userFlag->setUserTarget($userDisplayed);
        
        $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\UserFlagType(),$userFlag);
        

        
        $reportForm->bind($params['p4m_moderationbundle_userflag']);
        
        if ($reportForm->isValid()) {
            
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($userFlag);
            $em->flush();
                
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on PFM']
                );
        }
        else
        {
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>0,
                    'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$reportForm->createView()))
                );

        }

        return new Response(json_encode($response));
        
    }
    
    private function confirmUserFlag($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $flagRepo = $this->getDoctrine()->getManager()->getRepository('P4MModerationBundle:UserFlag');
        $flag = $flagRepo->find($params['flagId']);
        
        
        $userDisplayed = $flag->getUserTarget();
        
        
        
        $confirmation = new \P4M\ModerationBundle\Entity\UserFlagConfirmation();
        
        $confirmation->setUser($user);
        $confirmation->setFlag($flag);
        

        
        if ($params['confirmed']) 
        {
            $confirmation->setConfirmed(true);
        }
        else
        {
            $confirmation->setConfirmed(false);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($confirmation);
            $em->flush();
            
            $confirmations = $flag->getConfirmations();
            $confirmed = 0;
            foreach ($confirmations as $confirmation)
            {
                if ($confirmation->getConfirmed())
                {
                    $confirmed ++;
                }
                else
                {
                    $confirmed --;
                }
            }
            
            $response = array
                (
                    'action'=>'flaggedPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on Pressformore']
                );
        

        return new Response(json_encode($response));
        
    }
    
    private function deleteWall($params)
    {
        $em = $this->getDoctrine()->getManager();
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        
        $this->getDoctrine()->getManager()->remove($wall);
        
        $this->getDoctrine()->getManager()->flush();
        

        $response = array
            (
                'action'=>'deleteWall',
                'status'=>1,
                'data'=>array
                    (
//                        'wallId'=>$wall->getId(), LOL
                        'wallId'=>$params->wallId, 
                        'message'=>'Your strew has been deleted'
                    )
            );
        

        return new Response(json_encode($response));
    }
    
    private function reportWall($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $wallRepo = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Wall');
        $wall  = $wallRepo->find($params['wallId']);
        
        $wallFlag = new \P4M\ModerationBundle\Entity\WallFlag();
        $wallFlag->setUser($user);
        $wallFlag->setWall($wall);
        
        $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\WallFlagType(),$wallFlag);
        

        
        $reportForm->bind($params['p4m_moderationbundle_wallflag']);
        
        if ($reportForm->isValid()) {
            
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($wallFlag);
            $em->flush();
                
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on PFM']
                );
        }
        else
        {
            $response = array
                (
                    'action'=>'reportPost',
                    'status'=>0,
                    'data'=>$this->renderView('P4MCoreBundle:Post:post-form-full.html.twig',array('form'=>$reportForm->createView()))
                );

        }

        return new Response(json_encode($response));
        
    }
    
    private function confirmWallFlag($params)
    {
        $user = $this->getUser();
        
        $params = json_decode($this->getRequest()->request->get('params'),true);
        
        $flagRepo = $this->getDoctrine()->getManager()->getRepository('P4MModerationBundle:WallFlag');
        $flag = $flagRepo->find($params['flagId']);
        
        
        $wall = $flag->getWall();
        
        
        
        $confirmation = new \P4M\ModerationBundle\Entity\WallFlagConfirmation();
        
        $confirmation->setUser($user);
        $confirmation->setFlag($flag);
        

        
        if ($params['confirmed']) 
        {
            $confirmation->setConfirmed(true);
        }
        else
        {
            $confirmation->setConfirmed(false);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($confirmation);
            $em->flush();
            
            $confirmations = $flag->getConfirmations();
            $confirmed = 0;
            foreach ($confirmations as $confirmation)
            {
                if ($confirmation->getConfirmed())
                {
                    $confirmed ++;
                }
                else
                {
                    $confirmed --;
                }
            }
            
            $response = array
                (
                    'action'=>'flaggedPost',
                    'status'=>1,
                    'data'=>['message'=>'Thanks you for helping us improve the quality on Pressformore']
                );
        

        return new Response(json_encode($response));
        
    }
    
    
    
    
    
    
    
    private function wallVote($params)
    {
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository('P4MCoreBundle:Vote');
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->find($params->wallId);
        
        $userVote = $voteRepo->findOneBy(array('wall'=>$params->wallId,'user'=>$user->getId()));
        
        
        if (null === $userVote)
        {
            $userVote = new \P4M\CoreBundle\Entity\Vote();
            $userVote->setUser($user);
            $userVote->setWall($wall);
        }
        
        $userVote->setScore($params->score);
        
        $em->persist($userVote);
        $em->flush();
        
        $postitiveVotesNumber = $voteRepo->countPositiveByWall($wall);
        $negativeVotesNumber = $voteRepo->countNegativeByWall($wall);
        
        $response = array
                (
                    'action'=>'wallVote',
                    'status'=>1,
                    'data'=>array
                        (
                            'score'=>$params->score,
                            'positiveVotesNumber'=>$postitiveVotesNumber,
                            'negativeVotesNumber'=>$negativeVotesNumber
                        )
                );
        
        return new Response(json_encode($response));
    }
    private function commentVote($params)
    {
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository('P4MCoreBundle:Vote');
        
        $commentRepo = $em->getRepository('P4MCoreBundle:Comment');
        $comment = $commentRepo->find($params->commentId);
        
        $userVote = $voteRepo->findOneBy(array('comment'=>$params->commentId,'user'=>$user->getId()));
        
        
        if (null === $userVote)
        {
            $userVote = new \P4M\CoreBundle\Entity\Vote();
            $userVote->setUser($user);
            $userVote->setComment($comment);
        }
        
        $userVote->setScore($params->score);
        
        $em->persist($userVote);
        $em->flush();
        
        $postitiveVotesNumber = $voteRepo->countPositiveByComment($comment);
        $negativeVotesNumber = $voteRepo->countNegativeByComment($comment);
        
        $response = array
                (
                    'action'=>'commentVote',
                    'status'=>1,
                    'data'=>array
                        (
                            'score'=>$params->score,
                            'commentId'=>$comment->getId(),
                            'positiveVotesNumber'=>$postitiveVotesNumber,
                            'negativeVotesNumber'=>$negativeVotesNumber
                        )
                );
        
        return new Response(json_encode($response));
    }
    
    private function postVote($params)
    {
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository('P4MCoreBundle:Vote');
        
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $post = $postRepo->find($params->postId);
        
        $userVote = $voteRepo->findOneBy(array('post'=>$params->postId,'user'=>$user->getId()));
        
        
        if (null === $userVote)
        {
            $userVote = new \P4M\CoreBundle\Entity\Vote();
            $userVote->setUser($user);
            $userVote->setPost($post);
        }
        
        $userVote->setScore($params->score);
        
        $em->persist($userVote);
        $em->flush();
        
        $postitiveVotesNumber = $voteRepo->countPositive($post);
        $negativeVotesNumber = $voteRepo->countNegative($post);
        
        $response = array
                (
                    'action'=>'vote',
                    'status'=>1,
                    'data'=>array
                        (
                            'positiveVotesNumber'=>$postitiveVotesNumber,
                            'negativeVotesNumber'=>$negativeVotesNumber,
                            'scoreVoted'=>$userVote->getScore()
                        )
                );
        
        $this->dispatchPostActivity($post,$user);
        
        return new Response(json_encode($response));
    }
    
    private function postComment($params)
    {
        $response = 
            array
                (
                    'status'=>1,
                    'action'=>'postComment'
                );
        
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $commentRepo = $em->getRepository('P4MCoreBundle:Comment');
        $post = $postRepo->find($params->postId);
        
        $comment = new \P4M\CoreBundle\Entity\Comment();
        
        $comment->setUser($user);
        $comment->setContent($params->p4m_corebundle_comment_content);
        $comment->setPost($post);
        
        if ($params->parentId)
        {
            $parent = $commentRepo->find($params->parentId);
            $comment->setParent($parent);
            $response['parent'] = $params->parentId;
        }
        $em->persist($comment);
        $em->flush();
        
        $response['data'] = $this->renderView('P4MCoreBundle:Post:post-comment.html.twig', array('comment'=>$comment,'user'=>$user));
        
        $this->dispatchPostActivity($post,$user);
        
        return new Response(json_encode($response));
    }
    
    private function wallComment($params)
    {
        $response = 
            array
                (
                    'status'=>1,
                    'action'=>'wallComment'
                );
        
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $commentRepo = $em->getRepository('P4MCoreBundle:Comment');
        $wall = $wallRepo->find($params->wallId);
        
        $comment = new \P4M\CoreBundle\Entity\Comment();
        
        $comment->setUser($user);
        $comment->setContent($params->p4m_corebundle_comment_content);
        $comment->setWall($wall);
        
        if ($params->parentId)
        {
            $parent = $commentRepo->find($params->parentId);
            $comment->setParent($parent);
            $response['parent'] = $params->parentId;
        }
        $em->persist($comment);
        $em->flush();
        
        $response['data'] = $this->renderView('P4MCoreBundle:Post:post-comment.html.twig', array('comment'=>$comment,'user'=>$user));
        
//        $this->dispatchPostActivity($wall,$user);
        
        return new Response(json_encode($response));
    }
    
    private function follow($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $userLinkRepo = $em->getRepository('P4MUserBundle:UserLink');
        
        $followedUser = $userRepo->find($params->user);
        
        $userUtils = $this->get('p4mUser.user_utils');

        if (!$userUtils->isFollowedBy($followedUser,$user))
        {
            $userUtils->createLink($followedUser,$user);

            return new Response(json_encode(array('status'=>1,'callBackParams'=>$params->user,'callBack'=>$params->callBack,'action'=>'follow')));
        }
        else
        {
            return new Response(json_encode(array('status'=>0,'action'=>'follow','error'=>'already Following')));
    }
    }
    private function reportAjaxError($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $superAdmins = $userRepo->findByRole('ROLE_SUPER_ADMIN');
        
        $emails = [];
        foreach ($superAdmins as $sa)
        {
            $emails[]=$sa->getEmail();
        }
        
        
        $message = \Swift_Message::newInstance()
            ->setSubject('Ajax Troubles')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo(implode(',',$emails))
            ->setBody(print_r($params,true))
        ;
        
        $mailer = $this->get('mailer');
        
        
//            return new Response(json_encode(array('status'=>0,'action'=>'follow','error'=>'already Following')));
    
    }
    
    private function unfollow($params)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        $userLinkRepo = $em->getRepository('P4MUserBundle:UserLink');
        
        $followedUser = $userRepo->find($params->user);
        
        $userUtils = $this->get('p4mUser.user_utils');
        
        if (!$userUtils->isFollowedBy($followedUser,$user))
        {
            return new Response(json_encode(array('status'=>0,'action'=>'unfollow','error'=>'not Following')));
        }
        else
        {
//            die('delete link');
            $userUtils->deleteLink($followedUser,$user);
            return new Response(json_encode(array('status'=>1,'callBackParams'=>$params->user,'callBack'=>$params->callBack,'action'=>'unfollow')));    
    }
    }
    
    private function throw403($msg = 'Access Denied')
    {
        throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException($msg);
    }
    
    
    private function dispatchPostActivity($post,$user)
    {
         $event = new PostActivityEvent($post, $user);
        // On déclenche l'évènement
        $this->get('event_dispatcher')
             ->dispatch(PinocchioEvents::onPostActivity, $event);
    }
    
}




