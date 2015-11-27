<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostRepository
 *
 * @author Jona
 */


namespace P4M\UserBundle\SearchRepository;

use FOS\ElasticaBundle\Repository;


class UserRepository extends Repository
{
    
    
    public function findCommunity($userDisplayed,$options = array())
    {
        $query = new \Elastica\Query();
         
        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.followers.follower.id' => array('value' => $userDisplayed->getId())))
            );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.following.following.id' => array('value' => $userDisplayed->getId())))
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
            $query->addSort(array('wallsCount' => array('order' => 'desc')));
        }
        
        $query->setQuery($query_part);
        

        return $this->find($query,9999);
        
        
        
        
    }
    public function findFollowers($userDisplayed,$options = array())
    {
        $query = new \Elastica\Query();
         
        $query_part = new \Elastica\Query\Bool();
        
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.following.following.id' => array('value' => $userDisplayed->getId())))
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
            $query->addSort(array('wallsCount' => array('order' => 'desc')));
        }
        
        $query->setQuery($query_part);
        

        return $this->find($query,9999);
        
        
        
        
    }
    public function findFollowing($userDisplayed,$options = array())
    {
        $query = new \Elastica\Query();
         
        $query_part = new \Elastica\Query\Bool();
        
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.followers.follower.id' => array('value' => $userDisplayed->getId())))
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
            $query->addSort(array('wallsCount' => array('order' => 'desc')));
        }
        
        $query->setQuery($query_part);
        

        return $this->find($query,9999);
        
        
        
        
    }
    
    public function findCustom($searchText,$options = array(),$page=1,$nombreParPage=30)
    {
        $query = new \Elastica\Query();
        
        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
            new \Elastica\Query\Term(['firstName' => array('value' => strtolower($searchText),'boost'=>5)])
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('lastName' => array('value' => strtolower($searchText),'boost'=>3)))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('username' => array('value' => strtolower($searchText),'boost'=>3)))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.followers.follower.username' => array('value' => strtolower($searchText))))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.followers.follower.firstName' => array('value' => strtolower($searchText))))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.followers.follower.lastName' => array('value' => strtolower($searchText))))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.following.following.username' => array('value' => strtolower($searchText))))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.following.following.firstName' => array('value' => strtolower($searchText))))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.following.following.lastName' => array('value' => strtolower($searchText))))
        );
        
        
        if (count($options))
        {
                      
            $query=$this->userSort($query,$options);
            
        }
       
            
            $query->setQuery($query_part);
       
        
        $results = $this->find($query,999999);
        
        
        $entities = array_slice($results, ($page-1)*$nombreParPage,$nombreParPage);
        $entities = $results;
        
//        die('t'.count($results));

        return array('entities'=>$entities,'count'=>count($results));
        
        
        
        

//        return $this->find($query);
        
        
    }
    
    private function userSort($query,$options)
    {
         /**
             * Sort
             */
            if (isset($options['rank']))
            {
                switch ($options['rank'])
                {
                    case 'strews':
                        $query->addSort(array('wallsCount' => array('order' => 'desc')));
                        break;
                    case 'add':
                        $query->addSort(array('postsCount' => array('order' => 'desc')));
                        break;
//                    case 'comments':
//                        $query->addSort(array('commentsCount' => array('order' => 'desc')));
//                        break;
//                    case 'view':
//                        $query->addSort(array('viewsCount' => array('order' => 'desc')));
//                        break;
                }
            }
            else
            {
//                $query->addSort(array('votesRatio' => array('order' => 'desc')));
            }
            
            return $query;
    }
   
   
}
