<?php

namespace P4M\ConsoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('P4MConsoleBundle:Default:index.html.twig', array('name' => $name));
    }
}
