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


class PostRepository extends Repository
{


    public function findTrendyPosts($page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();


//        $query_part = new \Elastica\Query\MatchAll();
        $query_part = new \Elastica\Query\Bool();
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
        );
        $query->setQuery($query_part);
//        $query = $this->customSort($query,['rank'=>'quality']);
        $query = $this->customSort($query, []);


        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);


        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);
    }

    public function findPressablePosts(array $options, $page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();

        $query_part = new \Elastica\Query\Bool();
//        $query_part->addMustNot( 
//            new \Elastica\Query\Term(array('post.author' => array('value' => null)))
//        );
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
        );

        $query = $this->customSort($query, []);
//        if (count($options))
//        {
//            $filters = new \Elastica\Filter\Bool();
//            $filters = $this->searchFilter($filters,$options);
//            
//            $query = $this->customSort($query,$options);
//           
//            
//            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters,'post.dateAddedTimestamp');
//            $query->setQuery($queryfiltered);
//
//        }
//        else
//        {
        $query->setQuery($query_part);
//        }

        $existsFilter = new \Elastica\Filter\Exists('post.author.id');
//        $query->setFilter($existsFilter);
        $query->setPostFilter($existsFilter);

        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);


//        die('t'.count($results));
        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);
    }

    public function findHomePosts(array $options, $page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();

        $query_part = new \Elastica\Query\Bool();
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.showOnHome' => array('value' => true, 'boost' => 5)))
        );
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
        );

        if (count($options)) {
            $filters = new \Elastica\Filter\Bool();
            $filters = $this->searchFilter($filters, $options);

            $query = $this->customSort($query, $options);


            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters, 'post.dateAddedTimestamp');
            $query->setQuery($queryfiltered);

        } else {
            $query->setQuery($query_part);
        }

        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);


//        die('t'.count($results));
        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);
    }


    public function findReadLaterPosts($userDisplayed, array $options, $page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();

        $query_part = new \Elastica\Query\Bool();
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.readLater.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
        );
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
        );
        $query = $this->customSort($query, $options, 'post.readLater.dateTimestamp');
        if (count($options)) {
            $filters = new \Elastica\Filter\Bool();
//            $filters->addMust( 
//                new \Elastica\Query\Term(array('post.readLater.user.username' => array('value' => strtolower($userDisplayed->getUsername()), 'boost' => 5)))
//            );
//            $filters->addMust( 
//                new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
//            );
            $filters = $this->searchFilter($filters, $options);


            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
            $query->setQuery($queryfiltered);

        } else {
            $query->setQuery($query_part);
        }


        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);


