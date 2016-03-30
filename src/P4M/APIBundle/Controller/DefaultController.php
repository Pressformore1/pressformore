<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Put;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Form\PostType;
use P4M\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Acl\Exception\Exception;
use DateTime;

class DefaultController extends FOSRestController
{
    private $response = array();

    public function __construct()
    {
        $this->response = [
            'message' => '',
            'status_codes' => ''
        ];
    }

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


    /**
     * @ApiDoc(
     *     description="add a post"
     * )
     */
    public function getAddPostAction(){
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mCore.post_utils');
        $request = $this->get('request');

        $post = new Post();
        $form = $this->get('form.factory')->create(new PostType(), $post);
        $form->remove('blogPost')
            ->remove('anchors');
        if(!empty($url = $request->query->get('url'))){
            $this->response['url']= $url;
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $langueRepo = $em->getRepository('P4MCoreBundle:Lang');

            $post = $postRepo->findOneBySourceUrl($url);

            if(null === $post){
                $metas = $userUtils->grabMetas($url);
            }
            $this->response['metas'] = $metas;
            $this->response['form'] = $form;

            $view = $this->view($this->response);
            return $this->handleView($view);
        }



        $response = [
            'message' => '',
            'status_codes' => '',
            'form' => $form,
        ];
        $view = $this->view($response);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  method="GET",
     *  resource=true,
     *  description="Load a wallet",
     *  parameters={
     *      {"name"="cardNumber", "dataType"="string", "required"=true, "description"="card number"},
     *      {"name"="cardExpirationDate", "dataType"="string", "required"=true, "description"="card expiration date"},
     *      {"name"="cardCvx", "dataType"="string", "required"=true, "description"="card Cvx"},
     *     }
     * )
     */
    public function getChargeWalletAction()
    {
        $user = $this->getUser();
        $response = [
            'message' => '',
            'status_codes' => '',
            'user' => $user->getUsername()
        ];
        $view = $this->view($response);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  method="GET",
     *  resource=true,
     *  description="Create an user",
     *  parameters={
     *          {"name"="username", "dataType"="string", "required"=true, "description"="your username"},
     *          {"name"="email", "dataType"="email", "required"=true, "description"="your email"},
     *          {"name"="password", "dataType"="password", "required"=true, "description"="your password"},
     *          {"name"="first_name", "dataType"="string", "required"=true, "description"="your first name"},
     *          {"name"="last_name", "dataType"="string", "required"=true, "description"="your last name"},
     *          {"name"="term_accepted", "dataType"="boolean", "required"=true, "description"="term accepted"},
     *     },
     *   statusCodes={
     *          200="User correctly create",
     *          601="Empty Username",
     *          602="Empty Email",
     *          603="Empty First Name",
     *          604="Empty Last Name",
     *          605="Empty Password",
     *          615="Bad email format",
     *          620="User already exist",
     *          621="Email already exist",
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
        // Vérifie si les donnée requies son remplie
        $response = $this->checkData($data, $response);


        // Vérifie si les termes son accepté
        if(!$data['term_accepted'] && empty($response['status_codes'])){
            $response['status_codes'] = 640;
            $response['message'] = 'Les termes du contract doivent être accepter';
        }

        // Crée un utilisateur
        if (empty($response['status_codes'])) {
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
                $response['status_codes'] = 200;
            } catch (Exception $e) {
                $response['message'] = 'Une erreur s\'est produite';
            }
        }
        $view = $this->view($response);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     method="GET",
     *     resource=true,
     *     description="Complete your inscription",
     *     parameters={
     *          {"name"="address", "dataType"="string", "required"=true, "description"="your adress"},
     *          {"name"="city", "dataType"="string", "required"=true, "description"="your city"},
     *          {"name"="country", "dataType"="object()", "required"=true, "description"="your country"},
     *          {"name"="birth_date", "dataType"="date('Year-Month-Day')", "required"=true, "description"="your birth date"},
     *          {"name"="language", "dataType"="string", "required"=false, "description"="Default en can be en or fr"},
     *          {"name"="email", "dataType"="email", "required"=false, "description"="your email"},
     *          {"name"="first_name", "dataType"="string", "required"=false, "description"="your first name"},
     *          {"name"="last_name", "dataType"="string", "required"=false, "description"="your last name"},
     *          {"name"="website", "dataType"="string", "required"=false, "description"="your website"},
     *          {"name"="bio", "dataType"="text", "required"=false, "description"="your biography"},
     *          {"name"="skills", "dataType"="text", "required"=false, "description"="your skills"},
     *     },
     *     statusCodes={
     *              200="User Correctly updated",
     *              606="Empty Adresse",
     *              607="Empty City",
     *              608="Empty Country",
     *              609="Bad birth date",
     *              615="Bad email format"
     *     }
     * )
     * @return $Response
     */
    public function getCompleteRegisterAction(){
        $em = $this->getDoctrine()->getManager();
        $user =$this->getUser();
        $request = $this->container->get('request_stack')->getCurrentRequest();


        // Envoie les donnée de base concernant l'utilisateur
        if($request->query->count() == 0){
            $data = [
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'country' => $user->getCountry(),
                'birth_date' => $user->getBirthDate(),
                'language' => $user->getLanguage(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'website' => $user->getWebsite(),
                'bio' => $user->getBio(),
                'skills' => $user->getSkills(),
            ];
            $view = $this->view($data);
            return $this->handleView($view);
        }
        $response= [
            'message' => '',
            'status_codes' => ''
        ];
        //hydrate les données reçues
        $data = [
            'address' => ($request->query->get('address')) ? $request->query->get('address') : $user->getAddress(),
            'city' => ($request->query->get('city')) ? $request->query->get('city') : $user->getCity(),
            'country' => $request->query->get('country'),
            'birth_date' => ($request->query->get('birth_date')) ? new DateTime($request->query->get('birth_date')) : $user->getBirthDate(),
            'language' => ($request->query->get('language')) ? $request->query->get('language') : 'en',
            'email' => ($request->query->get('email')) ? $request->query->get('email') : $user->getEmail(),
            'first_name' => ($request->query->get('first_name')) ? $request->query->get('last_name') : $user->getFirstName(),
            'last_name' => ($request->query->get('last_name')) ? $request->query->get('last_name') : $user->getLastName(),
            'website' => $request->query->get('website'),
            'bio' => $request->query->get('bio'),
            'skills' => $request->query->get('skills'),
        ];
        $response = $this->checkData($data, $response);

        // sauvegarde les données utilisateurs
        if(empty($response['status_codes'])){
            $user->setAddress($data['address']);
            $user->setCity($data['city']);
            if(!empty($data['country'])){
                $user->setCountry($em->getRepository('P4MUserBundle:Country')->find($data['country']));
            }
            $user->setBirthDate($data['birth_date']);
            $user->setLanguage($data['language']);
            $user->setEmail($data['email']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            $user->setWebsite($data['website']);
            $user->setBio($data['bio']);


            //crée un utilisateur mango s'il n'existe pas
            $mangoUser = $user->getMangoUserNatural();
            $userUtil = $this->container->get('p4mUser.user_utils');
            if (null ==$mangoUser && $userUtil->isMangoUserAvailable($user))
            {
                $mango = $this->container->get('p4_m_mango_pay.util');
                $mangoUser = $mango->createUser($user);
                $wallets = $mango->createWallet($mangoUser);
            }
            try{
                $em->persist($user);
                $em->flush();
                $response['status_codes'] = 200;
                $response['message'] = 'Votre compte a bien été mis à jour';
            }catch(Exception $e){
                $response['status_codes'] = 'unknown';
                $response['message'] = "Votre compte n'a pas été mis à jour";
            }
        }

        $view = $this->view($response);
        return $this->handleView($view);
    }


    // Vérifie si les donnés son correctement remplies
    public function checkData($data, $response){
        foreach ($data as $key => $value) {
            if ($value == null) {
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
                    case 'address':
                        $response['status_codes'] = 606;
                        break;
                    case 'city':
                        $response['status_codes'] = 607;
                        break;
                    case 'country':
                        $response['status_codes'] = 608;
                        break;
                    case 'birth_date':
                        $response['status_codes'] = 609;
                        break;
                }
                if(!empty($response['status_codes'])){
                    $response['message'] = $key . ' ne peu pas etre vide';
                    return $response;
                }
            }
        }

        // Vérifie si l'émail est valide
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) && empty($response['status_codes'])) {
            $response['message'] = 'l\'adresse email n\'est pas dans un format correct';
            $response['status_codes'] = 615;
        }

        return $response;
    }
}
