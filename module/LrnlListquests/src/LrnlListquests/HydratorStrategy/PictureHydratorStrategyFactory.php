<?php

namespace LrnlListquests\HydratorStrategy;

use LrnlListquests\HydratorStrategy\PictureHydratorStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PictureHydratorStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnllistquests_module_options');
        $service = new PictureHydratorStrategy($options);
        $service->setFileBankService($services->get('FileBank'));
        $service->setListquestService($services->get('learnlists-listquestfactory-service'));
        $service->setThumbnailer($services->get('WebinoImageThumb'));
        
        return $service;
    }
}