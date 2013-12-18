<?php

namespace Ibw\JobeetBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * JobRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class JobRepository extends EntityRepository
{
	function getActiveJobs($category_id = null, $max = null, $offset = null){

		$qb = $this->createQueryBuilder('j')
		           ->where('j.expires_at > :date')
		           ->setParameter('date', date('Y-m-d H:i:s', time()))
		           ->andWhere('j.is_activated = :activated')
                   ->setParameter('activated', 1)
		           ->orderBy('j.expires_at', 'DESC');

        if($max){
        	$qb->setMaxResults($max);
        }
        if($offset)
	    {
	        $qb->setFirstResult($offset);
	    }
		if($category_id){
			$qb->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
		}

        $query = $qb->getQuery();

     return $query->getResult();
	} 

	function getActiveJob($id) {
        
		$query = $this->createQueryBuilder('j')
            ->where('j.id = :id')
            ->setParameter('id', $id)
            ->andWhere('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1)
            ->setMaxResults(1)
            ->getQuery();
 
        try {
            $job = $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $job = null;
        }
 
        return $job;
	}

	function countActiveJobs($category_id = null) {

		$db = $this->createQueryBuilder('j')
		              ->select('count(j.id)')
		              ->Where('j.expires_at > :date')
		              ->setParameter('date', date('Y:m:d H:i:s', time()))
		              ->andWhere('j.is_activated = :activated')
                      ->setParameter('activated', 1);
		              
        if($category_id) {
               $db->andwhere('j.category = :category_id')
                  ->setParameter('category_id', $category_id);
        }

        $query = $db->getQuery();
		
		return $query->getSingleScalarResult();
	}

    public function getLatestPost($category_id = null)
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1)
            ->orderBy('j.expires_at', 'DESC')
            ->setMaxResults(1);
 
        if($category_id) {
            $query->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
        }
 
        try{
            $job = $query->getQuery()->getSingleResult();
        } catch(\Doctrine\Orm\NoResultException $e){
            $job = null;
        }
 
        return $job;    
    }
}
