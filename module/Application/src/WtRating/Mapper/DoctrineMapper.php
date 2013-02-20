<?php

namespace WtRating\Mapper;

use WtRating\Entity\Rating;
use WtRating\Entity\RatingSet;
use Doctrine\ORM\EntityManager;

class DoctrineMapper implements MapperInterface
{
    private $em;
    private $rep;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->rep = $em->getRepository('WtRating\Entity\Rating');
        
    }

    /**
     * Gets the set of ratings for the given type id.
     *
     * @param string $typeId The type identifier to get the set of ratings for.
     * @return RatingSet
     */
    public function getRatingSet($typeId)
    {
        $qb = $this->rep->createQueryBuilder('e');
        $result = $qb   ->select($qb->expr()->avg('e.rating'))
                        ->addSelect($qb->expr()->count('e.rating'))
                        ->addSelect($qb->expr()->max('e.rating'))
                        ->addSelect($qb->expr()->min('e.rating'))
                        ->where('e.typeId = :typeId')
                        ->setParameter('typeId',$typeId)
                        ->getQuery()->getResult();
        
        $row = $result[0];
        return new RatingSet($typeId, $row[2], $row[1], $row[3], $row[4]);
    }
    
    /**
     * Gets the set of ratings for the given type id.
     *
     * @param string $userId 
     * @param string $typeId The type identifier to get the set of ratings for.
     * @return the rating of false if not rated
     */
    public function hasRated($userId,$typeId)
    {
        $qb = $this->rep->createQueryBuilder('e');
        $result = $qb   ->select('e.rating')
                        ->where('e.typeId = :typeId')
                        ->setParameter('typeId',$typeId)
                        ->andWhere('e.userId = :userId')
                        ->setParameter('userId',$userId)
                        ->getQuery()->getArrayResult();
        
        if (!$result){
            return false;
        }
        
        $rating = 0;
        foreach ($result as $value){
           $rating += (int)$value['rating'];
        };
        
        return $rating;
        
    }

    /**
     * Saves the given rating to the storage system.
     *
     * @param Rating $rating The rating to persist.
     */
    public function persist(Rating $rating)
    {
        if ($rating->getId()) {
            $this->em->flush();
        } else {
            $this->em->persist($rating);
            $this->em->flush();
        }
    }
}
