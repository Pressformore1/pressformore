<?php
// src/Sdz/UserBundle/Controller/SecurityController.php;

namespace P4M\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;


class ProfileController extends Controller
{
    public function profileAction($username,$page)
    {
        
        
        $userUtils = $this->get('p4mUser.user_utils');
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $userDisplayed = $userRepo->findOneByUsername($username);
        
        $user = $this->getUser();


      
        if (null === $userDisplayed)
        {
          
          throw $this->createNotFoundException('this user doesn\'t exist!');
        }
      
        $following = false;
        if (null !== $user)
        {
            $following = $userUtils->isFollowedBy($userDisplayed,$user);
        }
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        
//        die('test');
        
//        $results = $userUtils->getFullProfileResultsDoctrine($userDisplayed,$page);
//        $results = $userUtils->getFullProfileResults($userDisplayed,$page,$nombrePostsParPage);
        $activityRepo = $em->getRepository('P4MTrackingBundle:UserActivity');
        $results = $activityRepo->findProfileActivity($userDisplayed,$page);
        
        $postLanguages = array();
        $postTypes = array();
        $categories = array();
        $tags = array();
        

    foreach ($results as $entity)
    {
        $post = $entity->getPost();
        if ($post !== null)
        {
//            $post = $entity;
            if (!in_array($post->getLang(), $postLanguages,true) && null !== $post->getLang())
            {
                $postLanguages[] = $post->getLang();
            }
            if (!in_array($post->getType(), $postTypes,true))
            {
                $postTypes[] = $post->getType();
            }
            foreach ($post->getCategories() as $categorie)
            {
                if (!in_array($categorie, $categories,true))
                {
                    $categories[] = $categorie;
                }
            }
            foreach ($post->getTags() as $tag)
            {
                if (!in_array($tag, $tags,true))
                {
                    $tags[] = $tag;
                }
            }
        }
        

    }
    
    $strewCats = [];
    foreach ($userDisplayed->getWalls() as $wall)
    {
        foreach ($wall->getIncludedCategories() as $cat)
        {
            if (!in_array($cat, $strewCats))
            {
                $strewCats[]=$cat;
            }
        }
    }
        
        $wallLanguages = array();

        $flagged = false;
        if ($userDisplayed->getFlag() && $user !== null)
        {
            $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\UserFlagConfirmationType());
            $flagRepo = $em->getRepository('P4MModerationBundle:UserFlag');
            $flagConfirmRepo = $em->getRepository('P4MModerationBundle:UserFlagConfirmation');
        
            $userFlag = $flagRepo->findOneBy(['user'=>$user,'userTarget'=>$userDisplayed]);
            $userFlagConfirm = $flagConfirmRepo->findOneBy(['user'=>$user,'flag'=>$user->getFlag()]);
            
            if (null !== $userFlag || null !== $userFlagConfirm)
            {
                $flagged = true;
            }
        }
        else
        {
            $reportForm = $this->createForm(new \P4M\ModerationBundle\Form\UserFlagType());
            $reportForm->remove('description');
            
        }
      
        $params = array
              (
                'results'=>$results,
//                'results'=>$results['entities'],
                'userDisplayed'=>$userDisplayed,
                'user'=>$user,
                'following'=>$following,
                'nombrePages'=>10,
                'forceTileClass'=>'small-brick',
                'postLanguages'=>$postLanguages,
                'wallLanguages'=>$wallLanguages,
                'postTypes'=>$postTypes,
                'categories'=>$categories,
                'strewCats'=>$strewCats,
                'tags'=>$tags,
                'filters'=>  http_build_query($this->get('request')->request->all()),
                'flagged'=>$flagged,
                'reportForm'=>$reportForm->createView()
              );
      
      return $this->render('P4MUserBundle:Social/Profile:profile.html.twig',$params);
    
  }
  
    
    
  
 
  
  
  
}
