<?php

namespace LrnlCategory\InputFilter;

use Application\InputFilter\PictureInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class PictureInputFilterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnlcategory_module_options');
        $tempDir = $options->getTmpPictureUploadDir();
        $filterPluginManager = $services->get('FilterManager');
        
        $service = new PictureInputFilter($filterPluginManager,$tempDir,'pictureId');
        
        return $service;
    }
}