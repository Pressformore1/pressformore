<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificationListener
 *
 * @author Jona
 */

namespace P4M\NotificationBundle\Event\Listener;

use P4M\CoreBundle\Entity\Post;
use P4M\UserBundle\Entity\User;

use P4M\NotificationBundle\Event\NotificationEvent;
use P4M\NotificationBundle\Entity\Notification;


abstract class NotificationListener {
    protected $em;
    protected $typeLinks;
    
    public function __construct($em)
    {
        $this->em = $em;
//        $this->typeLinks = $typeLinks;
    }
    
    
    abstract public function onNotification($event);
    
}
