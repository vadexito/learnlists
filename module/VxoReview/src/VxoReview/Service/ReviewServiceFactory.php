<?php

namespace VxoReview\Service;

use VxoReview\Service\ReviewService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReviewServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $user = $services->get('zfcuser_auth_service')->getIdentity();
        $options = $services->get('vxoreview_module_options');
        $entityClass = $options->getReviewEntityClass();
        $service   = new ReviewService($objectManager,$entityClass,$user);
        
        return $service;
    }
}