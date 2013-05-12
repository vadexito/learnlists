<?php

namespace LrnlListquests\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;
use LrnlListquests\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use LrnlListquests\Options\ModuleOptions;
use LrnlListquests\Exception\ServiceException;
use ReflectionClass;
use DateTime;

class ListquestService
{
    protected $repository;    
    protected $user;    
    protected $options;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,
        UserInterface $user = NULL, ModuleOptions $options)
    {
        $this->setObjectManager($om);
        $this->repository = $om->getRepository($options->getListquestEntityClass());
        $this->user = $user;
        $this->options = $options;        
    }
    
    public function fetchAll()
    {
        return $this->repository->findAll();
    }
    
    /**
     * function fetching all listquest with sort using a property
     * 
     * @param string $property, property of the Listquest class
     * @param string $direction, direction for sorting can be SORT_DESC or SORT_ASC
     * @return array of Listquest Class
     * @throws ServiceException
     */
    public function fetchAllSortBy($property , $direction = SORT_DESC)
    {
        $listquestClass = $this->options->getListquestEntityClass();
        $reflectionClass = new ReflectionClass($listquestClass);
        if (!$reflectionClass->hasProperty($property)){
            throw new ServiceException('The Class '.$listquestClass. 'has no '.$property. 'property.');
        }
        
        
        $sortCallable = function($list1,$list2) use ($property,$direction) {
            
            if (is_object($list1->$property)){
                $count1 = $list1->$property->count();
                $count2 = $list2->$property->count();   
            } else {
                $count1 = $list1->$property;
                $count2 = $list2->$property;   
            }
            
            $direction = $direction === SORT_DESC ? 1 : -1;
            $diff = ($count1 - $count2) * $direction;
            return  $diff > 0 ? -1 : 1;
        };
        
        $lists = $this->repository->findAll();
        usort($lists,$sortCallable);
        
        return $lists;
    }
    
    public function fetchById($id)
    {
        $listquestClass = $this->options->getListquestEntityClass();
        return $this->getObjectManager()->find($listquestClass,$id);
    }

    public function insertListquest(Listquest $listquest)
    {
        $listquest->setCreationDate(new DateTime());
        
        if ($this->user){
            $listquest->author = $this->user;
        }
        
        $this->getObjectManager()->persist($listquest);
        $this->getObjectManager()->flush();
        
        return $listquest->id;
    }
    
    public function updateListquest(Listquest $listquest)
    {
        $this->getObjectManager()->flush();
    }
    
    /**
     * Delete listquest if it exists and return true, return false otherwise
     * @param type $listquestId
     */
    public function deleteListquest($listquestId)
    {
        $listquest = $this->repository->find($listquestId);
        if ($listquest){
            $this->getObjectManager()->remove($listquest);
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
}