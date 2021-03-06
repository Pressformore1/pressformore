<?php

namespace P4M\BackofficeBundle\Entity;

use Doctrine\ORM\EntityRepository;
use P4M\CoreBundle\Entity\Post;
use P4M\UserBundle\Entity\User;
/**
 * BannedPostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannedPostRepository extends EntityRepository
{
    
    public function findIdsByUser (User $user)
    {
         $em= $this->getEntityManager();
        $qb = $this->createQueryBuilder('b');
        $qb->join('b.post','p');
        
        $qb ->where('b.user = :userId')
            ->setParameter('userId',$user->getId())
            ;
        
        $results = $qb->getQuery()->getResult();
        
        
        $listId = array();
        foreach ($results as $result)
        {
            $listId[]=$result->getPost()->getId();
        }
        
        return $listId;
    }
}
