<?php

namespace LrnlSearch\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use LrnlSearch\Service\ElasticaSearchService as SearchService;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('config')['lrnl-search'];
        $listquestService = $services->get('learnlists-listquestfactory-service');
        $filterConfig = $services->get('config')['lrnl-search']['filters'];
        $service   = new SearchService(
                $config['indexPath'],$listquestService,
                new Parameters($filterConfig)
        );
        $ratingService = $services->get('wtrating.service');
        $service->setRatingService($ratingService);
        return $service;
    }
}