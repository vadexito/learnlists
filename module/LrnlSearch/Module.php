<?php

namespace LrnlSearch;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use LrnlSearch\Form\SearchForm;
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Form\FilterTermCheckboxElement;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
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
                'learnlists-form-search' =>  function($sm) {
                    return new SearchForm();
                },
                'learnlists-form-filter' =>  function($sm) {
                    $searchService = $sm->get('learnlists-search-service-factory');
                    return new FiltersForm('filtersForm',$searchService);
                },
            ],
        ];
    }
}
