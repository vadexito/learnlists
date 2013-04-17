<?php

namespace LrnlSearch\Service;

use LrnlSearch\Service\SearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('config')['lrnl-search'];
        $listquestService = $services->get('learnlists-listquestfactory-service');
        $service   = new SearchService($config['indexPath'],$listquestService);
        return $service;
    }
}