<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorController extends Controller
{
    public function wantedPressformListAction()
    {
        
        
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('P4MCoreBundle:WantPressform');
        
        $entities = $repo->findAllGroupped();
        
//        dump($entities);
//        die();
//        
        
        return $this->render('SKCMSAdminBundle:Page:wanted-pressform-list.html.twig',['entities'=>$entities]);
    }
    public function tempAuthorAction($postSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository('P4MCoreBundle:Post');
        
        $post = $postRepo->findOneBySlug($postSlug);
        
        if (null === $post)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('post Not found');
        }
        
        $repo = $em->getRepository('P4MCoreBundle:WantPressform');
        $wantPressforms = $repo->findByPost($post);
        
        $tempAuthor = $post->getTempAuthor();
        if (null === $post->getTempAuthor())
        {
            $tempAuthor = new \P4M\CoreBundle\Entity\TempAuthor();
            $tempAuthor->setPost($post);
        }
        
        $form = $this->createForm(new \P4M\CoreBundle\Form\TempAuthorType(),$tempAuthor);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
                
                
                
              $em->persist($tempAuthor);
              $em->persist($post);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'temp author Edited :)'
                );
//              if ($entity == "WallAutoFollow")
//              {
//                    
//              }
              $url = $this->generateUrl('sk_admin_temp_author',['postSlug'=>$postSlug]);
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Temp Author not edited, sorry, try again :/'
                );
            }
            
        }
        
          
        
        return $this->render('SKCMSAdminBundle:Page:temp-author.html.twig',['wantedPressforms'=>$wantPressforms,'post'=>$post,'form'=>$form->createView()]);
    }
}
