<?php

namespace LrnlListquests\HydratorStrategy;

use Application\HydratorStrategy\PictureHydratorStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PictureHydratorStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnllistquests_module_options');
        $tempDir = $options->getTmpPictureUploadDir();
        $service = new PictureHydratorStrategy($tempDir,['listquest','thumb']);        
        $service->setFileBankService($services->get('FileBank'));
        $service->setThumbnailer($services->get('WebinoImageThumb'));
        
        return $service;
    }
}