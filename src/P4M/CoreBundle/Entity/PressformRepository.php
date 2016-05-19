<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PressformRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PressformRepository extends EntityRepository
{
    public function findByRecipient($user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.post', 'pp','WITH','pp.author=:user')
            ->setParameter('user', $user);
        
        $qb->orderBy('p.date', 'DESC');
        return $qb->getQuery()->getResult();
    }
    
    
    public function findLastMonthNumberByUser($user)
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(p.id)');
        $qb->from('P4MCoreBundle:PressForm','p');
        $qb ->where('p.sender = :user')
            ->setParameter('user', $user)
            ->andWhere('p.date>:lastMonth')
            ->setParameter('lastMonth', $lastMonth );
        
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function findLastMonthNumber()
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(p.id)');
        $qb->from('P4MCoreBundle:PressForm','p');
        $qb ->where('p.date>:lastMonth')
            ->setParameter('lastMonth', $lastMonth );
        
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function findLastMonthPressformersNumber()
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(DISTINCT p.sender)');
        $qb->from('P4MCoreBundle:PressForm','p');
        $qb ->where('p.date>:lastMonth')
            ->setParameter('lastMonth', $lastMonth );
        
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    public function findLastMonthPressedContentNumber()
    {
        $lastMonth = new \DateTime();
        $lastMonth->modify('-1 month');
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb ->select('count(DISTINCT p.post)');
        $qb ->from('P4MCoreBundle:PressForm','p');
        $qb ->where('p.date>:lastMonth')
            ->setParameter('lastMonth', $lastMonth );
        return $qb->getQuery()->getSingleScalarResult();
    }
    public function findDonatorForAnAuthor($author){
        $qb = $this->createQueryBuilder('PF')
            ->join('PF.sender', 'S')
            ->leftJoin('PF.post', 'P')
            ->leftJoin('P.author', 'A')
            ->leftJoin('P.tempAuthor', 'T')
            ->leftJoin('S.picture', 'SP')
            ->addSelect("CONCAT( CONCAT(SP.id, '.' ), SP.name) as sender_picture")
            ->addSelect('S.username as sender')
            ->addSelect('P.slug')
            ->addSelect('A.username as author')
            ->addSelect('T.twitter')
            ->addSelect('T.email')
            ->where('A.username = :author')
            ->orWhere('T.twitter = :author')
            ->orWhere('T.email = :author')
            ->setParameter('author', $author)
            ->getQuery()->getArrayResult();
        return $qb;
    }
    public function findDonatorForAPost($slug){
        $qb = $this->createQueryBuilder('PF')
            ->join('PF.sender', 'S')
            ->leftJoin('PF.post', 'P')
            ->leftJoin('P.author', 'A')
            ->leftJoin('P.tempAuthor', 'T')
            ->leftJoin('S.picture', 'SP')
            ->addSelect("CONCAT( CONCAT(SP.id, '.' ), SP.name) as sender_picture")
            ->addSelect('S.username as sender')
            ->addSelect('P.slug')
            ->addSelect('A.username as author')
            ->addSelect('T.twitter')
            ->addSelect('T.email')
            ->where('P.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()->getArrayResult();
        return $qb;
    }
    public function findDonationByUser($user){
        $qb = $this->createQueryBuilder('PF')
            ->join('PF.sender', 'S')
            ->leftJoin('PF.post', 'P')
            ->leftJoin('P.author', 'A')
            ->leftJoin('P.tempAuthor', 'T')
            ->leftJoin('S.picture', 'SP')
            ->addSelect("CONCAT( CONCAT(SP.id, '.' ), SP.name) as sender_picture")
            ->addSelect('S.username as sender')
            ->addSelect('P.slug')
            ->addSelect('A.username as author')
            ->addSelect('T.twitter')
            ->addSelect('T.email')
            ->where('PF.sender = :user')
            ->setParameter('user', $user)
            ->getQuery()->getArrayResult();
        return $qb;
    }
    public function findDonationBySourceURL($url){
        $qb = $this->createQueryBuilder('PF')
            ->join('PF.sender', 'S')
            ->leftJoin('PF.post', 'P')
            ->leftJoin('P.author', 'A')
            ->leftJoin('P.tempAuthor', 'T')
            ->leftJoin('S.picture', 'SP')
            ->addSelect("CONCAT( CONCAT(SP.id, '.' ), SP.name) as sender_picture")
            ->addSelect('S.username as sender')
            ->addSelect('P.slug')
            ->addSelect('A.username as author')
            ->addSelect('T.twitter')
            ->addSelect('T.email')
            ->where('P.sourceUrl = :url')
            ->setParameter('user', $url)
            ->getQuery()->getArrayResult();
        return $qb;
    }

}
