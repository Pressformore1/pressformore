<?php


namespace  P4M\MyElasticaBundle\Listener;

use FOS\ElasticaBundle\Doctrine\Listener as BaseListener;
use \Doctrine\Common\EventArgs;

class FollowerListener extends BaseListener
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

    public function postUpdate(EventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof \P4M\UserBundle\Entity\UserLink) 
            {
            $this->scheduledForUpdate[] = $entity;
            $this->inicializaUser();
            $this->objectPersisterPost->replaceOne($entity->getFollower());
            
        }
    }
    
    public function postPersist(EventArgs $eventArgs)
    {
        
        $entity = $eventArgs->getEntity();

        
        if ($entity instanceof \P4M\UserBundle\Entity\UserLink ) {

            $this->scheduledForInsertion[] = $entity;
            $this->inicializaUser();
            $this->objectPersisterPost->replaceOne($entity->getFollower());
        }
    }
    
    public function preRemove(EventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
       
        

        if ($entity instanceof \P4M\UserBundle\Entity\UserLink) 
        {
             
            $this->scheduleForDeletion($entity);
            $this->inicializaUser();
            $this->objectPersisterPost->replaceOne($entity->getFollower());
        }
    }
    

}
