<?php

namespace P4M\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    
    public function findWallMembers($wallId)
    {
        
    }
    
    public function findCommunity(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        
        $qb ->join('u.followers','flwr','with','flwg.follower=:user')
            ->join('u.following','flwg','with','flwg.following=:user')
//            ->where('flwr.following=:user')
//            ->orWhere('flwg.follower=:user')
            ->setParameter('user', $user);
        
        return $qb->getQuery()->getResult();
                
    }
    
    public function findFollowers(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        
        $qb ->join('u.following','flwg','with','flwg.following=:user')
//            ->where('flwg.follower=:user')
            ->setParameter('user', $user);
        
        return $qb->getQuery()->getResult();
    }
    
    public function findFollowing(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        
        $qb ->leftJoin('u.followers','flwr','with','flwg.follower=:user')
//            ->where('flwr.following=:user')
            ->setParameter('user', $user);
        
        return $qb->getQuery()->getResult();
    }
    
    public function findByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');
        
        return $qb->getQuery()->getResult();
                
    }
    
    public function findActiveUserNumber()
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(u.id)');
        $qb->from('P4MUserBundle:User','u');
        $qb ->where('u.enabled=1')
            ->andWhere('u.lastLogin>:lastMonth')
            ->setParameter('lastMonth', $lastMonth);
        
        
        
        return $qb->getQuery()->getSingleScalarResult();
        
    }
    public function findLastMonthRegistration()
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        
        $qb = $this->createQueryBuilder('u');
        $qb ->where('u.dateCreated>:lastMonth')
            ->setParameter('lastMonth', $lastMonth);
        
        
        
        return $qb->getQuery()->getResult();
        
}
    
    public function findGoodGuys()
    {
        $qb = $this->createQueryBuilder('u');
        $qb ->join('u.mangoUserNatural','m')
              ->addSelect('m')
                ;
        
        return $qb->getQuery()->getResult();
    }
}
