<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\TrackingBundle\Entity\UserActivity;

class AdminNotificationTypeController extends Controller
{
    
    
    public function notificationTypeEditAction($notificationTypeId)
    {
        $em = $this->getDoctrine()->getManager();
        $typeRepo = $em->getRepository('P4MNotificationBundle:NotificationType');
        
        $type = $typeRepo->find($notificationTypeId);
        
        $editor = $this->getUser();
        
        $type->setUser($editor);
        if(null === $type)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Type not found');
        }
        
        $activity = new UserActivity;
        
        $form = $this->createForm(new \P4M\NotificationBundle\Form\NotificationTypeType($activity->getTypeAllowed()),$type);
        
        $form->remove('dateAdded');
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($type);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Type '.$type->getName().' Edited :)'
                );
              
              $url = $this->generateUrl('p4m_admin_notificationTypeList');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Post Tytpe '.$type->getName().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'notificationType'=>$type
                );
        
        return $this->render('P4MAdminBundle:NotificationType:notification-type-edit.html.twig',$params);
    }
    
    
    
    public function notificationTypeCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $notificationType = new \P4M\NotificationBundle\Entity\NotificationType();
        
        $editor = $this->getUser();
        
        $notificationType->setUser($editor);
        
         $activity = new UserActivity;
        
        $form = $this->createForm(new \P4M\NotificationBundle\Form\NotificationTypeType($activity->getTypeAllowed()),$notificationType);
        $form->remove('dateAdded');
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($notificationType);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'NotificationType '.$notificationType->getName().' created :)'
                );
              
              $url = $this->generateUrl('p4m_admin_notificationTypeList');
              return $this->redirect($url);
              
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, NotificationType '.$notificationType->getName().' not created, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'notificationType'=>$notificationType
                );
        
        return $this->render('P4MAdminBundle:NotificationType:notification-type-edit.html.twig',$params);
    }
    
    
    public function notificationTypeListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ptRepo = $em->getRepository('P4MNotificationBundle:NotificationType');
        
        $notificationTypes  = $ptRepo->findAll();
        
        $params = array
                (
                    'notificationTypes'=>$notificationTypes
                );
        
        return $this->render('P4MAdminBundle:NotificationType:notification-type-list.html.twig',$params);
    }
    
    public function notificationTypeDeleteAction($notificationTypeId)
    {
        $em = $this->getDoctrine()->getManager();
        $typeRepo = $em->getRepository('P4MNotificationBundle:NotificationType');
        
        $type = $typeRepo->find($notificationTypeId);
        
        
        $em->remove($type);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Type'.$type->getNAme().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_notificationTypeList');
        return $this->redirect($url);
    }
    
}
