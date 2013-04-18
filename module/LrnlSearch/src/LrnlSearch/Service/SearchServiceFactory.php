<?php

namespace LrnlSearch\Service;

use LrnlSearch\Service\SearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('config')['lrnl-search'];
        $listquestService = $services->get('learnlists-listquestfactory-service');
        $ratingService = $services->get('wtrating.service');
        $filterConfig = $services->get('config')['lrnl-search']['filters'];
        $service   = new SearchService(
                $config['indexPath'],$listquestService,
                $ratingService,new Parameters($filterConfig)
        );
        return $service;
    }
}