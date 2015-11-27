<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminPostFlagsController extends Controller
{
    
    
    public function flagEditAction($categoryId)
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MModerationBundle:FlagType');
        
        $category = $categoryRepo->find($categoryId);
        
        $editor = $this->getUser();
        
        if(null === $category)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('category not found');
        }
        
        
        $form = $this->createForm(new \P4M\AdminBundle\Form\Type\AdminCategoryType($editor),$category);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($category);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Categorie '.$category->getName().' Edited :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Categorie '.$category->getName().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'category'=>$category
                );
        
        return $this->render('P4MAdminBundle:Categories:category-edit.html.twig',$params);
    }
    
    public function flagCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $flagType = new \P4M\ModerationBundle\Entity\FlagType();
        
        $editor = $this->getUser();
        
        $form = $this->createForm(new \P4M\ModerationBundle\Form\FlagTypeType(),$flagType);
        
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
        
        return $this->render('P4MAdminBundle:Flag:flag-edit.html.twig',$params);
    }
    
    
    public function postFlagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $flagRepo = $em->getRepository('P4MModerationBundle:PostFlag');
        
        $flags  = $flagRepo->findAll();
        
        $params = array
                (
                    'postFlags'=>$flags
                );
        
        return $this->render('P4MAdminBundle:PostFlag:post-flags-list.html.twig',$params);
    }
    
    public function flagDeleteAction($categoryId)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository('P4MCoreBundle:Category');
        
        $category = $categoryRepo->find($categoryId);
        $category->setDeleted(true);
        
        $em->persist($category);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Categorie '.$category->getName().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_categorylist');
        return $this->redirect($url);
    }
    
}
