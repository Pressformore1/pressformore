<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function userListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('P4MUserBundle:User');
        
        $entities = $repo->findAll();
        
        
        return $this->render('SKCMSAdminBundle:Page:user-list.html.twig',['entities'=>$entities]);
    }
   
}
