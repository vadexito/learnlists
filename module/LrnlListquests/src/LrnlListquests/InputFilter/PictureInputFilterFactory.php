<?php

namespace LrnlListquests\InputFilter;

use Application\InputFilter\PictureInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PictureInputFilterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnllistquests_module_options');
        $tempDir = $options->getTmpPictureUploadDir();
        $filterPluginManager = $services->get('FilterManager');
        
        $service = new PictureInputFilter($filterPluginManager,$tempDir,'pictureId');
        
        return $service;
    }
}