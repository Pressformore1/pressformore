<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RegisterUserController extends FOSRestController
{
    /**
     * @ApiDoc(
     *
     *  resource=true,
     *  description="Create an User",
     *  parameters={
     *          {"name"="username", "dataType"="string", "required"=true, "description"="Votre username"},
     *          {"name"="email", "dataType"="string", "required"=true, "description"="Votre email"},
     *          {"name"="first_name", "dataType"="string", "required"=true, "description"="Votre prénom"},
     *          {"name"="last_name", "dataType"="string", "required"=true, "description"="Votre nom"},
     *          {"name"="term_accepted", "dataType"="boolean", "required"=true, "description"="term accepted"},
     *          {"name"="password", "dataType"="string", "required"=true, "description"="term accepted"}
     *     }
     * )
     */
    public function RegisterAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        $response = [
            'message' => ''
        ];
        $data = [
            'username' => $request->query->get('username'),
            'email' => $request->query->get('email'),
            'first_name' => $request->query->get('first_name'),
            'last_name' => $request->query->get('last_name'),
            'password' => $request->query->get('password'),
            'term_accepted' => true

        ];

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
        $em->flush();


        //$user = $this->get('fos_user.util.user_manipulator')->create($data['username'], $data['password'], $data['email'], 1, 0);
        $view = $this->view($user);

        return $this->handleView($view);
    }
}
