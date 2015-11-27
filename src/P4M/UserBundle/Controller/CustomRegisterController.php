<?php
// src/Sdz/UserBundle/Controller/SecurityController.php;

namespace P4M\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;


class CustomRegisterController extends Controller
{
  public function registerChoiceAction()
  {
      $params = array();
      return $this->render('P4MUserBundle:Registration:choice.html.twig',$params);
  }
  
 
  
  
  
}
