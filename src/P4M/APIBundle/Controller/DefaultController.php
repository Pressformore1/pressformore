<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use P4M\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
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


    /**
     * @Rest\Post("register")
     * @ApiDoc(
     *  resource="Register",
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
     * )
     * @param Request $request
     * @return Response
     * @View()
     */
    public function postRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $this->response['data'] = $data;


        // Vérifie si les termes son accepté
        if (!$data['term_accepted']) {
            $this->response['status_codes'] = 640;
            $this->response['message'] = 'Les termes du contract doivent être accepter';
            return $this->response;
        }
        if (!empty($data['email'])) {
            $test_email = $em->getRepository('P4MUserBundle:User')->findOneBy(['email' => $data['email']]);
            if ($test_email !== null) {
                $this->response['status_codes'] = 621;
                $this->response['message'] = 'Cette adresse email existe déjà';
                return $this->response;
            }
        }
        if(!empty($data['username'])){
            $test_username = $em->getRepository('P4MUserBundle:User')->findOneBy(['username' => $data['username']]);
            if($test_username !== null ){
                $this->response['status_codes'] = 620;
                $this->response['message'] = 'Cette utilisateur existe déjà';
                return $this->response;
            }
        }

        // Crée un utilisateur
        if ($this->checkData($data, 'REGISTER')) {
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
                $this->response['message'] = 'L\utilisateur a bien été crée';
                $this->response['status_codes'] = 200;
            } catch (Exception $e) {
                $this->response['message'] = 'Une erreur s\'est produite';
            }
        }
        return $this->response;
    }

    /**
     * @Rest\Post("complete/register")
     * @ApiDoc(
     *     resource="Register",
     *     description="Complete your inscription",
     *     parameters={
     *          {"name"="address", "dataType"="string", "required"=true, "description"="your address"},
     *          {"name"="city", "dataType"="string", "required"=true, "description"="your city"},
     *          {"name"="country", "dataType"="integer", "required"=true, "description"="your country"},
     *          {"name"="birth_date", "dataType"="Year-Month-Day", "required"=true, "description"="your birth date"},
     *          {"name"="language", "dataType"="string", "required"=false, "description"="Default en can be en or fr"},
     *          {"name"="email", "dataType"="email", "required"=false, "description"="your email"},
     *          {"name"="first_name", "dataType"="string", "required"=true, "description"="your first name"},
     *          {"name"="last_name", "dataType"="string", "required"=true, "description"="your last name"},
     *          {"name"="website", "dataType"="string", "required"=false, "description"="your website"},
     *          {"name"="bio", "dataType"="text", "required"=false, "description"="your biography"},
     *          {"name"="skills", "dataType"="text", "required"=false, "description"="your skills"},
     *     },
     *     statusCodes={
     *              200="User Correctly updated",
     *              603="Empty First Name",
     *              604="Empty Last Name",
     *              606="Empty Adresse",
     *              607="Empty City",
     *              608="Empty Country",
     *              609="Bad birth date",
     *              615="Bad email format"
     *     }
     * )
     * @param Request $request
     * @return Response
     * @View()
     */
    public function postCompleteRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getFirstLogin()===true)
        {
            $user->setFirstLogin(false);
            $em->persist($user);
            $em->flush();
        }
        //hydrate les données reçues
        $data = $request->request->all();
        $this->response['count'] = $request->request->count();
        // sauvegarde les données utilisateurs
        if ($this->checkData($data, 'FULL_REGISTER')) {
            $user->setAddress($data['address']);
            $user->setCity($data['city']);
            if (!empty($data['country']))
                $user->setCountry($em->getRepository('P4MUserBundle:Country')->find($data['country']));
            $user->setBirthDate(new DateTime($data['birth_date']));
            if (!empty($data['language']))
                $user->setLanguage($data['language']);
            $user->setEmail($data['email']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            if (!empty($data['website']))
                $user->setWebsite($data['website']);
            if (!empty($data['bio']))
                $user->setBio($data['bio']);
            //crée un utilisateur mango s'il n'existe pas
            $mangoUser = $user->getMangoUserNatural();
            $userUtil = $this->container->get('p4mUser.user_utils');
            if (null == $mangoUser && $userUtil->isMangoUserAvailable($user)) {
                $mango = $this->container->get('p4_m_mango_pay.util');
                $mangoUser = $mango->createUser($user);
                $wallets = $mango->createWallet($mangoUser);
            }
            try {
                $em->persist($user);
                $em->flush();
                $this->response['status_codes'] = 200;
                $this->response['message'] = 'Votre compte a bien été mis à jour';
            } catch (Exception $e) {
                $this->response['status_codes'] = 'unknown';
                $this->response['message'] = "Votre compte n'a pas été mis à jour";
            }
        }
        return $this->response;
    }
    
    /**
     * @ApiDoc(
     *     resource="Register",
     *     description="get information about current user"
     * )
     * @View(serializerGroups={"json"})
     * @return Response
     */
    public function getCompleteRegisterAction()
    {
        $user = $this->getUser();
        return $user;
    }


    // Vérifie si les donnés son correctement remplies
    public function checkData($data, $type)
    {
        switch ($type) {
            case 'FULL_REGISTER':
                $this->response['data'] = $data;
                if (!array_key_exists('address', $data)) {
                    $this->response['message'] = 'Empty Address';
                    $this->response['status_codes'] = 606;
                    return false;
                }
                if (!array_key_exists('city', $data)) {
                    $this->response['message'] = 'Empty City';
                    $this->response['status_codes'] = 607;
                    return false;
                }
                if (!array_key_exists('country', $data)) {
                    $this->response['message'] = 'Empty Country';
                    $this->response['status_codes'] = 608;
                    return false;
                }
                if (!array_key_exists('birth_date', $data)) {
                    $this->response['message'] = 'Empty Birth Date';
                    $this->response['status_codes'] = 609;
                    return false;
                }
            case 'REGISTER':
                if (!array_key_exists('username', $data) && $type !== 'FULL_REGISTER') {
                    $this->response['message'] = 'Empty Username';
                    $this->response['status_codes'] = 601;
                    return false;
                }
                if (!array_key_exists('email', $data)) {
                    $this->response['message'] = 'Empty email';
                    $this->response['status_codes'] = 602;
                    return false;
                }
                if (!array_key_exists('password', $data) && $type !== 'FULL_REGISTER') {
                    $this->response['message'] = 'Empty password';
                    $this->response['status_codes'] = 605;
                    return false;
                }
                if (!array_key_exists('first_name', $data)) {
                    $this->response['message'] = 'Empty first name';
                    $this->response['status_codes'] = 603;
                    return false;
                }
                if (!array_key_exists('last_name', $data)) {
                    $this->response['message'] = 'Empty last name';
                    $this->response['status_codes'] = 604;
                    return false;
                }
                if (!array_key_exists('term_accepted', $data) && $type !== 'FULL_REGISTER') {
                    $this->response['message'] = 'Empty term accepted';
                    $this->response['status_codes'] = 609;
                    return false;
                }
                break;
        }
        return true;
    }


}
