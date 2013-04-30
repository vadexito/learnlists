<?php

namespace LrnlSearch;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Stdlib\Parameters;

use LrnlSearch\Form\SearchForm;
use LrnlSearch\Form\FiltersForm;
use LrnlListquests\Form\Element\Category;

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
                    $form = new SearchForm();
                    
                    $categories = $sm->get('config')['lrnl-listquests']['categories'];  
                    $category = new Category('category',$categories);
                    
                    $form->add($category);
                    
                    return $form;
                },
                'learnlists-form-filter' =>  function($sm) {
                    $searchService = $sm->get('learnlists-search-service-factory');
                    $filterConfig = $sm->get('config')['lrnl-search']['filters'];
                    return new FiltersForm('filtersForm',
                            $searchService,new Parameters($filterConfig));
                },
            ],
        ];
    }
}
