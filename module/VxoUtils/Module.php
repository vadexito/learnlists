<?php

namespace VxoUtils;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\FilterProviderInterface;


class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    FilterProviderInterface   
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
    
    public function getFilterConfig()
    {
        return [
            'invokables' => [
                'filerenamealnumstrict' => 'VxoUtils\Filter\File\AlnumStrictFileFilter',
            ],
        ];
    }
}
