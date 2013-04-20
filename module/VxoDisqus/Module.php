<?php

namespace VxoDisqus;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ViewHelperProviderInterface
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
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'disqus' => function ($sm) {
                    $config = $sm->getServiceLocator()
                                 ->get('config')['vxo-disqus']['config'];
                    $viewHelper = new View\Helper\Disqus();
                    $viewHelper->setConfig($config);
                    return $viewHelper;
                },
            ),
        );
    }
}
