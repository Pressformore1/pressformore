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


class UserController extends Controller
{
  public function profileAction($username)
  {
      $userUtils = $this->get('p4mUser.user_utils');
      $em = $this->getDoctrine()->getManager();
      $userRepo = $em->getRepository('P4MUserBundle:User');
      $userLinkRepo = $em->getRepository('P4MUserBundle:UserLink');
      $userDisplayed = $userRepo->findOneByUsername($username);
      
      if (null === $userDisplayed)
      {
          throw $this->createNotFoundException('this user doesn\'t exist!');
      }
      
      $following = $userUtils->isFollowedBy($userDisplayed,$this->getUser());
//      $userlink = $userLinkRepo->findOneBy(array())
      
      $params = array
              (
                'user'=>$userDisplayed,
                'following'=>$following
              );
      
      return $this->render('P4MUserBundle:Social/Profile:profile.html.twig',$params);
    
  }
  
  public function myAccountAction()
  {
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('picture')
                ->remove('plainPassword')
                ->remove('categories')
                ->remove('tags')
                ->remove('title');
        
        // On récupère la requête
        $request = $this->get('request');
        
        

        if ($request->getMethod() == 'POST') 
        {
            
          $form->bind($request);

            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        
        
        // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('P4MUserBundle:Social/Profile:account-form.html.twig', array(
          'form' => $form->createView(),
            'user'=>$user
        ));
  }
  
  public function myAccountPasswordAction()
  {
        $user_manager = $this->getDoctrine()->getManager();
  
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('picture')
                ->remove('firstname')
                ->remove('website')
                ->remove('publicStatus')
                ->remove('name')
                ->remove('surname')
                ->remove('bio')
                ->remove('skills')
                ->remove('email')
                ->remove('categories')
                ->remove('tags')
                ->remove('username')
                ->remove('title');
        
       $dispatcher = $this->container->get('event_dispatcher');

       $request = $this->getRequest();
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

            $password = $request->request->get('currentPassword');
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $passwordValid = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? "true" : "false";
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
       
        if ($request->isMethod('POST') && $passwordValid) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('p4m_social_profile_edit_password');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->render('P4MUserBundle:Social/Profile:account-password.html.twig',array('form'=>$form->createView(),'user'=>$user));
        
  }
  
  public function myAccountPictureAction()
  {
      
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('plainPassword')
                ->remove('firstname')
                ->remove('website')
                ->remove('publicStatus')
                ->remove('name')
                ->remove('surname')
                ->remove('bio')
                ->remove('skills')
                ->remove('email')
                ->remove('categories')
                ->remove('tags')
                ->remove('username')
                ->remove('title');
        
         
        // On récupère la requête
        $request = $this->get('request');
        
        

        if ($request->getMethod() == 'POST') 
        {
            
          $form->bind($request);

            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                
                
            }
        }
        
         return $this->render('P4MUserBundle:Social/Profile:account-picture.html.twig',array('form'=>$form->createView(),'user'=>$user));
  }
  public function myAccountPreferencesAction()
  {
      
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('plainPassword')
                ->remove('picture')
                ->remove('firstname')
                ->remove('website')
                ->remove('publicStatus')
                ->remove('name')
                ->remove('surname')
                ->remove('bio')
                ->remove('skills')
                ->remove('email')
                ->remove('username')
                ->remove('title');
        
         
        // On récupère la requête
        $request = $this->get('request');
        
        

        if ($request->getMethod() == 'POST') 
        {
            
          $form->bind($request);

            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        
         return $this->render('P4MUserBundle:Social/Profile:account-preferences.html.twig',array('form'=>$form->createView(),'user'=>$user));
  }
  
  
  
    public function accountMenuAction($routeName)
    {
//        $request = $this->container->get('request');
//        $routeName = $request->get('_route');
//        
//        die($routeName);
        $menu =
                array
                (
                    array('name'=>'Account',   'path'=>'p4m_social_profile_edit'),
                    array('name'=>'Password',   'path'=>'p4m_social_profile_edit_password'),
                    array('name'=>'Profile Picture',   'path'=>'p4m_social_profile_edit_picture'),
                    array('name'=>'Preferences',   'path'=>'p4m_social_profile_edit_preferences'),
                    array('name'=>'Walls',   'path'=>'p4m_social_profile_edit_walls')
                );
        
        return $this->render('P4MUserBundle:Social/Profile:account-menu.html.twig',array('menu'=>$menu,'route'=>$routeName));
    }
  
  
  
  
  
  
}
