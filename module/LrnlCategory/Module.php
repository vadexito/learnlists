<?php

namespace LrnlCategory;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;


class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
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
                
                'lrnlcategory_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['lrnl-category']) ? $config['lrnl-category'] : []);
                },
            ],
            'aliases' => [
                'category-service' => 'lrnl-category-service',
            ],
        ];
    }
    


    public function getFormElementConfig()
    {
        return [
            'factories' => [
                'category-create-form' => 'LrnlCategory\Form\CategoryCreateFormFactory',
                'category-edit-form' => 'LrnlCategory\Form\CategoryEditFormFactory',
                'category-changepicture-form' => 'LrnlCategory\Form\CategoryChangePictureFormFactory',
                'LrnlCategoryFieldset' => 'LrnlCategory\Form\Fieldset\CategoryFieldsetFactory',
            ],
        ];
    }
}
