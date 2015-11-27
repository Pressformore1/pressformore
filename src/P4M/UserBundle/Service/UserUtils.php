<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserUtils
 *
 * @author Jona
 */

namespace P4M\UserBundle\Service;

use P4M\UserBundle\Entity\User;


class UserUtils 
{
    
    private $userLinkRepo;
    private $container;
    private $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em,$container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->userLinkRepo = $this->em->getRepository('P4MUserBundle:UserLink');
    }
    
    
    public function isFollowedBy(User $followed,User $follower)
    {
        if (null === $this->getLink($followed, $follower))
        {
            return false;
        }
        return true;
    }
    
    public function getLink(User $followed,User $follower)
    {
        $existLink = $this->userLinkRepo->findOneBy(array('follower' => $follower,'following'=>$followed));
        return $existLink;
    }
    
    public function createLink(User $followed,User $follower)
    {
        $link = new \P4M\UserBundle\Entity\UserLink();
        $link->setFollower($follower);
        $link->setFollowing($followed);

        $this->em->persist($link);
        $this->em->flush();
    }
    
    public function deleteLink(User $followed,User $follower)
    {
        $link = $this->getLink($followed, $follower);
        $this->em->remove($link);
        $this->em->flush();
    }
    
    
    public function getFullProfileResults($userDisplayed,$page,$nombreParPage)
    {
        $finder = $this->container->get('fos_elastica.finder.prodprofile');
        
        
        $query = new \Elastica\Query();
        
        $query_part = new \Elastica\Query\Bool();
        $query_part->addMustNot( 
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => true)))
        );
        $query_part->addMustNot( 
            new \Elastica\Query\Term(array('vote.post.quarantaine' => array('value' => true)))
        );
        $query_part->addMustNot( 
            new \Elastica\Query\Term(array('comment.post.quarantaine' => array('value' => true)))
        );
        
        $query_part->addShould(
        new \Elastica\Query\Term(array('post.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
        );
        $query_part->addShould(
        new \Elastica\Query\Term(array('post.author.id' => array('value' => $userDisplayed->getId()), 'boost' => 5))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('comment.user.id' => array('value' => $userDisplayed->getId())))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('vote.user.id' => array('value' =>  $userDisplayed->getId()))
        ));
        $query_part->addShould(
            new \Elastica\Query\Term(array('wall.user.id' => array('value' => $userDisplayed->getId())))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('userlink.follower.id' => array('value' => $userDisplayed->getId())))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('userlink.following.id' => array('value' => $userDisplayed->getId())))
        );
        

        if (isset($options['rank']))
        {
            switch ($options['rank'])
            {
                case 'strews':
                    $query->addSort(array('wallsCount' => array('order' => 'desc')));
                    break;
                case 'contentAdd':
                    $query->addSort(array('postsCount' => array('order' => 'desc')));
                    break;

            }
        }
        else
        {
            
            $query->addSort(array('dateAddedTimeStamp' => array('order' => 'desc')));

        }
        $query->setQuery($query_part);
        
        
        
        
        $results = $finder->find($query,999999);
        
        
        
        
//        die('test');
        
//        die('t'.count($results));
        $entities = array_slice($results, ($page-1)*$nombreParPage,$nombreParPage);
        
        

        return array('entities'=>$entities,'count'=>count($results));
        
//        return $finder->find($query,999999);
        
        
        
        
        $fieldQuery = new \Elastica_Query_Text();
        $fieldQuery->setFieldQuery('post.user.username', $userDisplayed->getUsername());
