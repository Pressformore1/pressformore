<?php


namespace  P4M\MyElasticaBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;
use \Doctrine\Common\EventArgs;

class ReadLaterListener extends BaseListener implements EventSubscriber
{
   
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }
   
    protected function inicializaPost() {
        $websiteIndex = $this->container->getParameter('elastica_website');
        $this->objectPersisterPost = $this->container->get('fos_elastica.object_persister.'.$websiteIndex.'.post');
        $this->em = $this->container->get('doctrine')->getEntityManager();
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater) 
            {
            $this->scheduledForUpdate[] = $entity;
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
            
        }
    }
    
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        
        $entity = $eventArgs->getEntity();

        
        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater ) {

            $this->scheduledForInsertion[] = $entity;
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
        }
    }
    
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
       
        

        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater) 
        {
             
            $this->scheduleForDeletion[] = $entity;
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
        }
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'preRemove',
            'postUpdate'
        );
    }

    

}