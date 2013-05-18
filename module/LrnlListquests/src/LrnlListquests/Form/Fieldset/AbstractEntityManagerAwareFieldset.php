<?php
namespace LrnlListquests\Form\Fieldset;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Form\Fieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractEntityManagerAwareFieldset extends Fieldset implements 
    ObjectManagerAwareInterface,
    ServiceLocatorAwareInterface
{
    use ProvidesObjectManager;
    use ServiceLocatorAwareTrait;
    
    protected $_entityClass;
    
    public function init()
    {
        $entityClass = $this->getEntityClass();
        
        $doctrineHydrator = new DoctrineHydrator(
            $this->getObjectManager(),
            $entityClass
        );
        $this->setHydrator($doctrineHydrator);
        $this->setObject(new $entityClass);
    }
    
    public function getEntityClass()
    {
        return $this->_entityClass;
    }
    
    public function setEntityClass($class)
    {
        $this->_entityClass = $class;
        return $this;
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (isset($options['entity_class'])) {
            $this->setEntityClass($options['entity_class']);
        }

        return $this;
        
        
    }
}