<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminWPBlogPostController extends Controller
{
    
    
    public function wpBlogPostEditAction($wpPostId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $wpRepo = $em->getRepository('P4MBlogBundle:WPBlogPost');
        
        $post = $wpRepo->find($wpPostId);
        
        $editor = $this->getUser();
        
        if(null === $post)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('post not found');
        }
        
        
        $form = $this->createForm(new \P4M\BlogBundle\Form\WPBlogPostType(),$post);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
                 $post->setUser($user);
              $em->persist($post);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'post '.$post->getTitle().' Edited :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, post '.$post->getTitle().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'wpPost'=>$post
                );
        
        return $this->render('P4MAdminBundle:WPPost:post-edit.html.twig',$params);
    }
    
    public function WPBlogPostCreateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = new \P4M\BlogBundle\Entity\WPBlogPost();
        
        $editor = $this->getUser();
        
        $form = $this->createForm(new \P4M\BlogBundle\Form\WPBlogPostType(),$post);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
                $post->setUser($user);
              $em->persist($post);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Post '.$post->getTitle().' Created :)'
                );
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Post '.$post->getTitle().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'wpPost'=>$post
                );
        
        return $this->render('P4MAdminBundle:WPPost:post-edit.html.twig',$params);
    }
    
    
    public function WPBlogPostsAction()
    {
        $em = $this->getDoctrine()->getManager();
       $wpRepo = $em->getRepository('P4MBlogBundle:WPBlogPost');
        
        
        $posts = $wpRepo->findAll();
        
        
        
        $params = array
                (
                    'wpPosts'=>$posts
                );
        
        return $this->render('P4MAdminBundle:WPPost:post-list.html.twig',$params);
    }
    
    public function WPBlogPostDeleteAction($wpPostId)
    {
        $em = $this->getDoctrine()->getManager();
        $wpRepo = $em->getRepository('P4MBlogBundle:WPBlogPost');
        
        $post = $wpRepo->find($wpPostId);
//        $category->setDeleted(true);
        
        $em->remove($post);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            'Post '.$post->getTitle().' deleted'
        );
        
        $url = $this->generateUrl('p4m_admin_wpPostlist');
        return $this->redirect($url);
    }
    
}
