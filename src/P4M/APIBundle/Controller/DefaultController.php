<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Put;
use P4M\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Acl\Exception\Exception;

class DefaultController extends FOSRestController
{
    public function getDefaultAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }

    public function getTestAction()
    {
        $user = $this->getUser();
        $data = array("test" => "test",
            "user" => $user->getUsername());
        $view = $this->view($data);
        return $this->handleView($view);
    }

    public function getChargeWallet()
    {

    }

    /**
     * @ApiDoc(
     *  method="GET",
     *  resource=true,
     *  description="Create an user",
     *  parameters={
     *          {"name"="username", "dataType"="string", "required"=true, "description"="Votre username"},
     *          {"name"="email", "dataType"="email", "required"=true, "description"="Votre email"},
     *          {"name"="password", "dataType"="password", "required"=true, "description"="Votre password"},
     *          {"name"="first_name", "dataType"="string", "required"=true, "description"="Votre prénom"},
     *          {"name"="last_name", "dataType"="string", "required"=true, "description"="Votre nom"},
     *          {"name"="term_accepted", "dataType"="boolean", "required"=true, "description"="term accepted"},
     *     },
     *   statusCodes={
     *          600="User correctly create",
     *          601="Empty Username",
     *          602="Empty Email",
     *          603="Empty First Name",
     *          604="Empty Last Name",
     *          605="Empty Password",
     *          606="Bad email incremential",
     *          610="User already exist",
     *          611="Email already exist",
     *          640="Terms need to be accepted"
     *     }
     *
     * )
     * @return $Response
     */
    public function getRegisterAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $error = false;

        $response = [
            'message' => '',
            'status_codes' => ''
        ];
        $data = [
            'username' => $request->query->get('username'),
            'email' => $request->query->get('email'),
            'first_name' => $request->query->get('first_name'),
            'last_name' => $request->query->get('last_name'),
            'password' => $request->query->get('password'),
            'term_accepted' => $request->query->get('term_accepted')
        ];
        foreach ($data as $key => $value) {
            if ($value == null) {
                $response['message'] = $key . ' ne peu pas etre vide';
                switch($key){
                    case 'username':
                        $response['status_codes'] = 601;
                        break;
                    case 'email':
                        $response['status_codes'] = 602;
                        break;
                    case 'first_name':
                        $response['status_codes'] = 603;
                        break;
                    case 'last_name':
                        $response['status_codes'] = 604;
                        break;
                    case 'password':
                        $response['status_codes'] = 605;
                        break;
                }
                $error = true;
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !$error) {
            $response['message'] = 'l\'adresse email n\'est pas dans un format correct';
            $response['status_codes'] =
            $error = true;
        }
        if(!$data['term_accepted'] && !$error){
            $response['status_codes'] = 640;
            $response['message'] = 'Les termes du contract doivent être accepter';
        }

        if (!$error) {
            $user = new User();
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $user->setUsername($data['username']);
            $user->setLastName($data['last_name']);
            $user->setFirstName($data['first_name']);
            $user->setSalt(md5(time()));
            $pass = $encoder->encodePassword($data['password'], $user->getSalt());
            $user->setPassword($pass);
            $user->setEmail($data['email']);
            $user->setEnabled(true);
            $user->setSuperAdmin(false);
            $user->setTermsAccepted($data['term_accepted']);
            $em->persist($user);
            try {
                $em->flush();
                $response['message'] = 'L\utilisateur a bien été crée';
            } catch (Exception $e) {
                $response['message'] = 'Une erreur s\'est produite';
            }
        }
        $view = $this->view($response);

        return $this->handleView($view);
    }

}
