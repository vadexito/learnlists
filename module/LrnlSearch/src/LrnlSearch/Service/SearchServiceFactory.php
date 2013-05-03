<?php

namespace LrnlSearch\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;

class SearchServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $config = $services->get('config')['lrnl-search'];
        $listquestService = $services->get('learnlists-listquestfactory-service');
        $service   = new $config['lrnl_search_service'](
                $config['indexPath'],$listquestService,
                new Parameters($config['filters'])
        );
        $ratingService = $services->get('wtrating.service');
        $service->setRatingService($ratingService);
        return $service;
    }
}