//        die('t'.count($results));
        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);
    }


    public function findUserViewedPosts($userDisplayed, array $options, $page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();

        $query_part = new \Elastica\Query\Bool();
        $query_part->addShould(
            new \Elastica\Query\Term(array('post.views.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
        );
        $query_part->addMust(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
        );


        if (count($options)) {

            $filters = new \Elastica\Filter\Bool();
            $filters = $this->searchFilter($filters, $options, 'post.views.dateTimestamp');

            $query = $this->customSort($query, $options, 'post.views.dateTimestamp');


            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
            $query->setQuery($queryfiltered);

//            die(print_r($query->toArray(),true));

            //        $results = $this->findPaginated($query);
            //        $results->setMaxPerPage($nombreParPage);
            //        $results->setCurrentPage($page);
        } else {
            $query = $this->customSort($query, [], 'post.views.dateTimestamp');
            $query->setQuery($query_part);
        }

        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);
//        $results = $this->find($query,999999);


//        die('t'.count($results));

        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);
    }

    public function findUserActionPosts($userDisplayed, array $options, $page = 1, $nombreParPage = 30)
    {

        $query = new \Elastica\Query();

        $query_part = new \Elastica\Query\Bool();
        $query_part->addMustNot(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => true)))
        );
        if (!count($options)) {
            $query_part->addShould(
                new \Elastica\Query\Term(array('post.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('post.author.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('post.comments.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 3)))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('post.votes.user.id' => array('value' => $userDisplayed->getId())))
            );

        } else if (count($options)) {
            $dateFilter = [];
            if (isset($options['activities'])) {
                if (in_array('post_added', $options['activities'])) {
                    $query_part->addShould(
                        new \Elastica\Query\Term(array('post.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
                    );
                    $query_part->addShould(
                        new \Elastica\Query\Term(array('post.author.id' => array('value' => $userDisplayed->getId(), 'boost' => 5)))
                    );
                    $dateFilter[] = 'post.dateAddedTimeStamp';
                }
                if (in_array('post_commented', $options['activities'])) {
                    $query_part->addShould(
                        new \Elastica\Query\Term(array('post.comments.user.id' => array('value' => $userDisplayed->getId(), 'boost' => 3)))
                    );
                    $dateFilter[] = 'post.comments.dateAddedTimeStamp';
                }
                if (in_array('post_voted', $options['activities'])) {
//                    die('votes');
                    $query_part->addShould(
                        new \Elastica\Query\Term(array('post.votes.user.id' => array('value' => $userDisplayed->getId()))
                        ));
                    $dateFilter[] = 'post.votes.dateAddedTimeStamp';
                }
            }


            $filters = new \Elastica\Filter\Bool();

//            $filters->addMust( 
//                new \Elastica\Query\Term(array('post.quarantaine' => array('value' => false)))
//            );

            $filters = $this->searchFilter($filters, $options, $dateFilter);

            /**
             * Sort
             */
            $query = $this->customSort($query, $options);


            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
            $query->setQuery($queryfiltered);


            //        $results = $this->findPaginated($query);
            //        $results->setMaxPerPage($nombreParPage);
            //        $results->setCurrentPage($page);
        } else {
            $query->setQuery($query_part);
        }


        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);
//        $results = $this->find($query,999999);


//        die('t'.count($results));
        $entities = $results;


        return array('entities' => $entities, 'count' => 9999);

    }

    public function findCustom($searchText, $options = array(), $page = 1, $nombreParPage = 30)
    {
        $query = new \Elastica\Query();


        if ($searchText !== null) {
            $query_part = new \Elastica\Query\Bool();
            $query_part->addShould(
                new \Elastica\Query\Term(array('title' => array('value' => $searchText, 'boost' => 5)))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('content' => array('value' => $searchText, 'boost' => 3)))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('user.username' => array('value' => strtolower($searchText))))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('tags.name' => array('value' => strtolower($searchText))))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('categories.name' => array('value' => strtolower($searchText))))
            );
            $query_part->addShould(
                new \Elastica\Query\Term(array('readLater.user.username' => array('value' => strtolower($searchText))))
            );


        } else {
            $query_part = new \Elastica\Query\Bool();
        }
        $query_part->addMustNot(
            new \Elastica\Query\Term(array('post.quarantaine' => array('value' => true)))
        );


        if (count($options)) {
            $filters = new \Elastica\Filter\Bool();


            $filters = $this->searchFilter($filters, $options);

            /**
             * Sort
             */
            $query = $this->customSort($query, $options);


            $queryfiltered = new \Elastica\Query\Filtered($query_part, $filters);
            $query->setQuery($queryfiltered);


            //        $results = $this->findPaginated($query);
            //        $results->setMaxPerPage($nombreParPage);
            //        $results->setCurrentPage($page);
        } else {
            $query->setQuery($query_part);
        }

        $query->setFrom(($page - 1) * $nombreParPage);
        $query->setSize($nombreParPage);
        $results = $this->find($query);


//        die('t'.count($results))AZA;
//        $entities = array_slice($results, ($page-1)*$nombreParPage,$nombreParPage);
        $entities = $results;


        return array('entities' => $entities, 'count' => count($results));
