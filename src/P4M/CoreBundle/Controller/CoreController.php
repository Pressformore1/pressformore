<?php

namespace P4M\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Category;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\PostType;
use P4M\UserBundle\Entity\User;
use P4M\PinocchioBundle\Event\PinocchioEvents;
use P4M\PinocchioBundle\Event\PostActivityEvent;

class CoreController extends Controller
{
    public function indexAction()
    {
//        $user = $this->getUser();
//        die($user->getUsername());
//        $em = $this->getDoctrine()->getManager();
//        
//        
//        $em->remove($user->getPicture());
//        $em->flush();
        
        
        $params = array('posts' => array('zg','zg','zg','zg','zg','zg','zg','zg','zg','zg','zg'));
        return $this->render('P4MCoreBundle:Core:index.html.twig',$params);
    }
    
    public function categoryAction($categoryName,$page)
    {
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $catRepo = $em->getRepository('P4MCoreBundle:Category');
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        
        $category = $catRepo->findOneByName($categoryName);
        
        if (null === $category)
        {
            throw $this->createNotFoundException('this category doesn\'t exist!');
        }
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        $posts =  $postRepo->findByCategoriesId(array($category->getId()),$nombrePostsParPage,($page-1)*$nombrePostsParPage);
        $nombrePages = ceil($postRepo->countPostsByCategory(array($category->getId()))/$nombrePostsParPage);
        
        $params = array
                (
                    'category'=>$category,
                    'posts'=>$posts,
                    'nombrePages'=>$nombrePages
                );
        return $this->render('P4MCoreBundle:Core/Category:category.html.twig',$params);
        
    }
    public function wallAction($page =1)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
        $wall = $wallRepo->findOneByUser($user);
        
        if (null === $wall)
        {
            return $this->redirect($this->generateUrl('p4_m_backoffice_homepage').'#'.$this->generateUrl('p4_m_backoffice_wallAdd'));
        }
        
        
        return $this->redirect($this->generateUrl('p4m_core_showWall',array('wallSlug'=>$wall->getSlug())));
        
    }
    
    
    
    
    
    
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $user = $this->getUser();
        

            $userCategories=array();
            $userTags=array();

        $notifRepo = $em->getRepository('P4MNotificationBundle:Notification');
        $notifications = $notifRepo->findByUserPaginated($user);
        
//        die('test');
        $params = array
            (
                'user'=>$user,
                'categories'=>$userCategories,
                'tags'=>$userTags,
                'notifications'=>$notifications
                
            );
        return $this->render('P4MCoreBundle:Menu:menu.html.twig',$params);
        
    }
    public function shortcutBarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $user = $this->getUser();
        

        $userCategories=array();
        $userTags=array();
        $autoFollowRepo = $em->getRepository('P4MCoreBundle:WallAutoFollow');
        $autoFollows = $autoFollowRepo->findAll();

        $suggestedWalls = [];
        foreach ($autoFollows as $autoFollow)
        {
//            die('wall '.$autoFollow->getWall());
            if ($user !== null && !$user->getWallsFollowed()->contains($autoFollow->getWall()))
            {
                $suggestedWalls[] = $autoFollow->getWall();
//                $user->addWallsFollowed($autoFollow->getWall());
            }
        }
        
