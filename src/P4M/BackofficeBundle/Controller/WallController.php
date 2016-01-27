<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Wall;

class WallController extends Controller
{
    
    
    public function walls()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        
        
    }
    
   public function wallEditAction($wallSlug = null)
    {
       
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getUser();
        if (null === $wallSlug)
        {
            $wall = new Wall();
        }
        else
        {
            $wallRepo = $em->getRepository('P4MCoreBundle:Wall');
            $wall = $wallRepo->findOneBySlug($wallSlug);
        }
        
        $wall->setUser($user);
        $form = $this->createForm(new \P4M\CoreBundle\Form\WallType(),$wall);
        
       
//        $form   ->remove('excludedTags')
//                ->remove('excludedPeople')
//                ->remove('excludedCategories');
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            $form->bind($request);
          
            if ($form->isValid()) 
            {
                
              $em->persist($wall);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'wall '.$wall->getName().' Created :)'
                );
              return $this->redirect($this->generateUrl('p4_m_backoffice_homepage').'#'.$this->generateUrl('p4_m_backoffice_wallEdit',array('wallSlug'=>$wall->getSlug())));
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Wall '.$wall->getName().' not edited, sorry, try again :/'
                );
//                die ($form->getErrorsAsString());
                return $this->redirect($this->generateUrl('p4_m_backoffice_homepage').'#'.$this->generateUrl('p4_m_backoffice_wallAdd'));
            }
            
        }
        
        
        
        
        $params = array
        (
            'form'=>$form->createView(),
            'user'=>$user
        );
        
        return $this->render('P4MBackofficeBundle:pages/wall:add-wall.html.twig',$params);
    }
    
    
    
}
