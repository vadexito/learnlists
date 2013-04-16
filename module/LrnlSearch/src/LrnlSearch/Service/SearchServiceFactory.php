<?php

namespace LrnlSearch\Service;

use LrnlSearch\Service\SearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $objectManager = $services->get('doctrine.entitymanager.orm_default');
        $user = $services->get('zfcuser_auth_service')->getIdentity();
        $config = $services->get('config')['lrnl-search'];
        $service   = new SearchService($config['indexPath']);
        return $service;
    }
}