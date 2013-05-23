<?php

namespace LrnlListquests\Form\Element;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractSelectElementFactory implements AbstractFactoryInterface
{
    
    protected $_services = [
        'Category' => '\LrnlListquests\Form\Element\Category',
        'Level' => '\LrnlListquests\Form\Element\Level',
        'Language' => '\LrnlListquests\Form\Element\Language',
    ];
    
    public function canCreateServiceWithName(
        ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (in_array($requestedName,array_keys($this->_services)) ||
             in_array($name,array_keys($this->_services))   )
        {
            return true;
        };
    }

    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $name = $this->_services[$requestedName];        
        $service   = new $name();
        
        $formElementManager = $serviceLocator;
        $applicationServices = $formElementManager->getServiceLocator();
        $objectManager = $applicationServices->get('Doctrine\ORM\EntityManager');
        $service->setObjectManager($objectManager);
        return $service;
    }    
}