//        die('test');
        $params = array
            (
                'user'=>$user,
                'categories'=>$userCategories,
                'tags'=>$userTags,
                'suggestedWalls'=>$suggestedWalls
                
            );
        return $this->render('P4MCoreBundle:Menu:shortcut-bar.html.twig',$params);
        
    }
    
    
    public function postAction($postSlug)
    {
        
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        $voteRepo = $em->getRepository('P4MCoreBundle:Vote');
        $pressformRepo = $em->getRepository('P4MCoreBundle:Pressform');
        
        $post = $postRepo->findOneBySlug($postSlug);
        $user = $this->getUser();
        $securityContext = $this->container->get('security.context');
        $request = $this->container->get('request');

        if (null !== $user)
        {
            $walletFillRepo = $em->getRepository('P4MMangoPayBundle:WalletFill');
            $walletFill = $walletFillRepo->findActiveByUser($user);
        }
        else
        {
            $walletFill = null;
        }
        
        
        if (null === $post)
        {
            throw $this->createNotFoundException('this post doesn\'t exist!');
        }
        if ($post->getQuarantaine() == true && !$securityContext->isGranted('ROLE_ADMIN'))
        {
            throw $this->createNotFoundException('this post doesn\'t exist!');
        }
        
       
        $postitiveVotesNumber = $voteRepo->countPositive($post);
        $negativeVotesNumber = $voteRepo->countNegative($post);
        
        $commentForm = $this->createForm(new \P4M\CoreBundle\Form\CommentType()); 
        
        
        $view = new \P4M\TrackingBundle\Entity\PostView();
        $view->setPost($post);
         if (null !== $user)
         {
             $view->setUser($user);
             
         }
        $em->persist($view); 
        
        
        
        $ReadLaterRepo = $em->getRepository('P4MBackofficeBundle:ReadPostLater');
        $readLater = $ReadLaterRepo->findOneBy(array('user'=>$user,'post'=>$post));
        if (null !== $readLater)
        {
            $em->remove($readLater);
        }
        

        $em->flush();

        $event = new PostActivityEvent($post, $user);
        // On déclenche l'évènement
        $this->get('event_dispatcher')
             ->dispatch(PinocchioEvents::onPostActivity, $event);

            
        
        
        $flagged = false;
        
        
        
        if ($post->getFlag())
        {
            $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\FlagConfirmationType());
            $flagRepo = $em->getRepository('P4MModerationBundle:PostFlag');
            $flagConfirmRepo = $em->getRepository('P4MModerationBundle:FlagConfirmation');
        
            $userFlag = $flagRepo->findOneBy(['user'=>$user,'post'=>$post]);
            $userFlagConfirm = $flagConfirmRepo->findOneBy(['user'=>$user,'flag'=>$post->getFlag()]);
            
            if (null !== $userFlag || null !== $userFlagConfirm)
            {
                $flagged = true;
            }
        }
        else
        {
            $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\PostFlagType());
            $reportForm->remove('description');
            
        }
        
        
        $pressforms = $pressformRepo->findBy(['sender'=>$user,'post'=>$post,'payed'=>false]);
        if (count($pressforms))
        {
            $pressformed = true;
        }
        else
        {
            $pressformed = false;
        }
        
        $unpressformTypeRepo = $em->getRepository('P4MCoreBundle:UnpressformType');
        $unpressformTypes = $unpressformTypeRepo->findAll();
        
        $unpressformRepo = $em->getRepository('P4MCoreBundle:Unpressform');
        $unpressform = $unpressformRepo->findOneBy(['post'=>$post,'user'=>$user]);
        
        
        $wantPressformForm;
        $wantPressformRepo = $em->getRepository('P4MCoreBundle:WantPressform');
        $wantPressform = $wantPressformRepo->findOneBy(['user'=>$user,'post'=>$post]);
        
        
        
        
        
        
        $params = array
                (
                    'post'=>$post,
                    'user'=>$user,
                    'commentForm'=>$commentForm->createView(),
                    'viewId'=>$view->getId(),
                    'reportForm'=>$reportForm->createView(),
                    'positiveVotesNumber'=>$postitiveVotesNumber,
                    'negativeVotesNumber'=>$negativeVotesNumber,
                    'flagged'=>$flagged,
                    'pressformed'=>$pressformed,
                    "unpressformTypes"=>$unpressformTypes,
                    "unpressform"=>$unpressform,
                    'walletFill'=>$walletFill,
                    'wantPressform'=>$wantPressform
                );
        
        
//        $response =  $this->renderView('P4MCoreBundle:Post:post.html.twig',$params);
        $response =  $this->render('P4MCoreBundle:Post:post.html.twig',$params);
        if (null === $user)
        {
            $currentDate = new \DateTime();
            $cookieExpiration = $currentDate->modify('+1 hour');
            $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie('login_redirection',$request->getUri(),$cookieExpiration ));
        }
//        die($response);
        return $response;
        
        
    }
    
    public function addPostAction()
    {
        $post = new Post();
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostType(), $post);
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();
                return $this->redirect($this->generateUrl('p4m_core_post',array($post->getSlug())));
            }
            else
            {
//                die($request->request->get('picture'));
                return $this->render('P4MCoreBundle:Core/Post:post-form-full.html.twig',array('form'=>$form->createView()));
            }
        }
        throw $this->createNotFoundException('error in datas!');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
   
        
    
    
}
