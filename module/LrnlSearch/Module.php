<?php

namespace LrnlSearch;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use LrnlSearch\Form\FiltersForm;

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
                    $formManager = $sm->get('FormElementManager');
                    $form = $formManager->get('LrnlSearch\Form\SearchForm');
                    return $form;
                },
                'learnlists-form-filter' =>  function($sm) {
                    $searchService = $sm->get('learnlists-search-service-factory');
                    $listquestService = $sm->get('learnlists-listquestfactory-service');
                    $filterConfig = $sm->get('config')['lrnl-search']['filtersForm'];
                    return new FiltersForm($listquestService,$searchService,$filterConfig,'filtersForm');
                },
            ],
        ];
    }
}