//        return 


    }

    private function customSort($query, $options, $default = 'dateAddedTimeStamp')
    {

        if (isset($options['rank'])) {
            switch ($options['rank']) {
                case 'quality':
                    $query->addSort(array('score.score' => array('order' => 'desc')));
                    break;
                case 'like':
                    $query->addSort(array('score.ratio' => array('order' => 'desc')));
                    break;
                case 'comments':
                    $query->addSort(array('commentsCount' => array('order' => 'desc')));
                    break;
                case 'view':
                    $query->addSort(array('viewsCount' => array('order' => 'desc')));
                    break;
                case 'time':
                    $query->addSort(array('dateAddedTimeStamp' => array('order' => 'desc')));
                    $query->addSort(array('comments.dateAddedTimeStamp' => array('order' => 'desc')));
                    $query->addSort(array('votes.dateAddedTimeStamp' => array('order' => 'desc')));
                    break;
            }
        } else {
//            die('default'.print_r($options,true));
            $query->addSort(array($default => array('order' => 'desc')));
        }

        return $query;

    }


    private function searchFilter($filters, $options, $date = 'dateAddedTimeStamp')
    {
        if (isset($options['language'])) {
            $filters->addMust(
                new \Elastica\Filter\Terms('lang.id', $options['language'])
            );
//                foreach ($options['language'] as $language)
//                {
//                    $filters->addMust(
//                        new \Elastica\Filter\Term(array('lang.id' => $language))
//                    );
//                }
        }

        /*
         * Time
         */
        if (isset($options['time'])) {
            $time = (array)$options['time'];

//                die($this->getTimeFromIndice($time['from'])->format('d-m-Y'));
            if (is_array($date)) {
                foreach ($date as $field) {

                    $filters->addShould(
                        new \Elastica\Filter\Range($field, array(
                            'gte' => $this->getTimeFromIndice($time['from'])->getTimestamp(),
                        ))
                    );

                    $filters->addShould(
                        new \Elastica\Filter\Range($field, array(
                            'lte' => $this->getTimeFromIndice($time['to'])->getTimestamp(),
                        ))
                    );
                }

            } else {
                $filters->addMust(
                    new \Elastica\Filter\Range($date, array(
                        'gte' => $this->getTimeFromIndice($time['from'])->getTimestamp(),
                    ))
                );

                $filters->addMust(
                    new \Elastica\Filter\Range($date, array(
                        'lte' => $this->getTimeFromIndice($time['to'])->getTimestamp(),
                    ))
                );
            }

        }


        /*
         * Type
         */
        if (isset($options['type'])) {
            $filters->addMust(
                new \Elastica\Filter\Terms('type.id', $options['type'])
            );
//                
        }

        /*
         * Categories
         */
        if (isset($options['categories'])) {
//                die(print_r($options['categories']));
//                $nested = new \Elastica\Filter\Nested(); 
////                $nested->setQuery(new \Elastica\Query\Terms('id',array(5))); 
//                $nested->setFilter(new \Elastica\Filter\Terms('id',array(5))); 
//                $nested->setPath('categories'); 
//                
//                $filters->addMust($nested); 


            $filters->addShould(
                new \Elastica\Filter\Terms('categories.id', $options['categories'])
            );
        }
        if (isset($options['excludedCategories']) && count($options['excludedCategories'])) {

            $filters->addMustNot(
                new \Elastica\Filter\Terms('categories.id', $options['excludedCategories'])
            );
        }
        if (isset($options['tags']) && count($options['tags'])) {
            $filters->addShould(
                new \Elastica\Filter\Terms('tags.id', $options['tags'])
            );
        }
        if (isset($options['excludedTags']) && count($options['excludedTags'])) {
            $filters->addMustNot(
                new \Elastica\Filter\Terms('tags.id', $options['excludedTags'])
            );
        }
//            
        if (isset($options['bannedPost']) && count($options['bannedPost'])) {
            $filters->addMustNot(
                new \Elastica\Filter\Terms('id', $options['bannedPost'])
            );
        }
        if (isset($options['afterDate'])) {

            $filters->addMust(
                new \Elastica\Filter\Range('dateAddedTimeStamp', array(
                    'gte' => $options['afterDate'])));
        }

        return $filters;
    }


    private function getTimeFromIndice($indice)
    {
        $dateTime = new \DateTime;
        switch ($indice) {
            case '1' :
                $dateTime->modify('-1 day');
                break;
            case '2' :
                $dateTime->modify('-2 day');
                break;
            case '3' :
                $dateTime->modify('-3 day');
                break;
                break;
            case '4' :
                $dateTime->modify('-1 week');
                break;
                break;
            case '5' :
                $dateTime->modify('-2 week');
                break;
            case '6' :
                $dateTime->modify('-1 month');
                break;
                break;
            case '7' :
                $dateTime->modify('-3 month');
                break;
                break;
            case '8' :
                $dateTime->modify('-6 month');
                break;
                break;
            case '9' :
                $dateTime->modify('-1 year');
                break;
            case '10' :
                $dateTime->modify('-5 year');
                break;
            case '11' :
                $dateTime->modify('-11 year');
                break;

        }

        return $dateTime;
    }
}

