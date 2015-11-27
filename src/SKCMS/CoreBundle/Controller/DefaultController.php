<?php

namespace SKCMS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SKCMSCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
