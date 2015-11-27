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

class PostAuthorChangeCheck
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    

    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs)
    {

        
        $entity = $eventArgs->getEntity();

        if ($entity instanceof Post) 
        {
            if ($eventArgs->hasChangedField('author')) 
            {
                $this->createActivity($entity,$eventArgs);
            }

        }


        return;    
           

    }
    
    private function createActivity($entity,$eventArgs)
    {
        $activity = new UserActivity();
        $activity->setPost($entity);
        $activity->setType(UserActivity::TYPE_POST_AUTHOR_IDENTIFIED);
        $activity->setUser($entity->getAuthor());
        
        $em = $this->container->get('doctrine')->getManager();
//        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        
//        die('persisting');
        $em->persist($activity);
//        $classMetadata = $em->getClassMetadata(get_class($activity));
//        $em->getUnitOfWork()->computeChangeSet($classMetadata, $activity);
    }
    
    

}


 