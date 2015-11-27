<?php
namespace P4M\UserBundle\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

    public function __construct( HttpUtils $httpUtils, array $options ) {
        parent::__construct( $httpUtils, $options );
    }

    public function onAuthenticationSuccess( Request $request, TokenInterface $token ) {
        
        if($token->getUser()->getFirstLogin()=== true)
        {
            $response = new \Symfony\Component\HttpFoundation\RedirectResponse($this->httpUtils->generateUri($request,'p4_m_backoffice_profile_infos'));
        }
        elseif ($request->cookies->get('login_redirection'))
        {
            $response = new \Symfony\Component\HttpFoundation\RedirectResponse($request->cookies->get('login_redirection'));
            $response->headers->clearCookie('login_redirection');
        }else {
            $response = parent::onAuthenticationSuccess( $request, $token );
        }
        return $response;
    }
}