<?php


/**
 * Description of PreUpdate
 *
 * @author Jona
 */
namespace P4M\TrackingBundle\Listener;

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

class Notify
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $eventArgs)
    {
        $user = null;
        $token = $this->container->get('security.context')->getToken();
        if (null !== $token)
        {
            $user = $token->getUser();
        }
        
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
            


        foreach ($uow->getScheduledEntityInsertions() as $entity) 
        {
            if ($entity instanceof UserActivity) 
            {
                $this->notifications($entity,$em,$user);
            }


        }

            

            
        return;
        


            

    }

  
    

    public function notifications(UserActivity $entity,$em,  User $user = null)
    {
        $typeRepo = $em->getRepository('P4MNotificationBundle:NotificationType');
        $type = $typeRepo->findOneByTypeLink($entity->getType());
        
        if ( $type !== null )
        {
//             die('type not null');
            
            
            switch ($entity->getType())
            {
                
                case UserActivity::TYPE_POST_AUTHOR_IDENTIFIED:
                case UserActivity::TYPE_POST_AUTHOR_CHANGED:
                    $userTarget= $entity->getUser();
                    $notification = new Notification();
                    $notification->setType($type);
                    $notification->setActivity($entity);
                    $notification->setUser($userTarget);
                    $em->persist($notification);
                    $classMetadata = $em->getClassMetadata(get_class($notification));
                    $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    break;
                case UserActivity::TYPE_POST_COMMENTED:
                case UserActivity::TYPE_POST_VOTED:
                case UserActivity::TYPE_POST_EDITED:
                    $userTarget= $entity->getPost()->getUser();
                    if ($user !== $userTarget)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($userTarget);
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                    
                    break;
                case UserActivity::TYPE_WALL_VOTED:
                case UserActivity::TYPE_WALL_COMMENTED:
                    $userTarget= $entity->getWall()->getUser();
                    if ($user !== $userTarget)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($entity->getWall()->getUser());
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                break;
                case UserActivity::TYPE_WALL_CREATED:
                case UserActivity::TYPE_WALL_EDITED:
                case UserActivity::TYPE_POST_ADDED:
                    $userRepo = $em->getRepository('P4MUserBundle:User');
                    $followers = $userRepo->findFollowers($entity->getUser());
                    if ($followers !== null)
                    {
                        foreach ($followers as $follower)
                        {
                            if ($user !== $follower)
                            {
                                $notification = new Notification();
                                $notification->setType($type);
                                $notification->setActivity($entity);
                                $notification->setUser($follower);
                                $em->persist($notification);
                                $classMetadata = $em->getClassMetadata(get_class($notification));
                                $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                            }
                            
                        }
                    }
                break;
                case UserActivity::TYPE_POST_ADDED:
                    if ($entity->getPost()->getAuthor() !== null)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($entity->getPost()->getAuthor());
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                break;
                case UserActivity::TYPE_COMMENT_VOTED:
                    $userTarget= $entity->getComment()->getUser();
                    if ($user !== $userTarget)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($entity->getComment()->getUser());
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                break;
                case UserActivity::TYPE_USER_FOLLOWED:
                    $userTarget= $entity->getUserLink()->getFollowing();
                    if ($user !== $userTarget)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($entity->getUserLink()->getFollowing());
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                break;
                case UserActivity::TYPE_POST_PRESSFORMED:
                    $userTarget= $entity->getPost()->getAuthor();
                    if ($user !== $userTarget)
                    {
                        $notification = new Notification();
                        $notification->setType($type);
                        $notification->setActivity($entity);
                        $notification->setUser($userTarget);
                        $em->persist($notification);
                        $classMetadata = $em->getClassMetadata(get_class($notification));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                    }
                break;
                case UserActivity::TYPE_CUSTOMMER_WALLET_LOADED:
                case UserActivity::TYPE_PRODUCER_WALLET_LOADED:
                case UserActivity::TYPE_CUSTOMMER_WALLET_EXPIRED:
                    
                    $notification = new Notification();
                    $notification->setType($type);
                    $notification->setActivity($entity);
                    $notification->setUser($entity->getUser());
                    $em->persist($notification);
                    $classMetadata = $em->getClassMetadata(get_class($notification));
                    $em->getUnitOfWork()->computeChangeSet($classMetadata, $notification);
                break;
            
            }
            
            
        }
        else
        {
//            die('type null');
        }
        
        
        
        
        
        
        
    }
    
    
}


 