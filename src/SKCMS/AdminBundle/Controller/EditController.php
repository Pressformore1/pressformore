<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditController extends Controller
{
    public function indexAction($entity,$id)
    {
        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['bundle'].'Bundle:'.$entity);
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new $entityParams['class'];
        }
        
        if (isset($entityParams['formParams']) && count($entityParams['formParams']))
        {
            if (isset($entityParams['formParams']['user']) && $entityParams['formParams']['user'] == 'current')
            {
                $form = $this->createForm(new $entityParams['form']($user),$entity);
            }
            if (isset($entityParams['formParams']['method']))
            {
                $class = new $entityParams['formParams']['class'];
                $formParams = call_user_func( array( $class, $entityParams['formParams']['method'] ) );
//                die('t'.$formParams);
                $form = $this->createForm(new $entityParams['form']($formParams),$entity);
            }
        }
        else
        {
            $form = $this->createForm(new $entityParams['form'],$entity);
        }
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($entity);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                $entityParams['beautyName'].' Edited :)'
                );
              $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, '.$entityParams['beautyName'].' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        return $this->render('SKCMSAdminBundle:Page:edit.html.twig',['entityParams'=>$entityParams,'entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function deleteAction($entity,$id)
    {
        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['bundle'].'Bundle:'.$entity);
        $entity = $repo->find($id);
        
        
        $em->remove($entity);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
                    'success',
                    $entityParams['beautyName'].' deleted'
                );
        
        $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
        return $this->redirect($url);
        
    }
}
