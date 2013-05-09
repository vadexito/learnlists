<?php

namespace LrnlListquests;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use LrnlListquests\Form\EditQuestionsInListquestForm;
use LrnlListquests\Form\ListquestForm;
use LrnlListquests\Form\EditQuestionForm;
use LrnlListquests\Form\Element\Category;
use LrnlListquests\Form\ListquestFieldset;
use LrnlListquests\Hydrator\Picture as PictureHydrator;


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
                'listquest-fieldset' => function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    
                    $listquestFieldset = new ListquestFieldset($entityManager);
                    
                    $categories = $sm->get('lrnllistquests_module_options')
                                     ->getCategories();  
                    $category = new Category('category',$categories);
                    $category->setLabel(_('category'));
                    
                    $listquestFieldset->add($category);
                    
                    return $listquestFieldset;
                
                },
                
                'LrnlListquests\Form\ListquestForm' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $form = new ListquestForm($entityManager);
                    
                    $listquestFieldset = $sm->get('listquest-fieldset');
                    $listquestFieldset->setUseAsBaseFieldset(true);
                    $listquestFieldset->remove('questions');
                    
                    $form->add($listquestFieldset);
                    
                    return $form;
                },
                'LrnlListquests\Form\EditQuestionsInListquestForm' =>  function($sm) {                    
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $form = new EditQuestionsInListquestForm($entityManager);
                    $listquestFieldset = $sm->get('listquest-fieldset');
                    $listquestFieldset->setUseAsBaseFieldset(true);
                    $listquestFieldset->remove('tags');
                    $form->add($listquestFieldset);
                    return $form;
                },
                'edit-question-form' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionForm($entityManager);
                }, 
                'lrnllistquests_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['lrnl-listquests']) ? $config['lrnl-listquests'] : []);
                },
                'listquest_picture_hydrator' => function ($sm) {
                    $config = $sm->get('lrnllistquests_module_options');
                    $hydrator = new PictureHydrator($config);
                    $hydrator->setFileBankService($sm->get('FileBank'));
                    $hydrator->setListquestService($sm->get('learnlists-listquestfactory-service'));
                    return $hydrator;
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
                'listquestCount' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\ListquestCount();
                    $viewHelper->setListquestService($locator->get('learnlists-listquestfactory-service'));
                    return $viewHelper;
                },
                'results' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\Results();
                    $viewHelper->setRoundService($locator->get('learnlists-roundfactory-service'));
                    return $viewHelper;
                },
            ),
        );
    }
}
