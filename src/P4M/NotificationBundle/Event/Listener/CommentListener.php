<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LikeListener
 *
 * @author Jona
 * 
 * @Todo Comment on wall
 */
namespace P4M\NotificationBundle\Event\Listener;



use P4M\NotificationBundle\Event\NotificationEvent;
use P4M\NotificationBundle\Event\Listener\NotificationListener;
use P4M\NotificationBundle\Entity\Notification;


class CommentListener extends NotificationListener
{
    
    
    
    public function onNotification($event)
    {
        
        $typeRepo = $this->em->getRepository('P4MNotificationBundle:NotificationType');
        
        $type = $typeRepo->findOneByTypeLink($this->typeLinks['comment']);
        
        $notification = new Notification();
        
        $notification->setUser($event->getPost()->getUser());
        $notification->setType($type);
        $notification->setComment($event->getComment());
        $notification->setPost($event->getPost());
        
         $this->em->persist($notification);
//         die('score '.$notification->getVote()->getScore());
//        $this->em->flush();
        $classMetadata = $this->em->getClassMetadata(get_class($notification));
        $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $notification); // We need to manually tell EM to notice the changes
        
        if (null !== $event->getComment()->getParent())
        {
             $notificationReply = new Notification();
        
            $notificationReply->setUser($event->getComment()->getParent()->getUser());
            $notificationReply->setType($type);
            $notificationReply->setComment($event->getComment());
            $notificationReply->setPost($event->getPost());
            
            $this->em->persist($notificationReply);
            $classMetadata = $this->em->getClassMetadata(get_class($notificationReply));
            $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $notificationReply); // We need to manually tell EM to notice the changes
        }
        
        
       
       
        
    }
}
