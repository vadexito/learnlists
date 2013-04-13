<?php

namespace LrnlListquests\Service;

use LrnlListquests\Service\ListquestService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListquestServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $user = $services->get('zfcuser_auth_service')->getIdentity();
        $service   = new ListquestService($objectManager,$user);
        
        return $service;
    }
}