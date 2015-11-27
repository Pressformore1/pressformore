<?php


/**
 * Description of PreUpdate
 *
 * @author Jona
 */
namespace P4M\UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\Wall;
use P4M\CoreBundle\Entity\Vote;
use P4M\CoreBundle\Entity\Comment;
use P4M\UserBundle\Entity\User;
use P4M\UserBundle\Entity\UserLink;
use P4M\TrackingBundle\Entity\PostArchive;
use Symfony\Component\DependencyInjection\ContainerInterface;

use P4M\NotificationBundle\Entity\Notification;
use P4M\NotificationBundle\Event\NotificationEvents;
use P4M\NotificationBundle\Event\NotificationLikeEvent;
use P4M\NotificationBundle\Event\NotificationCommentEvent;
use P4M\TrackingBundle\Entity\UserActivity;

class NewUser
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    

    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $eventArgs)
    {
        $token = $this->container->get('security.context')->getToken();
        
        if (null === $token)
        {
            return;
        }
        
        $user = $token->getUser();
        if (null === $user)
        {
            return;
        }
        
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
            
        foreach ($uow->getScheduledEntityInsertions() as $entity) 
        {
            if ($entity instanceof User) 
            {
                $entity = $this->createDefaultWall($entity,$em);
            }

        }
   
            

    }
    
    private function createDefaultWall(User $user,$em)
    {
        
        $wall = new Wall();
        
        $catNames = ['Art','Science','Politics','Internet'];
        $catRepo = $em->getRepository('P4MCoreBundle:Category');
        
        foreach ($catNames as $catName)
        {
            $cat = $catRepo->findOneByName($catName);
            if (null !== $cat)
            {
                $wall->addIncludedCategorie($cat);
            }            
        }
        
        if (count($wall->getIncludedCategories()))
        {
//            die('default wall');
            $wall->setName('Example Strew');
            $wall->setDescription('This is an example. You may want to edit and to customize this strew.');
            $wall->setUser($user);
            $wall->setPicture(null);
            $wall->setSlug(strtolower(str_replace(' ','-',$user->getUsername())).'/example-strew');
            $em->persist($wall);
            $classMetadata = $em->getClassMetadata(get_class($wall));
            $em->getUnitOfWork()->computeChangeSet($classMetadata, $wall);
            
            
        }
        
        
        
        
        
        
    }
}


 