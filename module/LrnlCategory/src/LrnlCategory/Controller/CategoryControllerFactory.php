<?php

namespace LrnlCategory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controller = new CategoryController();
        
        $sm = $serviceLocator->getServiceLocator();       
        $controller->setCategoryService($sm->get('category-service'));
        
        $options = $sm->get('lrnlcategory_module_options');
        $controller->setRedirectRoute($options->getRedirectRoute());
        return $controller;
    }
}
