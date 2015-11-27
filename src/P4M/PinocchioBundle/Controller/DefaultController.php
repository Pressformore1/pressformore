<?php

namespace P4M\PinocchioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('P4MPinocchioBundle:Default:index.html.twig', array('name' => $name));
    }
}
