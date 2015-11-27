<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminMailAccountController extends Controller
{
    
    
    public function accountEditAction($categoryId)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository('P4MCoreBundle:Category');
        
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
    
    public function accountCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $category = new \P4M\CoreBundle\Entity\Category();
        
        $editor = $this->getUser();
        
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
                'Categorie '.$category->getName().' Created :)'
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
    
    
    public function accountsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $accountsRepo = $em->getRepository('P4MMailBundle:Compte');
        
        $accounts = $accountsRepo->findAll();
        
        
        $params = array
                (
                    'accounts'=>$accounts
                );
        
        return $this->render('P4MAdminBundle:Mail:account-list.html.twig',$params);
    }
    
    public function accountDeleteAction($categoryId)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository('P4MCoreBundle:Category');
        
        $category = $categoryRepo->find($categoryId);
//        $category->setDeleted(true);
        
        $em->remove($category);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Categorie '.$category->getName().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_categorylist');
        return $this->redirect($url);
    }
    
}
