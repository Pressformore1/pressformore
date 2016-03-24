<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class DefaultController extends FOSRestController
{
    public function getDefaultAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }
    public function getTestAction()
    {
        $data = array("test" => "test");
        $view = $this->view($data);
        return $this->handleView($view);
    }

}
