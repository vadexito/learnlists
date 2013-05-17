<?php

namespace LrnlListquests\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractEntityServiceFactory implements AbstractFactoryInterface
{
    
    protected $_services = [
        'learnlists-category-service' => 'LrnlListquests\Service\CategoryService',
        'learnlists-level-service' => 'LrnlListquests\Service\LevelService',
        'learnlists-language-service' => 'LrnlListquests\Service\LanguageService',
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
        $options = $serviceLocator->get('lrnllistquests_module_options');
        $name = $this->_services[$requestedName];
        $service   = new $name($options);
        
        return $service;
    }    
}