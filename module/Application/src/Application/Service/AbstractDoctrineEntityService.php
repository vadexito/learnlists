<?php

namespace Application\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractDoctrineEntityService
{
    protected $repository;
    protected $entityClass;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,$entityClass)
    {
        $this->setObjectManager($om);
        $this->setEntityClass($entityClass);
        $this->repository = $om->getRepository($this->getEntityClass());
    }
    
    public function fetchAll()
    {
        return $this->repository->findAll();
    }
    
    public function fetchById($id)
    {
        return $this->getObjectManager()->find($this->getEntityClass(),$id);
    }
    
    public function insert($entity)
    {
        $this->getObjectManager()->persist($entity);
        $this->getObjectManager()->flush();
        
        return $entity->getId();
    }
    
    public function update($entity)
    {
        $this->getObjectManager()->flush();
        
        return true;
    }
    
    /**
     * Delete listquest if it exists and return true, return false otherwise
     * @param type $listquestId
     */
    public function delete($entityId)
    {
        $entity = $this->repository->find($entityId);
        if ($entity){
            $this->getObjectManager()->remove($entity);
            $this->getObjectManager()->flush();
            return true;
        }
        return false;
    }
    
    public function getCount()
    {
        $qb = $this->repository->createQueryBuilder('l');
        $count = $qb->select($qb->expr()->count('l'))
                    ->getQuery()
                    ->getSingleScalarResult();
        
        return $count;
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;              
    }
    
    public function getEntityClass()
    {
        return $this->entityClass;              
    }
}