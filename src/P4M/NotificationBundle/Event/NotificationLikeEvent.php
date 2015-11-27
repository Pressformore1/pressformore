<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificationEvent
 *
 * @author Jona
 */

namespace P4M\NotificationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use P4M\UserBundle\Entity\User;
use P4M\CoreBundle\Entity\Vote;


class NotificationLikeEvent extends Event 
{
    protected $vote;
    protected $user;
    
    
    public function __construct(User $user, Vote $vote)
    {
        $this->user = $user;
        $this->vote = $vote;

    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getVote()
    {
        return $this->vote;
    }
    
    
    
    
}
