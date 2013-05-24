<?php

namespace LrnlListquests\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LrnlListquests\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use LrnlListquests\Options\ModuleOptions;
use LrnlListquests\Exception\ServiceException;
use ReflectionClass;
use DateTime;
use Application\Service\AbstractDoctrineEntityService;

class ListquestService extends AbstractDoctrineEntityService
{
    protected $user;
    
    public function __construct(ObjectManager $om,
        UserInterface $user = NULL, ModuleOptions $options)
    {
        parent::__construct($om,$options->getListquestEntityClass());        
        $this->user = $user;
               
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
        $this->checkIsListquestProperty($property);
        
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
        
        $lists = $this->fetchAll();
        usort($lists,$sortCallable);
        
        return $lists;
    }
    
    public function fetchByIds(array $ids)
    {
         if (empty($ids)){
            return [];
         }
        
        return $this->getRepository()->findById($ids);
    }
    
    public function insertListquest(Listquest $listquest)
    {
        $listquest->setCreationDate(new DateTime());
        
        if ($this->user){
            $listquest->author = $this->user;
        }
        
        return parent::insert($listquest);
    }
    
    public function updateListquest(Listquest $listquest)
    {
        return $this->update($listquest);
    }
    
    public function deleteListquest($listquestId)
    {
        return delete($listquestId);
    }
    
    public function getCount()
    {
        $qb = $this->getRepository()->createQueryBuilder('l');
        $count = $qb->select($qb->expr()->count('l'))
                    ->getQuery()
                    ->getSingleScalarResult();
        
        return $count;
    }
    
    /**
     * 
     * @param type $property
     * @param type $Idlist
     * @param type $values
     * @return array of array with property term, termId and count
     * @throws ServiceException
     */
    public function getFacet($property,$Idlist,array $values)
    {
        $this->checkIsListquestProperty($values['properties'][0]);
        
        if ($Idlist === 'all'){
            $lists = $this->fetchAll();
        } else if (!is_array($Idlist)){
           throw new ServiceException('The property $Idlist in getFacet function should be either array of equal to string "all"');
        } else {
            $lists = $this->fetchByIds($Idlist);
        }
        
        if (!is_array($values) || !isset($values['properties'])
                || !isset($values['values']) || !isset($values['values_key'])){
            throw new ServiceException('You should provide as $value an array with properties,values_key and values properties');
        }
        
        if (!isset($values['propertyForNaming'])){
            $values['propertyForNaming'] = $values['values_key'];
        }
        $getterTerm = 'get'.ucfirst($values['propertyForNaming']); 
        $getterKey = 'get'.ucfirst($values['values_key']);
        $facetValues=[];
        foreach ($values['values'] as $value){
            $count = 0;            
            foreach ($lists as $list){
                $entity = $list;
                foreach ($values['properties'] as $prop){
                    $getter = 'get'.ucfirst($prop);
                    $entity = $entity->$getter();
                }
                
                if (is_object($entity) && method_exists($entity,$getterKey)){
                    if ($entity->$getterKey() === $value){
                        $count ++;
                    }
                }
                    
            }
            $newValue =  ['count' => $count];
            if (isset($values['targetEntity'])){
                $entity = $this->getObjectManager()
                             ->getRepository($values['targetEntity'])
                             ->findOneBy([$values['values_key'] => $value]);
                if (is_object($entity)){
                    $term = $entity->$getterTerm();
                }
                $newValue['termId'] = $value;
            } else {
                $term = $value;
            }   
            
            $newValue['term']=$term;
            $facetValues[] = $newValue;
            
        }
        
        return $facetValues;

    }
    
    public function checkIsListquestProperty($property)
    {
        $listquestClass = $this->getEntityClass();
        $reflectionClass = new ReflectionClass($listquestClass);
        if (!$reflectionClass->hasProperty($property)){
            throw new ServiceException('The Class '.$listquestClass. 'has no '.$property. 'property.');
        }
        
        return true;
    }
}