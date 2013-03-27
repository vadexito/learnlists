<?php

namespace Question;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Question\Form\TagFieldset;
use Question\Form\EditQuestionsInListquestForm;
use Question\Form\ListquestForm;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface
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
                'Question\Form\ListquestForm' =>  function($sm) {
            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');    
                    return new ListquestForm($entityManager);
                },
                'Question\Form\EditQuestionsInListquestForm' =>  function($sm) {
            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionsInListquestForm($entityManager);
                },
            ],
        ];
    }
}
