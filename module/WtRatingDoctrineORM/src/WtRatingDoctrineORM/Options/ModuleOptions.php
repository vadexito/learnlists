<?php
namespace WtRatingDoctrineORM\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements WtRatingDoctrineORMOptionsInterface
{
    /**
     * @var string
     */
    protected $wtRatingEntityClass = 'WtRating\Entity\Rating';
    
     /**
     * @var bool
     */
    protected $enableDefaultEntities = true;

    /**
     * @param boolean $enableDefaultEntities
     */
    public function setEnableDefaultEntities($enableDefaultEntities)
    {
        $this->enableDefaultEntities = $enableDefaultEntities;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnableDefaultEntities()
    {
        return $this->enableDefaultEntities;
    }
    
    public function setWtRatingEntityClass($entityClass) 
    {
        $this->wtRatingEntityClass = $entityClass;
        return $this;
    }

    public function getWtRatingEntityClass() 
    {
        return $this->wtRatingEntityClass;
    }
}