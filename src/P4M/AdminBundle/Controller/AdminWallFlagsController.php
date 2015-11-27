<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminWallFlagsController extends Controller
{
    
    
    public function flagEditAction($flagId)
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MModerationBundle:WallFlagType');
        
        $flagType = $flagRepo->find($flagId);
        
        $editor = $this->getUser();
        
        if(null === $category)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Flag not found');
        }
        
        
        $form = $this->createForm(new \P4M\ModerationBundle\Form\WallFlagTypeType(),$flagType);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($flagType);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Flag '.$flagType->getName().' Edited :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Flag '.$flagType->getName().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'flag'=>$flagType
                );
        
        return $this->render('P4MAdminBundle:WallFlag:flag-edit.html.twig',$params);
    }
    
    public function flagCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $flagType = new \P4M\ModerationBundle\Entity\WallFlagType();
        
        $editor = $this->getUser();
        
        $form = $this->createForm(new \P4M\ModerationBundle\Form\WallFlagTypeType(),$flagType);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($flagType);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Flag '.$flagType->getName().' Created :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Flag '.$flagType->getName().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'flag'=>$flagType
                );
        
        return $this->render('P4MAdminBundle:WallFlag:flag-edit.html.twig',$params);
    }
    
    
    public function flagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MModerationBundle:WallFlagType');
        
        $flagTypes  = $flagRepo->findAll();
        
        $params = array
                (
                    'flags'=>$flagTypes
                );
        
        return $this->render('P4MAdminBundle:WallFlag:flags-list.html.twig',$params);
    }
    
    public function flagDeleteAction($flagId)
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MModerationBundle:WallFlagType');
        
        $flagType = $flagRepo->find($flagId);
        
        
        $em->remove($flagType);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Flag '.$category->getName().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_flagList');
        return $this->redirect($url);
    }
    
}
