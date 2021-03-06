<?php

namespace P4M\MyElasticaBundle\Transformer;

use FOS\ElasticaBundle\Doctrine\AbstractElasticaToModelTransformer;
use Doctrine\ORM\Query;

/**
 * Description of PostToElasticaAutoTransformer
 *
 * @author Jona
 */
class PostToElasticaAutoTransformer extends AbstractElasticaToModelTransformer
{
//    const ENTITY_ALIAS = 'post';
    
    public function setIgnoreMissing($value)
    {
        $this->options['ignore_missing'] = $value;
    }
    
    protected function findByIdentifiers(array $identifierValues, $hydrate)
    {
        if (empty($identifierValues)) {
            return array();
        }
        
//        dump($this->options);
//        die();
        
        $hydrationMode = $hydrate ? Query::HYDRATE_OBJECT : Query::HYDRATE_ARRAY;
        
        $qb = $this->registry
            ->getManagerForClass($this->objectClass)
            ->getRepository($this->objectClass)
            ->createQueryBuilder('p')
            ->select('p')
//            ->select('p,c')
//            ->leftJoin('p.categories','c')
//            ->leftJoin('p.views','v')
//            ->leftJoin('p.votes','vo')
//            ->leftJoin('p.user','u')
//            ->leftJoin('p.author','au')
//            ->leftJoin('u.flag','uf')
//            ->leftJoin('p.score','s')
//            ->leftJoin('p.flag','f')
//            ->leftJoin('p.readLater','rl')
//            ->leftJoin('p.pressforms','pr')
//            ->leftJoin('p.comments','co')
//            ->leftJoin('p.type','ty')
//            ->leftJoin('p.tags','t')
//            ->addSelect('rl')
//            ->addSelect('s')
//            ->addSelect('t')
//            ->addSelect('v')
//            ->addSelect('vo')
//            ->addSelect('u')
//            ->addSelect('f')
//            ->addSelect('uf')
//            ->addSelect('pr')
//            ->addSelect('au')
//            ->addSelect('co')
//            ->addSelect('ty')
//            ->setParameter('values', $identifierValues)
                ;
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb->where($qb->expr()->in('p.'.$this->options['identifier'], ':values'))
            ->setParameter('values', $identifierValues);

        return $qb->getQuery()->setHydrationMode($hydrationMode)->execute();
    }
}

