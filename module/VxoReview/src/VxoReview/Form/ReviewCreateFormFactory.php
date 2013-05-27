<?php

namespace VxoReview\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ReviewCreateFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');
        $options = $sl->get('vxoreview_module_options');
        $reviewEntityClass = $options->getReviewEntityClass();
        $maxRating = $options->getMaxRating();

        $factory = new FormFactory($services);
        $form = $factory->createForm([
            'type' => 'Zend\Form\Form',
            'name'       => 'review-create-form', 
            'hydrator' => new DoctrineHydrator($om),
            'validation_group' => [
                'csrf',
                'review' => [
                    'reviewedItem',
                    'text', 
                    'rating'
                ],
            ],
            'attribute' => [
                'method' => 'post',
            ],
            'fieldsets' => [
                [
                    'flags' => [
                        'name' => 'review',
                    ],                                
                    'spec' => [
                        'hydrator' => new DoctrineHydrator($om,$reviewEntityClass),
                        'object' => $reviewEntityClass,
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
                                    'name' => 'reviewedItem',
                                ],
                                'spec' => [
                                    'attributes' => [
                                        'type' => 'hidden',
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'name' => 'text',
                                ],
                                'spec' => [
                                    'type' => 'Zend\Form\Element\Textarea',
                                    'attributes' => [ 
                                        'id'    => 'text',
                                        'autocomplete'    => 'off'
                                    ],
                                    'options' => [
                                        'label' => _('You can leave your review here')
                                    ],
                                ],
                            ],
                            [
                                'flags' => [
                                    'name' => 'rating',
                                ],
                                'spec' => [
                                    'type'  => 'Zend\Form\Element\Range',
                                    'attributes' => [
                                        'min'    => '0',
                                        'max'    => $maxRating,
                                    ],
                                    'options' => [
                                        'label' => _('Rating')
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'elements' => [
                [
                    'flags' => [
                        'name' => 'csrf',
                    ],
                    'spec' => [
                        'type' => 'Zend\Form\Element\Csrf',                                    
                    ],
                ],
                [
                    'flags' => [
                        'name' => 'submit',
                    ],
                    'spec' => [
                        'type' => 'submit',
                        'attributes' => [
                            'type'  => 'submit',
                            'value' => _('Add'),
                            'id' => 'submitbutton',
                            'class' => 'btn btn-primary',
                        ], 
                    ],
                ],
            ],
        ]);

        return $form;
    }
}