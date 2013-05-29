<?php

namespace LrnlListquests\Form\Fieldset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


class ListquestFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');
        $options = $sl->get('lrnllistquests_module_options');
        $listquestEntityClass = $options->getListquestEntityClass();

        $factory = new FormFactory($services);
        $fieldset = $factory->createFieldset([
            'type' => 'Zend\Form\Fieldset',
            'name' => 'listquest',                    
            'hydrator' => new DoctrineHydrator($om,$listquestEntityClass),
            'object' => $listquestEntityClass,
            'options' => [
                'use_as_base_fieldset' => true
            ],
            'elements' => [
                [
                    'flags' => [
                        'name' => 'id',
                    ],
                    'spec' => [
                        'attributes' => [
                            'type' => 'hidden',
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'title',
                    ],
                    'spec' => [
                        'type' => 'text',
                        'attributes' => [ 
                            'id'    => 'title',
                            'autocomplete'    => 'off',
                            'required' => 'required'
                         ],
                        'options' => [
                            'label' => _('Title')
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'description',
                    ],
                    'spec' => [
                        'type' => 'textarea',
                        'attributes' => [ 
                            'id'    => 'description',
                            'autocomplete'    => 'off'
                         ],
                        'options' => [
                            'label' => _('Description')
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'tags',
                    ],
                    'spec' => [
                        'type' => 'Zend\Form\Element\Collection',
                        'attributes' => [ 
                            'id'    => 'tags',
                            'autocomplete'    => 'off'
                         ],
                        'options' => [
                            'label' => _('Tags'),                            
                            'count' => 0,
                            'template_placeholder' => '__index__',
                            'should_create_template' => true,
                            'allow_add' => true,
                            'target_element' => [
                                'type' => 'TagFieldset'
                            ],
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'questions',
                    ],
                    'spec' => [
                        'type' => 'Zend\Form\Element\Collection',
                        'attributes' => [ 
                            'id'    => 'questions',
                            'autocomplete'    => 'off'
                         ],
                        'options' => [
                            'label' => _('Add questions'),
                            'count' => 0,
                            'template_placeholder' => '__index__',
                            'should_create_template' => true,
                            'allow_add' => true,
                            'target_element' => [
                                'type' => 'QuestionFieldset'
                            ],
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'pictureId',
                    ],
                    'spec' => [
                        'type'  => 'Zend\Form\Element\File',
                        'options' => [
                            'label' => _('Choose a presentation picture for your quiz')
                        ],
                    ],
                ],
            ],
            'fieldsets' => [
                [
                    'spec' => [
                        'type' => 'CategoryFieldset',                                   
                    ],
                    'flags' => [
                        'name' => 'category',
                    ],
                ],
                [
                    'spec' => [
                        'type' => 'LevelFieldset',                                   
                    ],
                    'flags' => [
                        'name' => 'level',
                    ],
                ],
                [
                    'spec' => [
                        'type' => 'LanguageFieldset',                                   
                    ],
                    'flags' => [
                        'name' => 'language',
                    ],
                ],
            ],
        ]);
        
        $strategy = $sl->get('listquest_picture_hydratorstrategy');
        $fieldset->getHydrator()->addStrategy('pictureId',$strategy);
        return $fieldset;
    }
}