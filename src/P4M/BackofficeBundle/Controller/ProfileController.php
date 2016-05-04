<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Wall;


use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;

class ProfileController extends Controller
{
    
    
    public function myAccountAction()
    {
        $user = $this->getUser();
        $em= $this->getDoctrine()->getManager();
        
        if ($user->getFirstLogin()===true)
        {
            $user->setFirstLogin(false);
            $em->persist($user);
            $em->flush();
        }
        
        
        
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('picture')
                ->remove('plainPassword')
                ->remove('title')
                ;
        
        // On récupère la requête
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
            
          $form->bind($request);

            if ($form->isValid()) 
            {
                $mango = $this->container->get('p4_m_mango_pay.util');
                $mangoUser= $user->getMangoUserNatural();
                $userUtil = $this->container->get('p4mUser.user_utils');
                
                if (null ==$mangoUser && $userUtil->isMangoUserAvailable($user))
                {
                    $mangoUser = $mango->createUser($user);
                    $wallets = $mango->createWallet($mangoUser);
                }
                
//                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                
                 if ($request->query->get('goBack'))
                {
                    return $this->redirect($request->query->get('goBack'));
                }
                
                $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Your informations has been successfully updated.')
                );
                $url = $this->generateUrl('p4_m_backoffice_profile_infos');
                    return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('Oops, something gone wrong, please try again')
                );
            }
        }
        
        $params = array(
          'form' => $form->createView(),
            'user'=>$user
        );
        
       
        
        return $this->render('P4MBackofficeBundle:pages/profile:profile.html.twig',$params);
        
    }
    public function profileEditAction()
    {
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('picture')
                ->remove('plainPassword')
                ->remove('title')
               
                ;
        
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
                
//                $url = $this->generateUrl('p4_m_backoffice_homepage');
//                    return $this->redirect($url);
            }
        }
        
        $params = array(
          'form' => $form->createView(),
            'user'=>$user
        );
        return $this->render('P4MBackofficeBundle:pages/profile:profile.html.twig',$params);
        
    }
    
    public function myAccountPasswordAction()
  {
        $user_manager = $this->getDoctrine()->getManager();
  
        $user = $this->getUser();
        $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
        $form   ->remove('picture')
                ->remove('firstName')
                ->remove('lastName')
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
                ->remove('city')
                ->remove('country')
                ->remove('title')
                 ->remove('address')
                ->remove('birthDate')
            ->remove('language')
                ;
        
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
                    $url = $this->container->get('router')->generate('p4_m_backoffice_password');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->render('P4MBackofficeBundle:pages/password:password.html.twig',array('form'=>$form->createView(),'user'=>$user));
        
    }
    
    public function pictureEditAction()
    {

          $user = $this->getUser();
          $form = $this->createForm(new \P4M\UserBundle\Form\UserType(),$user); 
          $form   ->remove('plainPassword')
                  ->remove('firstName')
                  ->remove('lastName')
                  ->remove('website')
                  ->remove('publicStatus')
                  ->remove('name')
                  ->remove('surname')
                  ->remove('bio')
                  ->remove('skills')
                  ->remove('email')
                  ->remove('city')
                  ->remove('country')
//                  ->remove('categories')
//                  ->remove('tags')
                  ->remove('username')
                  ->remove('title')
                   ->remove('address')
                ->remove('language')
                ->remove('birthDate')
                  ;


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
                  
                    $url = $this->generateUrl('p4_m_backoffice_profile_picture');
                    return $this->redirect($url);
                  
              }
          }

           return $this->render('P4MBackofficeBundle:pages/picture:picture.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }
    
    
    
    
    
}
