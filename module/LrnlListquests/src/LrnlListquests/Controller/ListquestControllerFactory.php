<?php

namespace LrnlListquests\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListquestControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $controller = new ListquestController();
        $controller->setListquestService($sm->get('learnlists-listquestfactory-service'));
        $controller->setSearchService($sm->get('learnlists-search-service-factory'));
        $controller->setCategoryService($sm->get('category-service'));
        $controller->setReviewService($sm->get('review-service'));
        $controller->setTranslator($sm->get('translator'));
        return $controller;
    }
}
