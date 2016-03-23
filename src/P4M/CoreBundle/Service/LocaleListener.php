<?php

namespace P4M\CoreBundle\Service;

use P4M\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;
    private $token_storage;

    public function __construct($defaultLocale = 'en', TokenStorageInterface $token_storage)
    {
        $this->defaultLocale = $defaultLocale;
        $this->token_storage = $token_storage;
    }
    public function setLocal(FilterControllerEvent $event){
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        if($this->token_storage->getToken()){
            $user = $this->token_storage->getToken()->getUser();
            if($user instanceof User){
                $locale = $user->getLanguage();
                $request->setLocale($locale);
            }
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}