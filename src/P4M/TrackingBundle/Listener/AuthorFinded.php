<?php


/**
 * Description of PreUpdate
 *
 * @author Jona
 */
namespace P4M\TrackingBundle\Listener;


use P4M\CoreBundle\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerInterface;
use P4M\TrackingBundle\Entity\UserActivity;

class AuthorFinded
{
    
    protected $container;
    protected $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
    }
    
    

    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getEntityManager();
        $uow = $this->em->getUnitOfWork();
        
        
        foreach ($uow->getScheduledEntityInsertions() as $entity) 
        {
            if ($entity instanceof Post && $entity->getAuthor() !== null)
            {
                //Ne fonctionne pas, je ne sais pas pourquoi, j'en ai marre j'ai tapé ça dans PostVersion
//                $this->createActivity($entity);
            }

            
        }
        
       


        return;    
           

    }
    
    private function createActivity($entity)
    {
        $activity = new UserActivity();
        $activity->setPost($entity);
        $activity->setType(UserActivity::TYPE_POST_AUTHOR_IDENTIFIED);
        $activity->setUser($entity->getAuthor());
        //        $uow = $this->em->getUnitOfWork();
        $this->em->persist($activity);
        $classMetadata = $this->em->getClassMetadata(get_class($activity));
        $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $activity);
//        die('persisting'.$entity->getAuthor()->getId());
    }
    
    

}


 