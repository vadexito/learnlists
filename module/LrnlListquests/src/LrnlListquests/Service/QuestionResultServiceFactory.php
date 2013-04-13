<?php

namespace LrnlListquests\Service;

use LrnlListquests\Service\QuestionresultService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LrnlListquests\Entity\Questionresult;


class QuestionresultServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $service   = new QuestionresultService($objectManager,new Questionresult);
        
        return $service;
    }
}