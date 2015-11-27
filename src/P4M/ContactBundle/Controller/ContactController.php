<?php

/**
 * Description of AdminController
 *
 * @author Jona
 */

namespace P4M\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \P4M\ContactBundle\Entity\ContactMessage;
class ContactController extends Controller
{
    
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $message = new ContactMessage();
        $form = $this->createForm(new \P4M\ContactBundle\Form\ContactMessageType(),$message);
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'user'=>$user
                );
        
         
        $view = $this->renderView('P4MContactBundle::contact.html.twig',$params);
        
        $json_options = array
            (
            'status' => 1,
            'action'=>'homeRefresh',
            'data'=>['content'=>$view]
            );
        
        return new Response(json_encode($json_options));
    }
    
    
    public function submitAction()
    {
//        die('aaargh');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $message = new ContactMessage();
        $form = $this->createForm(new \P4M\ContactBundle\Form\ContactMessageType(),$message);
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
            
            $params = json_decode($request->request->get('params'),true);
            $toBind = $params['p4m_contactbundle_contactmessage'];
            
            
//            die(print_r($,true));
            $form->bind($toBind);
          
         
          if ($form->isValid()) {
                $em->persist($message);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add(
                'success',
                'Thank you for your message'
                );
                $flashMessage = 'Thank you for your message';
                
                $messageMail = \Swift_Message::newInstance()
                     ->setSubject('Pressformore Contact Form' )
                    ->setFrom($this->container->getParameter('mailer_user'))
                    ->setTo('contact@pressformore.com')
                    ->addReplyTo($message->getEmail())
                    ->setBody($message->getName().' wrote : <br />'.$message->getMessage(), 'text/html')
                ;
                $this->get('mailer')->send($messageMail);
                      }
          else
          {
               $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, A problem occured sorry, try again :/'
                );
               $flashMessage = 'Oops, A problem occured sorry, try again :/';
          }
          
        }
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'user'=>$user,
                    'flashMessage'=>$flashMessage
                );
        
         
        $view = $this->renderView('P4MContactBundle::contact.html.twig',$params);
        
        $json_options = array
            (
            'status' => 1,
            'action'=>'homeRefresh',
            'data'=>['content'=>$view]
            );
        
        return new Response(json_encode($json_options));
    }
   
    
}
