<?php

namespace LrnlCategory\HydratorStrategy;

use Application\HydratorStrategy\PictureHydratorStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PictureHydratorStrategyFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnlcategory_module_options');
        $tempDir = $options->getTmpPictureUploadDir();
        $service = new PictureHydratorStrategy($tempDir,['category','thumb']);
        $service->setFileBankService($services->get('FileBank'));
        $service->setThumbnailer($services->get('WebinoImageThumb'));
        
        return $service;
    }
}