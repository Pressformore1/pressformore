<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use P4M\CoreBundle\Entity\Image;
use P4M\UserBundle\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use P4M\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;
use DateTime;
use Symfony\Component\Validator\Constraints\IsTrue;

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

//        $user = new User();
//        $form = $this->get('form.factory')->createNamed(null, UserType::class, $user)
//            ->remove('language')->remove('title')->remove('website')->remove('address')
//            ->remove('city')->remove('country')->remove('birthDate')->remove('website')
//            ->remove('bio')->remove('picture')->remove('publicStatus')->remove('skills')
//            ->remove('bio')->add('termsAccepted', CheckboxType::class, ['constraints' => new isTrue() ]);
//        $form->submit($request->request->all());
//        if ($form->isSubmitted() AND $form->isValid()) {
//            $password = $this->get('security.password_encoder')
//                ->encodePassword($user, $user->getPlainPassword());
//            $user->setPassword($password);
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($user);
//            $em->flush();
//            return $user;
//        }
//
//        return $form->getErrors();

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
        if (!empty($data['username'])) {
            $test_username = $em->getRepository('P4MUserBundle:User')->findOneBy(['username' => $data['username']]);
            if ($test_username !== null) {
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
            $user->setTermsAccepted(1);
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
     *          {"name"="birthDate", "dataType"="array", "required"=true, "description"="your birth date"},
     *          {"name"="language", "dataType"="string", "required"=false, "description"="Default en can be en or fr"},
     *          {"name"="email", "dataType"="email", "required"=false, "description"="your email"},
     *          {"name"="firstName", "dataType"="string", "required"=true, "description"="your first name"},
     *          {"name"="lastName", "dataType"="string", "required"=true, "description"="your last name"},
     *          {"name"="website", "dataType"="string", "required"=false, "description"="your website"},
     *          {"name"="bio", "dataType"="text", "required"=false, "description"="your biography"},
     *          {"name"="skills", "dataType"="text", "required"=false, "description"="your skills"},
     *          {"name"="picture", "dataType"="string", "required"=false, "description"="your picture"},
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
     * @View(serializerGroups={"json"})
     * @return Response
     */
    public function postCompleteRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getFirstLogin() === true) {
            $user->setFirstLogin(false);
        }
        //hydrate les données reçues
        $data = $request->request->all();

        $form = $this->get('form.factory')->createNamed(null, new \P4M\UserBundle\Form\UserType(), $user, ['csrf_protection' => false, 'allow_extra_fields' => true]);
        //$form = $this->createNamed(null,new \P4M\UserBundle\Form\UserType(),$user, ['csrf_protection'   => false, 'allow_extra_fields' => true]);
        $form->remove('username')
            ->remove('picture')
            ->remove('plainPassword')
            ->remove('title')
            ->remove('language')
            ->remove('bio')
            ->remove('publicStatus')
            ->remove('skills')
            ->remove('website')
            ->remove('birthDate')->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //return $data;
            //$user->setBirthDate(new DateTime($data['birthDate']));
            $em->persist($user);
            $em->flush();
            $mangoUser = $user->getMangoUserNatural();
            $userUtil = $this->container->get('p4mUser.user_utils');
            if (null == $mangoUser && $userUtil->isMangoUserAvailable($user)) {
                $mango = $this->container->get('p4_m_mango_pay.util');
                $mangoUser = $mango->createUser($user);
                $wallets = $mango->createWallet($mangoUser);
            }
            $em->refresh($user);
            return $user;

        }
        return $form;

//        $this->response['count'] = $request->request->count();
        // sauvegarde les données utilisateurs
//        if ($this->checkData($data, 'FULL_REGISTER')) {
//            $user->setAddress($data['address']);
//            $user->setCity($data['city']);
//            if (!empty($data['country']))
//                $user->setCountry($em->getRepository('P4MUserBundle:Country')->find($data['country']));
//            $user->setBirthDate(new DateTime($data['birth_date']));
//            if (!empty($data['language']))
//                $user->setLanguage($data['language']);
//            $user->setEmail($data['email']);
//            $user->setFirstName($data['first_name']);
//            $user->setLastName($data['last_name']);
//            if (!empty($data['website']))
//                $user->setWebsite($data['website']);
//            if (!empty($data['bio']))
//                $user->setBio($data['bio']);
//
//            if(!empty($data['picture'])){
//
//                $fileRAW = imagecreatefromstring(base64_decode($data['picture']));
//                $name = uniqid();
//                $tmp_path = sys_get_temp_dir() .'/' .$name . '.png';
//                imagepng($fileRAW, $tmp_path);
//                $file =  new UploadedFile($tmp_path, $name, 'image/png',null,null,true);
//
//                $image = new Image();
//                $image->setFile($file);
//                $old_image = $user->getPicture();
//                if($old_image->getId() != 'defaultUser'){
//                    $em->remove($old_image);
//                }
//                $user->setPicture($image);
//                $em->persist($image);
//
//            }
//            try {
//                $em->persist($user);
//                $em->flush();
//                $this->response['status_codes'] = 200;
//                $this->response['message'] = 'Votre compte a bien été mis à jour';
//                $mangoUser = $user->getMangoUserNatural();
//                $userUtil = $this->container->get('p4mUser.user_utils');
//                if (null == $mangoUser && $userUtil->isMangoUserAvailable($user)) {
//                    $mango = $this->container->get('p4_m_mango_pay.util');
//                    $mangoUser = $mango->createUser($user);
//                    $wallets = $mango->createWallet($mangoUser);
//                }
//            } catch (Exception $e) {
//                $this->response['status_codes'] = 'unknown';
//                $this->response['message'] = "Votre compte n'a pas été mis à jour";
//            }
//        }
//        return $this->response;
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
