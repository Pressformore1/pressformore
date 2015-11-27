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
 */
namespace P4M\NotificationBundle\Event\Listener;



use P4M\NotificationBundle\Event\NotificationEvent;
use P4M\NotificationBundle\Event\Listener\NotificationListener;
use P4M\NotificationBundle\Entity\Notification;


class LikeListener extends NotificationListener
{
    
    
    
    public function onNotification($event)
    {
        
        $typeRepo = $this->em->getRepository('P4MNotificationBundle:NotificationType');
        
        $type = $typeRepo->findOneByTypeLink($this->typeLinks['like']);
        
        $notification = new Notification();
        if ($event->getVote()->getPost() !== null)
        {
            $notification->setUser($event->getVote()->getPost()->getUser());
        }
        else if ($event->getVote()->getComment() !== null)
        {
            $notification->setUser($event->getVote()->getComment()->getUser());
        }
        else if ($event->getVote()->getWall() !== null)
        {
            $notification->setUser($event->getVote()->getWall()->getUser());
        }
        
        $notification->setType($type);
        $notification->setVote($event->getVote());
       
        $this->em->persist($notification);
//         die('score '.$notification->getVote()->getScore());
//        $this->em->flush();
        $classMetadata = $this->em->getClassMetadata(get_class($notification));
        $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $notification); // We need to manually tell EM to notice the changes
        
    }
}
