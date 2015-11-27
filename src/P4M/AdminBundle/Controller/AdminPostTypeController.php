<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminPostTypeController extends Controller
{
    
    
    public function postTypeEditAction($postTypeId)
    {
        $em = $this->getDoctrine()->getManager();
        $typeRepo = $em->getRepository('P4MCoreBundle:PostType');
        
        $type = $typeRepo->find($postTypeId);
        
        $editor = $this->getUser();
        
        $type->setUser($editor);
        if(null === $type)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Type not found');
        }
        
        
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostTypeType(),$type);
        
        
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($type);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Type '.$type->getName().' Edited :)'
                );
              
              $url = $this->generateUrl('p4m_admin_postTypeList');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Post Tytpe '.$type->getName().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'postType'=>$type
                );
        
        return $this->render('P4MAdminBundle:PostType:post-type-edit.html.twig',$params);
    }
    
    
    
    public function postTypeCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $postType = new \P4M\CoreBundle\Entity\PostType();
        
        $editor = $this->getUser();
        
        $postType->setUser($editor);
        
        $form = $this->createForm(new \P4M\CoreBundle\Form\PostTypeType(),$postType);
        
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($postType);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'PostType '.$postType->getName().' created :)'
                );
              
              $url = $this->generateUrl('p4m_admin_postTypeList');
              return $this->redirect($url);
              
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, PostType '.$postType->getName().' not created, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'postType'=>$postType
                );
        
        return $this->render('P4MAdminBundle:PostType:post-type-edit.html.twig',$params);
    }
    
    
    public function postTypeListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ptRepo = $em->getRepository('P4MCoreBundle:PostType');
        
        $postTypes  = $ptRepo->findAll();
        
        $params = array
                (
                    'postTypes'=>$postTypes
                );
        
        return $this->render('P4MAdminBundle:PostType:post-type-list.html.twig',$params);
    }
    
    public function postTypeDeleteAction($postTypeId)
    {
        $em = $this->getDoctrine()->getManager();
        $typeRepo = $em->getRepository('P4MCoreBundle:PostType');
        
        $type = $typeRepo->find($postTypeId);
        
        
        $em->remove($type);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Type'.$type->getNAme().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_postTypeList');
        return $this->redirect($url);
    }
    
}