//        $fieldQuery->setFieldParam('title', 'analyzer', 'my_analyzer');
        $boolQuery->addShould($fieldQuery);

        $tagsQuery = new \Elastica_Query_Terms();
        $tagsQuery->setTerms('tags', array('tag1', 'tag2'));
        $boolQuery->addShould($tagsQuery);

        $categoryQuery = new \Elastica_Query_Terms();
        $categoryQuery->setTerms('categoryIds', array('1', '2', '3'));
        $boolQuery->addMust($categoryQuery);

        $data = $finder->find($boolQuery);
        
        $userLinkRepo = $this->em->getRepository('P4MUserBundle:UserLink');
        $activityRepo = $this->em->getRepository('P4MTrackingBundle:UserActivity');
        
        $posts = $activityRepo->findPostActivityByUser($userDisplayed,[],0,0);
        $walls = $activityRepo->findWallActivityByUser($userDisplayed);
      
        $followingLinks = $userLinkRepo->findByFollower($userDisplayed);
        $followedLinks = $userLinkRepo->findByFollowing($userDisplayed);
        
        
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        $nombrePages = ceil((count($walls)+count($posts)+count($followedLinks)+count($followingLinks))/$nombrePostsParPage);
        
        $feed = $this->processResults($walls,$posts,$followedLinks,$followingLinks,$page);
        
        
        
        return array('feed'=>$feed,'posts'=>$posts,'walls'=>$walls,'nombrePages'=>$nombrePages);
    }
    
    public function getFullProfileResultsDoctrine($userDisplayed,$page)
    {
        
        $activityRepo = $this->em->getRepository('P4MTrackingBundle:UserActivity');
        $activity = $activityRepo->findProfileActivity($userDisplayed,$page);
                
        
        
        
        $userLinkRepo = $this->em->getRepository('P4MUserBundle:UserLink');
        
        
        $posts = $activityRepo->findPostActivityByUser($userDisplayed,[],0,0);
        $walls = $activityRepo->findWallActivityByUser($userDisplayed);
      
        $followingLinks = $userLinkRepo->findByFollower($userDisplayed);
        $followedLinks = $userLinkRepo->findByFollowing($userDisplayed);
        
        
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        $nombrePages = ceil((count($walls)+count($posts)+count($followedLinks)+count($followingLinks))/$nombrePostsParPage);
        
        $feed = $this->processResults($walls,$posts,$followedLinks,$followingLinks,$page);
        
        
        
        return array('feed'=>$feed,'posts'=>$posts,'walls'=>$walls,'nombrePages'=>$nombrePages);
    }
    
    public function getProfilePosts($userDisplayed,$page,$postData =array())
    {
        
        $finder = $this->container->get('fos_elastica.finder.website');
        
        $query = new \Elastica\Query();
        
        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
        new \Elastica\Query\Term(array('post.user.username' => array('value' => $userDisplayed->getUsername(), 'boost' => 5)))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('post.comments.user.username' => array('value' => $userDisplayed->getUsername(), 'boost' => 3)))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('post.votes.user.username' => array('value' =>  $userDisplayed->getUsername())))
        );
        

         
        $query->setQuery($query_part);
        
        return $finder->find($query,999999);
        
        
       
        
        return array('feed'=>$feed,'posts'=>$posts,'walls'=>$walls,'nombrePages'=>$nombrePages);
    }
    public function getProfileWalls($userDisplayed,$page,$postData =array())
    {
        $activityRepo = $this->em->getRepository('P4MTrackingBundle:UserActivity');
        
        $posts = $activityRepo->findWallActivityByUser($userDisplayed,$postData);
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        $nombrePages = ceil(count($posts)/$nombrePostsParPage);
        
        $results = $this->processResults([],$posts,[],[],$page);
        
        return $results;
    }
    
    private function processResults($walls,$posts,$followedLinks,$followingLinks,$page)
    {
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        
        
        $toReturn = array();
        $latest_wall = 0;
        $latest_post = 0;
        $latest_following = 0;
        $latest_followed = 0;

        
        for ($i = 0; $i < ($nombrePostsParPage*$page); $i++)
        {
            $toTest = array();
            if (isset($walls[$latest_wall]))
            {
                $toTest['wall'] = $walls[$latest_wall]->getDate()->getTimestamp();
            }
            if (isset($posts[$latest_post]))
            {
                $toTest['post'] = $posts[$latest_post]->getDate()->getTimestamp();
            }
            if (isset($followedLinks[$latest_followed]))
            {
                $toTest['followed'] = $followedLinks[$latest_followed]->getDate()->getTimestamp();
            }
            
            if (isset($followingLinks[$latest_following]))
            {
                $toTest['following'] = $followingLinks[$latest_following]->getDate()->getTimestamp();
            }
            

            arsort($toTest);
            
            $keys = array();
            
            foreach ($toTest as $key => $date)
            {
                $keys[] = $key;
            }
                        
            if (count($keys))
            {
                switch ($keys[0])
                {
                    case 'post':
                        $toReturn[] = $posts[$latest_post];
                        $latest_post ++;
                    break;
                    case 'followed':
    //                    echo 'vote';
                        $toReturn[] = $followedLinks[$latest_followed];
                        $latest_followed ++;
                    break;
                    case 'following':
                        $toReturn[] = $followingLinks[$latest_following];
                        $latest_following ++;
                    break;
                    case 'wall':
                        $toReturn[] = $walls[$latest_wall];
                        $latest_wall ++;
                    break;
                }

                if ($i<($nombrePostsParPage*($page-1)))
                {
                    $toReturn = [];
                }
            }
            
//           die(print_r($toReturn)); 
            
        }
        
        return $toReturn;
    }
    
    public function isMangoUserAvailable(User $user)
    {
        if ($user->getEmail() && $user->getFirstName() && $user->getLastName() && $user->getBirthDate() && $user->getAddress() && $user->getCountry())
        {
            return true;
        }
        return false;
    }
}
