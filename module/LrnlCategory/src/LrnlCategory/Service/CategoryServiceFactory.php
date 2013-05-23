<?php

namespace LrnlCategory\Service;

use LrnlCategory\Service\CategoryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class CategoryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $options = $services->get('lrnlcategory_module_options');
        $entityClass = $options->getCategoryEntityClass();
        $service   = new CategoryService($objectManager,$entityClass);
        
        return $service;
    }
}