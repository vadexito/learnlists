<?php

namespace VxoReview\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReviewControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controller = new ReviewController();
        
        $sm = $serviceLocator->getServiceLocator();        
        $controller->setReviewService($sm->get('review-service'));
        
        $options = $sm->get('vxoreview_module_options');
        $controller->setRedirectRoute($options->getRedirectRoute());
        return $controller;
    }
}
