<?php

namespace LrnlListquests;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\InputFilter\InputFilter;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use LrnlListquests\Form\EditQuestionsInListquestForm;
use LrnlListquests\Form\CreateListquestForm;
use LrnlListquests\Form\EditQuestionForm;
use LrnlListquests\Form\ListquestFieldset;
use LrnlListquests\HydratorStrategy\Picture as PictureStrategy;
use LrnlListquests\InputFilter\Picture as PictureInputFilter;
use LrnlListquests\Form\Fieldset\PictureFieldset;


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
                'lrnllistquests_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['lrnl-listquests']) ? $config['lrnl-listquests'] : []);
                },                
                'listquest-fieldset' => function($sm) { 
                    $options = $sm->get('lrnllistquests_module_options');
                    $listquestEntityClass = $options->getListquestEntityClass();
                    
                    $om = $sm->get('Doctrine\ORM\EntityManager');                    
                    $doctrineHydrator = new DoctrineHydrator(
                        $om,
                        $listquestEntityClass
                    );                    
                    $category = $sm->get('learnlists-category-formelement');
                    
                    $listquestFieldset = new ListquestFieldset('listquest',$om);
                    $listquestFieldset->add($category);
                    $listquestFieldset->setHydrator($doctrineHydrator);
                    $listquestFieldset->setObject(new $listquestEntityClass);
                    
                    return $listquestFieldset;
                
                },
                
                'listquest-form-create' =>  function($sm) {            
                    $listquestFieldset = $sm->get('listquest-fieldset');
                    $listquestFieldset->setUseAsBaseFieldset(true);
                    $listquestFieldset->remove('questions');                    
                    
                    $pictureFieldset = $sm->get('listquest_picture_fieldset');
                    $fileFilter = $sm->get('listquest_picture_inputfilter'); 
                    $fieldsetFilter = (new InputFilter())->add($fileFilter);
                    
                    $form = new CreateListquestForm(); 
                    $form->add($listquestFieldset);
                    $form->add($pictureFieldset);
                    $form->getInputFilter()->add($fieldsetFilter,'picture');
                    
                    return $form;
                },
                'listquest-form-edit' =>  function($sm) {                    
                    $listquestFieldset = $sm->get('listquest-fieldset');
                    $listquestFieldset->setUseAsBaseFieldset(true);
                    $listquestFieldset->remove('tags');
                        
                    $form = new EditQuestionsInListquestForm();
                    $form->add($listquestFieldset);
                    
                    return $form;
                },
                'edit-question-form' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionForm($entityManager);
                }, 
                'listquest_picture_hydrator' => function ($sm) {
                    $options = $sm->get('lrnllistquests_module_options');
                    $hydrator = new PictureStrategy($options);
                    $hydrator->setFileBankService($sm->get('FileBank'));
                    $hydrator->setListquestService($sm->get('learnlists-listquestfactory-service'));
                    $hydrator->setThumbnailer($sm->get('WebinoImageThumb'));
                    return $hydrator;
                },
                'listquest_picture_inputfilter' => function ($sm) {
                    $config = $sm->get('lrnllistquests_module_options');
                    $targetUpload = $config->getTmpPictureUploadDir();
                    $fileFilter = new PictureInputFilter('pictureId',$targetUpload);
                    
                    return $fileFilter;
                },
                'listquest_picture_fieldset' => function ($sm) {
                    $config = $sm->get('lrnllistquests_module_options');
                    $listquestEntityClass = $config->getListquestEntityClass();                
                    $om = $sm->get('Doctrine\ORM\EntityManager');
                    
                    $fileHydratorStrategy = $sm->get('listquest_picture_hydrator');
                    $doctrineHydrator = new DoctrineHydrator(
                        $om,
                        $listquestEntityClass
                    );
                    $doctrineHydrator->addStrategy('pictureId',$fileHydratorStrategy);
                    $fieldset = new PictureFieldset('picture');
                    $fieldset->setHydrator($doctrineHydrator);
                    
                    return $fieldset;
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
                'listquestPictureUrl' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $options = $locator->get('lrnllistquests_module_options');
                    $viewHelper = new View\Helper\ListquestPictureUrl();
                    $viewHelper->setOptions($options);
                    return $viewHelper;
                },
            ),
        );
    }
}
