<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    
    
    public function notificationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $notifRepo = $em->getRepository('P4MNotificationBundle:Notification');
        
        $notifications = $notifRepo->findByUser($user);
        
        foreach ($notifications as $notif)
        {
            $notif->setRead(true);
            $em->persist($notif);
            
        }
        $em->flush();
        
        $params = array
        (
            'user'=>$user,
            'notifications'=>$notifications
        );
        
        return $this->render('P4MBackofficeBundle:pages/notification:notification.html.twig',$params);
    }
    
    
}
