<?php

namespace P4M\TrackingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('P4MTrackingBundle:Default:index.html.twig', array('name' => $name));
    }
}
