<?php

namespace LrnlListquests;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\InputFilter\InputFilter;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use LrnlListquests\Form\EditQuestionForm;
use LrnlListquests\Form\ChangePictureForm;
use LrnlListquests\InputFilter\Picture as PictureInputFilter;
use LrnlListquests\Form\Fieldset\PictureFieldset;


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
                'lrnllistquests_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['lrnl-listquests']) ? $config['lrnl-listquests'] : []);
                },
                'listquest-form-create' =>  function($sm) {            
                    $pictureFieldset = $sm->get('listquest_picture_fieldset');
                    $fileFilter = $sm->get('listquest_picture_inputfilter'); 
                    $fileFilter->setRequired(false);
                    $fieldsetFilter = (new InputFilter())->add($fileFilter);
                    
                    $formManager = $sm->get('FormElementManager');
                    $form = $formManager->get('LrnlListquests\Form\CreateListquestForm');
                    $form->add($pictureFieldset);
                    $form->getInputFilter()->add($fieldsetFilter,'picture');
                    
                    return $form;
                },
                        
                'listquest-form-changepicture' =>  function($sm) {            
                    $pictureFieldset = $sm->get('listquest_picture_fieldset');
                    $fileFilter = $sm->get('listquest_picture_inputfilter'); 
                    $fieldsetFilter = (new InputFilter())->add($fileFilter);
                    
                    $form = new ChangePictureForm(); 
                    $form->add($pictureFieldset);
                    $form->getInputFilter()->add($fieldsetFilter,'picture');
                    
                    return $form;
                },
                'listquest-form-edit' =>  function($sm) {                    
                    $formManager = $sm->get('FormElementManager');
                    $form = $formManager->get('LrnlListquests\Form\EditListquestForm'); 
                    return $form;
                },
                'edit-question-form' =>  function($sm) {            
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');  
                    return new EditQuestionForm($entityManager);
                }, 
                'listquest_picture_hydratorstrategy' => 'LrnlListquests\HydratorStrategy\PictureHydratorStrategyFactory',                
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
                    
                    $fileHydratorStrategy = $sm->get('listquest_picture_hydratorstrategy');
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
    
    public function getFormElementConfig()
    {
        return [
            'initializers' => [
                'Application\Initializer\EntityManagerInitializer', 
            ],
            'abstract_factories' => [
                'LrnlListquests\Form\Element\AbstractSelectElementFactory'
            ],
            'factories' => [
                'ListquestFieldset' => 'LrnlListquests\Form\Fieldset\ListquestFieldsetFactory'
            ],
            'invokables' => [
               'QuestionFieldset' =>  'LrnlListquests\Form\Fieldset\QuestionFieldset',
               'TagFieldset' =>  'LrnlListquests\Form\Fieldset\TagFieldset',
               'LevelFieldset' =>  'LrnlListquests\Form\Fieldset\LevelFieldset',
               'LanguageFieldset' =>  'LrnlListquests\Form\Fieldset\LanguageFieldset',
               'CategoryFieldset' =>  'LrnlListquests\Form\Fieldset\CategoryFieldset',
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
                'rating' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\Rating();
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
                    $fileBankService = $sm->get('FileBank');
                    $viewHelper = new View\Helper\ListquestPictureUrl();
                    $viewHelper->setFileBankService($fileBankService);
                    return $viewHelper;
                },
            ),
        );
    }
}
