<?php

namespace P4M\CoreBundle\Service;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use P4M\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\HttpKernel;


class MyLocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;
    private $token_storage;
    private $router;
    private $authorizationChecker;

    public function __construct($defaultLocale = 'en', TokenStorage $token_storage, Router $router, AuthorizationChecker $authorizationChecker)
    {
        $this->defaultLocale = $defaultLocale;
        $this->token_storage = $token_storage;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function setLocal(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($this->token_storage->getToken()) {
            $user = $this->token_storage->getToken()->getUser();
            if ($user instanceof User) {
                $locale = $user->getLanguage();
                $request->setLocale($locale);
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getRequestUri();
        $route = $request->get('_route');

        if(preg_match('#^/api#', $uri)){
            return;
        }
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        if($this->token_storage->getToken() && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')){
            $user = $this->token_storage->getToken()->getUser();
            $user_local = $user->getLanguage();
            $locale = $request->getLocale();

            //$request->getSession()->set('_locale', $locale);
            if($user_local !== $locale){
                $request->setLocale($user_local);
                $url = $this->router->generate($route, [ '_locale' => $user_local ], true);
                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 0)),
        );
    }
}