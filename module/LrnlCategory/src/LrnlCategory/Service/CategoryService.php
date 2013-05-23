<?php

namespace LrnlCategory\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LrnlCategory\Service\CategoryServiceInterface;
use Application\Service\AbstractDoctrineEntityService;
use LrnlCategory\Exception\InvalidArgumentException;
use LrnlCategory\Entity\CategoryInterface;

class CategoryService extends AbstractDoctrineEntityService 
    implements CategoryServiceInterface
{
    public function __construct(ObjectManager $om,$entityClass)
    {
        parent::__construct($om,$entityClass);
    }
    
    public function insert($category)
    {
        if (!($category instanceof CategoryInterface)){
            throw new InvalidArgumentException('The class to insert should implement category interface.');
        }
        
        return parent::insert($category);
    }
    
    public function update($category)
    {
        if (!($category instanceof CategoryInterface)){
            throw new InvalidArgumentException('The class to insert should implement category interface.');
        }
        
        return parent::update($category);
    }

    /**
     * fetch categories by depth
     * 
     * @param mixed $depths int || array
     * @param array $orderBy in an array ['property' => 'ASC'||'DESC']
     * @param integer $limit
     * @param type $offset
     * @return type array
     * @throws InvalidArgumentException
     */
    public function fetchByDepth($depth,array $orderBy = null, $limit = null, $offset = null)
    {
        if (!is_int($depth) && !is_array($depth)){
            throw new InvalidArgumentException('The depth should be an integer or an array.');
        }        
        return $this->getRepository()->findBy(['depth' => $depth],$orderBy,$limit,$offset);
    }
}