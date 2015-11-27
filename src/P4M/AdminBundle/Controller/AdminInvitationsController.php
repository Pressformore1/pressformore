<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminInvitationsController extends Controller
{
    
    
    public function invitationEditAction($invitationId)
    {
        $em = $this->getDoctrine()->getManager();
        $invitationRepo = $em->getRepository('P4MUserBundle:Invitation');
        
        $invitation = $invitationRepo->find($invitationId);
        
        $editor = $this->getUser();
        
        $invitation->setUser($editor);
        if(null === $invitation)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Invitation not found');
        }
        
        
        $form = $this->createForm(new \P4M\UserBundle\Form\InvitationType(),$invitation);
        $form   ->remove('code')
                ->remove('user')
                ->remove('sent');
        
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($invitation);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Invitation '.$invitation->getEmail().' Edited :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Invitation '.$invitation->getEmail().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'invitation'=>$invitation
                );
        
        return $this->render('P4MAdminBundle:Invitations:invitation-edit.html.twig',$params);
    }
    
    public function invitationSendFormAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $invitationsRepo = $em->getRepository('P4MUserBundle:Invitation');
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            
            $toSendList = $invitationsRepo->findMultipleById($request->request->get('invitation'));
            
            foreach ($toSendList as $invitation)
            {
                $this->sendInvitation($invitation);
                $invitation->setSent(true);
                $em->persist($invitation);
            }
            
            $em->flush();
            
        }
        
        
        $notSentInvitations = $invitationsRepo->findByIterations(0);
//        $notSentInvitations = $invitationsRepo->findBySent(false);
        
        $params = array
            (
                'invitations'=>$notSentInvitations
            );
        
        return $this->render('P4MAdminBundle:Invitations:invitation-send-list.html.twig',$params);
        
    }
    
    public function sendInvitation($invitation)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Invitation' )
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($invitation->getEmail())
            ->setBody($this->renderView('P4MUserBundle:email:invitation.html.twig', array('invitation' => $invitation)), 'text/html')
            ;
            $this->get('mailer')->send($message);
    }
    
    public function invitationCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $invitation = new \P4M\UserBundle\Entity\Invitation();
        
        $editor = $this->getUser();
        
        $invitation->setUser($editor);
        
        $form = $this->createForm(new \P4M\UserBundle\Form\InvitationType(),$invitation);
        
        $form   ->remove('user')
                ->remove('sent')
                ->remove('code');
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($invitation);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Email '.$invitation->getEmail().' Invited :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Email '.$invitation->getEmail().' not invited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'invitation'=>$invitation
                );
        
        return $this->render('P4MAdminBundle:Invitations:invitation-edit.html.twig',$params);
    }
    
    
    public function invitationsListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MUserBundle:Invitation');
        
        $flagTypes  = $flagRepo->findAll();
        
        $params = array
                (
                    'invitations'=>$flagTypes
                );
        
        return $this->render('P4MAdminBundle:Invitations:invitation-list.html.twig',$params);
    }
    
    public function invitationDeleteAction($invitationId)
    {
        $em = $this->getDoctrine()->getManager();
        $invitationRepo = $em->getRepository('P4MUserBundle:Invitation');
        
        $invitation = $invitationRepo->find($invitationId);
        
        
        $em->remove($invitation);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Invitation'.$invitation->getEmail().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_invitationList');
        return $this->redirect($url);
    }
    
}
