<?php


namespace  P4M\MyElasticaBundle\Listener;

use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;
use \Doctrine\Common\EventArgs;

class ReadLaterListener extends BaseListener
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

    public function postUpdate(EventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater) 
            {
            $this->scheduledForUpdate[] = $entity;
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
            
        }
    }
    
    public function postPersist(EventArgs $eventArgs)
    {
        
        $entity = $eventArgs->getEntity();

        
        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater ) {

            $this->scheduledForInsertion[] = $entity;
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
        }
    }
    
    public function preRemove(EventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
       
        

        if ($entity instanceof \P4M\BackofficeBundle\Entity\ReadPostLater) 
        {
             
            $this->scheduleForDeletion($entity);
            $this->inicializaPost();
            $this->objectPersisterPost->replaceOne($entity->getPost());
        }
    }
    

}