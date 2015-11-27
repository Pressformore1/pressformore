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


namespace P4M\CoreBundle\SearchRepository;

use FOS\ElasticaBundle\Repository;


class WallRepository extends Repository
{
 
    
    public function findByUser($userDisplayed,$options = array(),$page=1,$nombreParPage = 30)
    {
        $query = new \Elastica\Query();
        
        $query_part = new \Elastica\Query\Bool();
        
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.id' => array('value' => $userDisplayed->getId())))
        );
         $filters = new \Elastica\Filter\Bool();
        if (count($options))
        {
//           
            $filters = $this->wallFilters($filters, $options);
           
            $query=$this->wallSort($query,$options);
        }
        
        $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
        $query->setQuery($queryfiltered);
        

        return $this->find($query,9999);
        
        
    }
    
    public function findCustom($searchText,$options = array(),$page=1,$nombreParPage=30)
    {
        $query = new \Elastica\Query();
        
        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
            new \Elastica\Query\Term(array('wall.name' => array('value' => strtolower($searchText), 'boost' => 5)))
        );
        $query_part->addShould(
            new \Elastica\Query\Term(array('user.username' => array('value' => strtolower($searchText))))
        );
        
        if (count($options))
        {
            $filters = new \Elastica\Filter\Bool();
            $filters = $this->wallFilters($filters, $options);
           
            $query=$this->wallSort($query,$options);
            
            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
            $query->setQuery($queryfiltered);
        }
        else
        {
            $query->setQuery($query_part);
        }
        
        $results = $this->find($query,999999);
        
        
        $entities = array_slice($results, ($page-1)*$nombreParPage,$nombreParPage);
        
        

        return array('entities'=>$entities,'count'=>count($results));
        
        
        
        

//        return $this->find($query);
        
        
    }
    
    private function wallSort($query,$options)
    {
        
            /**
             * Sort
             */
            if (isset($options['rank']))
            {
                switch ($options['rank'])
                {
                    case 'followers':
                        $query->addSort(array('followersCount' => array('order' => 'desc')));
                        break;
                    case 'like':
                        $query->addSort(array('votesRatio' => array('order' => 'desc')));
                        break;
                    case 'comments':
                        $query->addSort(array('commentsCount' => array('order' => 'desc')));
                        break;
                    case 'view':
                        $query->addSort(array('viewsCount' => array('order' => 'desc')));
                        break;
                }
            }
            else
            {
                $query->addSort(array('votesRatio' => array('order' => 'desc')));
            }
            
            return $query;
            
    }
    
    private function wallFilters($filters,$options)
    {
         /*
             * Langues
             */
            if (isset($options['language']))
            {
//                $filters->addMust(
//                    new \Elastica\Filter\Terms('lang.id', $options['language'])
//                );
//                foreach ($options['language'] as $language)
//                {
//                    $filters->addMust(
//                        new \Elastica\Filter\Term(array('lang.id' => $language))
//                    );
//                }
            }
            
            
            
            
            
            /*
             * Categories
             */
            if (isset($options['categories']))
            {
                foreach ($options['categories'] as $cat)
                {
                    $filters->addShould(
                        new \Elastica\Filter\Term(array('includedCategories.id' => $cat))
                    );
                }
                
//                $nested = new \Elastica\Filter\Nested(); 
//                $nested->setFilter(new \Elastica\Filter\Ids(null,$options['categories'])); 
//                $nested->setPath('includedCategories'); 
//                $filters->addShould($nested); 
                
              
            }
            
            return $filters;
    }


}
