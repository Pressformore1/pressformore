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

class PostVersion
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
            
        if ($user instanceof User)
        {
            

            foreach ($uow->getScheduledEntityInsertions() as $entity) 
            {
                if ($entity instanceof Post) 
                {
                    $this->createArchive($entity,$em,$user);
                }

                $this->activity($entity,$user,$em);
            }

            foreach ($uow->getScheduledEntityUpdates() as $entity) 
            {

                 if ($entity instanceof Post) 
                {
                    $this->createArchive($entity,$em,$user);
                }
                $this->activity($entity,$user,$em);

            }
            }
            

        

//        foreach ($uow->getScheduledEntityDeletions() as $entity) {
//
//        }
//
//        foreach ($uow->getScheduledCollectionDeletions() as $col) {
//
//        }
//
//        foreach ($uow->getScheduledCollectionUpdates() as $col) {
//
//        }
            

    }
    
    private function activity($entity, User $user,$em)
    {

        if (!$entity instanceof \P4M\NotificationBundle\Entity\Notification &&!$entity instanceof UserActivity )
        {
            if ($entity instanceof \P4M\MangoPayBundle\Entity\WalletFill) 
            {
//                echo 'walletFill';
                $activity = new UserActivity();
                
                if ($entity->getExpired() == false)
                {
//                    echo 'new activity';
                    
                    $activity->setType(UserActivity::TYPE_CUSTOMMER_WALLET_LOADED);
                    
                }
                else
                {
                    $activity->setType(UserActivity::TYPE_CUSTOMMER_WALLET_EXPIRED);
                }
                $userTarget = $entity->getUser();
//                die();
                
            }
            if ($entity instanceof \P4M\CoreBundle\Entity\Pressform) 
            {
                $activity = new UserActivity();
                
                
                if ($entity->getPayed() == true)
                {
                    $activity->setType(UserActivity::TYPE_PRODUCER_WALLET_LOADED);
                    
                }else
                {
                    $activity->setType(UserActivity::TYPE_POST_PRESSFORMED);
                    
                }
                $activity->setPost($entity->getPost());
                $userTarget = $entity->getSender();
                
            }
            if ($entity instanceof Vote) 
            {
                $activity = new UserActivity();
                $activity->setVote($entity);
                if ($entity->getWall() !== null)
                {
                    $activity->setType(UserActivity::TYPE_WALL_VOTED);
                    $activity->setWall($entity->getWall());
//                    $userTarget = $entity->getWall()->getUser();
                    $userTarget = $entity->getUser();
                }
                elseif ($entity->getPost() !== null)
                {
                    $activity->setType(UserActivity::TYPE_POST_VOTED);
                    $activity->setPost($entity->getPost());
//                    $userTarget = $entity->getPost()->getUser();
                    $userTarget = $entity->getUser();
                }
                elseif ($entity->getComment() !== null)
                {
//                    die('comment voted');
                    $activity->setType(UserActivity::TYPE_COMMENT_VOTED);
                    $activity->setComment($entity->getComment());
//                    $userTarget = $entity->getComment()->getUser();
                    $userTarget = $entity->getUser();
                }
            }
            if ($entity instanceof Comment)// @Todo : Comment on wall 
            {
                $activity = new UserActivity();
                $activity->setComment($entity);
                if ($entity->getWall() !== null)
                {
                    $activity->setType(UserActivity::TYPE_WALL_COMMENTED);
                    $activity->setWall($entity->getWall());
                    $userTarget = $entity->getUser();
//                    $userTarget = $entity->getWall()->getUser();
                }
                elseif ($entity->getPost() !== null)
                {
                    $activity->setType(UserActivity::TYPE_POST_COMMENTED);
                    $activity->setPost($entity->getPost());
//                    $userTarget = $entity->getPost()->getUser();
                    $userTarget = $entity->getUser();
                }
//                die('id$'.$userTarget->getId());
            }
            if ($entity instanceof Post) 
            {
                
                if (count($entity->getViews())<1)
                {
                    $activity = new UserActivity();
                    $activity->setPost($entity);
                    $activity->setType(UserActivity::TYPE_POST_ADDED);
                    $userTarget = $entity->getUser();
                    
                    if ($entity->getAuthor() !== null)
                    {
                        $activity2 = new UserActivity();
                        $activity2->setPost($entity);
                        $activity2->setType(UserActivity::TYPE_POST_AUTHOR_IDENTIFIED);
                        $activity2->setUser($entity->getAuthor());
                        //        $uow = $this->em->getUnitOfWork();
                        $em->persist($activity2);
                        $classMetadata = $em->getClassMetadata(get_class($activity2));
                        $em->getUnitOfWork()->computeChangeSet($classMetadata, $activity2);
                }
                    
            }
            }
            if ($entity instanceof Wall) 
            {
//                die('wall');
                
                if (count($entity->getViews())<1)
                {
                    $activity = new UserActivity();
                    $activity->setWall($entity);
                    $activity->setType(UserActivity::TYPE_WALL_CREATED);
                    $userTarget = $entity->getUser();
                }
            }
            if ($entity instanceof UserLink) 
            {
//                die('follow');
                $activity = new UserActivity();
                $activity->setUserLink($entity);
                $activity->setType(UserActivity::TYPE_USER_FOLLOWED);
                $userTarget = $entity->getFollowing();
                
            }
            
            if (isset($activity))
            {
                $activity->setUser($user);
                $em->persist($activity);
                $classMetadata = $em->getClassMetadata(get_class($activity));
                $em->getUnitOfWork()->computeChangeSet($classMetadata, $activity);
                
                $notificator = $this->container->get('event_listener.p4m_trackingbundle_entitylistener_notifier');
                $notificator->notifications($activity,$em,$user);
//                die('user :'.$user->getId(). ' - entityUser'.$userTarget->getId());
//                if ($activity->getType() == UserActivity::TYPE_CUSTOMMER_WALLET_LOADED|| $activity->getType() == UserActivity::TYPE_CUSTOMMER_WALLET_EXPIRED)
//                {
//                    die('notif');
//                    $this->notifications($activity,$em,$user);
//                }
            }
            
            
            
            
            
        }
//        else if ($entity instanceof UserActivity )
//        {
//            $this->notifications($entity,$em);
//        }
        
    }
    
    
    private function createArchive($originalPost,$em,$user)
    {
        $archive = new PostArchive();
        $archive->setUserEdit($user);
        $archive->setDateEdited(new \DateTime());

        $archive->setTitle($originalPost->getTitle());
        $archive->setSlug($originalPost->getSlug());
        $archive->setContent($originalPost->getContent());
        $archive->setLang($originalPost->getLang());
        $archive->setSourceUrl($originalPost->getSourceUrl());
        $archive->setStatus($originalPost->getStatus());
        $archive->setType($originalPost->getType());
        $archive->setTags($originalPost->getTags());
        $archive->setCategories($originalPost->getCategories());
        $archive->setOriginalPost($originalPost);


        $em->persist($archive);
        $classMetadata = $em->getClassMetadata(get_class($archive));
        $em->getUnitOfWork()->computeChangeSet($classMetadata, $archive); // We need to manually tell EM to notice the changes

        $activity = new UserActivity();
        $activity->setPost($originalPost);
        $activity->setType(UserActivity::TYPE_POST_EDITED);
        $activity->setUser($user);
        $em->persist($activity);
        $classMetadata = $em->getClassMetadata(get_class($activity));
        $em->getUnitOfWork()->computeChangeSet($classMetadata, $activity);
        
    }
    
    
            }            
        
            
            
