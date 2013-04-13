<?php

namespace LrnlListquests\Service;

use LrnlListquests\Service\RoundService;
use LrnlListquests\Entity\Round;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoundServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $user = $services->get('zfcuser_auth_service')->getIdentity();
        $service   = new RoundService($objectManager,$user, new Round());
        $service->setListquestService($services->get('learnlists-listquestfactory-service'));
        return $service;
    }
}