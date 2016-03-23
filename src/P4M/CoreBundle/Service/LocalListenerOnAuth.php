<?php

namespace P4M\CoreBundle\Service;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use P4M\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;



class LocalListenerOnAuth implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $route = $request->get('_route');
        $user = $token->getUser();
        $locale = $user->getLanguage();
        $request->setLocale($locale);
        $route = $this->router->generate('p4m_core_wall', [ '_locale' => $locale ]);

        $response = new RedirectResponse($route);
        return $response;


    }
}