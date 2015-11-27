<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostActivityListener
 *
 * @author Jona
 */

namespace P4M\PinocchioBundle\Event;

use P4M\PinocchioBundle\Entity\PostScore;
use P4M\CoreBundle\Entity\Post;
use P4M\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class PostActivityListener {
    
    protected $em;
    
    public function __construct($em)
    {
      $this->em  = $em;
    }
    


    
    // Méthode « technique » de liaison entre l'évènement et la fonctionnalité reine
    public function onPostActivity(PostActivityEvent $event)
    {
        $score = $event->getPost()->getScore();
        if ($score !== null)
        {
            $score->generateScore(); 
        }
        else
        {
            $score = new PostScore();
            $score->setPost($event->getPost());
            $score->generateScore();
        }
        
        $this->em->persist($score);
        $this->em->flush();
        
    }
}
