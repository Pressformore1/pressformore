<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListController extends Controller
{
    public function indexAction($entity,$page)
    {
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
//        die(print_r($entitiesParams,true));
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['bundle'].'Bundle:'.$entity);
        
        $entities = $repo->findAll();
        
        
        
        return $this->render('SKCMSAdminBundle:Page:list.html.twig',['entityParams'=>$entityParams,'entities'=>$entities]);
    }
}
