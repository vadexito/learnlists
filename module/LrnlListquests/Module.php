<?php

namespace LrnlListquests;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

use LrnlListquests\Form\TagFieldset;
use LrnlListquests\Form\EditQuestionsInListquestForm;
use LrnlListquests\Form\ListquestForm;
use LrnlListquests\Form\EditQuestionForm;


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
                'LrnlListquests\Form\ListquestForm' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');    
                    return new ListquestForm($entityManager);
                },
                'LrnlListquests\Form\EditQuestionsInListquestForm' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionsInListquestForm($entityManager);
                },
                'edit-question-form' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionForm($entityManager);
                },
            ],
        ];
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'listquestCollection' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\ListquestCollection;
                    $viewHelper->setListquestService($locator->get('learnlists-listquestfactory-service'));
                    $viewHelper->setRatingService($locator->get('wtrating.service'));
                    return $viewHelper;
                },
            ),
        );
    }
}
