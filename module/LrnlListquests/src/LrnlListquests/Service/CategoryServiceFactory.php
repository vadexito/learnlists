<?php

namespace LrnlListquests\Service;

use LrnlListquests\Service\ListquestService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnllistquests_module_options');
        $service   = new CategoryService($options);
        
        return $service;
    }
}