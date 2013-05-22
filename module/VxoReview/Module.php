<?php

namespace VxoReview;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;


class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ViewHelperProviderInterface,
    ServiceProviderInterface,
    FormElementProviderInterface
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
    
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'vxo-review-service' => 'VxoReview\Service\ReviewServiceFactory',
                'vxoreview_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['vxo-review']) ? $config['vxo-review'] : []);
                },
            ],
            'aliases' => [
                'review-service' => 'vxo-review-service',
            ],
        ];
    }
    
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'vxoreview' => function ($sm) {
                    $config = $sm->getServiceLocator()
                                 ->get('vxoreview_module_options');
                    $viewHelper = new View\Helper\VxoReview();
                    $viewHelper->setOptions($config);
                    return $viewHelper;
                },
            ],
        ];
    }

    public function getFormElementConfig()
    {
        return [
            'factories' => [
                'review-create-form' => 'VxoReview\Form\ReviewCreateFormFactory',
                'review-edit-form' => 'VxoReview\Form\ReviewEditFormFactory',
            ],
        ];
    }
}
