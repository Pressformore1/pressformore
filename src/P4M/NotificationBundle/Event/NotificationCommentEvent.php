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
use P4M\CoreBundle\Entity\Comment;
use P4M\CoreBundle\Entity\Post;


class NotificationCommentEvent extends Event 
{
    protected $comment;
    protected $user;
    protected $post;
    
    
    public function __construct(User $user, Comment $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->post = $comment->getPost();

    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getComment()
    {
        return $this->comment;
    }
    
    public function getPost()
    {
        return $this->post;
    }
    
    
    
    
}
