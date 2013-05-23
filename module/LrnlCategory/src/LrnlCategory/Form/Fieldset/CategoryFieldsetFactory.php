<?php

namespace LrnlCategory\Form\Fieldset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CategoryFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');
        $options = $sl->get('lrnlcategory_module_options');
        $categoryEntityClass = $options->getCategoryEntityClass();

        $factory = new FormFactory($services);
        $fieldset = $factory->createFieldset([
            'type' => 'Zend\Form\Fieldset',
            'name' => 'category',                    
            'hydrator' => new DoctrineHydrator($om,$categoryEntityClass),
            'object' => $categoryEntityClass,
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
                        'name' => 'name',
                    ],
                    'spec' => [
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => [ 
                            'id'    => 'name',
                            'autocomplete'    => 'off'
                        ],
                        'options' => [
                            'label' => _('Name (unique) for the category')
                        ],
                    ],
                ],
               [
                    'flags' => [
                        'name' => 'description',
                    ],
                    'spec' => [
                        'type' => 'Zend\Form\Element\Textarea',
                        'attributes' => [ 
                            'id'    => 'description',
                            'autocomplete'    => 'off'
                        ],
                        'options' => [
                            'label' => _('Description for the category')
                        ],
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'depth',
                    ],
                    'spec' => [
                        'type'  => 'Zend\Form\Element\Range',
                        'attributes' => [
                            'min'    => '0',
                            'max'    => '5',
                        ],
                        'options' => [
                            'label' => _('Depth of the category in the hierarchie (1 are the highest parents')
                        ],
                    ],
                ],
//                [
//                    'flags' => [
//                        'name' => 'parent',
//                    ],
//                    'spec' => [
//                        'type'  => 'LrnlListquests\Form\Element\Category',
////                        'type'  => 'DoctrineModule\Form\Element\ObjectSelect',
////                        'attributes' => [
////                            'id'    => 'category',
////                            'class' => 'chzn-select',
////                        ],
////                        'options' => [
////                            'label' => _('Parent of the category if any'),                                        
////                            'object_manager' => $om,
////                            'target_class'   => $categoryEntityClass,
////                            'property'       => 'name',
////                            'is_method'      => true,
////                            'find_method'    => [
////                                'name'   => 'findBy',
////                                'params' => [
////                                    'criteria' => ['depth' => [1]],
////                                ],
////                            ],        
////                        ],
//                    ],
//                ],
                [
                    'flags' => [
                        'name' => 'pictureId',
                    ],
                    'spec' => [
                        'type'  => 'Zend\Form\Element\File',
                        'options' => [
                            'label' => _('Choose a picture')
                        ],
                    ],
                ],
            ],
        ]);

        $strategy = $sl->get('listquest_picture_hydratorstrategy');
        $fieldset->getHydrator()->addStrategy('pictureId',$strategy);
        return $fieldset;
    }
}