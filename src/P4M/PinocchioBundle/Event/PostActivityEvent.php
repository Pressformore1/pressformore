<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostActivityEvent
 *
 * @author Jona
 */
namespace P4M\PinocchioBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use P4M\UserBundle\Entity\User;


class PostActivityEvent extends Event
{
    protected $post;
    protected $user;
    
    public function __construct($post, User $user = null)
    {
      $this->post  = $post;
      $this->user     = $user;
    }
  
    // Le listener doit avoir accès au message
    public function getPost()
    {
      return $this->post;
    }

    // Le listener doit pouvoir modifier le message
    public function setPost($post)
    {
      return $this->post = $post;
    }

    // Le listener doit avoir accès à l'utilisateur
    public function getUser()
    {
      return $this->user;
    }
}
