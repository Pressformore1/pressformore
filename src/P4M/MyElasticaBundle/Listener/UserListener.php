<?php


namespace  P4M\MyElasticaBundle\Listener;

use Doctrine\Common\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;


class UserListener extends BaseListener implements EventSubscriber
{
   
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }
   
    protected function inicializaUser() {
        $websiteIndex = $this->container->getParameter('elastica_website');
        $this->objectPersisterPost = $this->container->get('fos_elastica.object_persister.'.$websiteIndex.'.user');
        $this->em = $this->container->get('doctrine')->getEntityManager();
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        
        $entity = $eventArgs->getEntity();

        
        if ($entity instanceof \P4M\UserBundle\Entity\User ) {

            $this->scheduledForInsertion[] = $entity;
            $this->inicializaUser();
            $this->objectPersisterPost->replaceOne($entity);
        }
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist'
        );
    }
    
   
    
}