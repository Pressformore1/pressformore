<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminUserController extends Controller
{
    
    
    public function userEditAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $user = $userRepo->findOneByUsername($username);
        
        $editor = $this->getUser();
        
        if(null === $user)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('user not found');
        }
        
        
        $form = $this->createForm(new \P4M\AdminBundle\Form\Type\AdminUserType($editor),$user);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
          // On fait le lien Requête <-> Formulaire
          // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
          
            $form->bind($request);
          
          // On vérifie que les valeurs entrées sont correctes
          // (Nous verrons la validation des objets en détail dans le prochain chapitre)
          if ($form->isValid()) {
            // On l'enregistre notre objet $article dans la base de données
            $em->persist($user);
            $em->flush();
            // On redirige vers la page de visualisation de l'article nouvellement créé
            $this->get('session')->getFlashBag()->add(
            'success',
            'User '.$user->getUsername().' Edited :)'
            );
          }
          else
          {
               $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Categorie not'.$user->getUsername().' edited, sorry, try again :/'
                );
          }
          
        }
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'user'=>$user
                );
        
        return $this->render('P4MAdminBundle:users:user-edit.html.twig',$params);
    }
    
    
    public function usersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $users = $userRepo->findAll();
        
        $params = array
                (
                    'users'=>$users
                );
        
        return $this->render('P4MAdminBundle:users:user-list.html.twig',$params);
    }
    
    public function resendActivationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('P4MUserBundle:User');
        
        $users = $userRepo->findByEnabled(false);
        
        
      
        
        $userMailer = $this->get('fos_user.mailer');
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        
        foreach ($users as $user)
        {
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $userMailer->sendConfirmationEmailMessage($user);
        }
        
        
        $this->get('session')->getFlashBag()->add(
                    'success',
                    'ok, '.count($users).' emails sent'
                );
        $em->flush();
        
        $url = $this->generateUrl('p4m_admin_userlist');
        return $this->redirect($url);
//        return $this->render('P4MAdminBundle:users:user-list.html.twig',$params);
        
    }
    
}
