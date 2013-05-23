<?php

namespace LrnlCategory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CategoryCreateFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');

        $factory = new FormFactory($services);
        $form = $factory->createForm([
            'type' => 'Zend\Form\Form',
            'name'       => 'category-create-form', 
            'hydrator' => new DoctrineHydrator($om),
            'validation_group' => [
                'csrf',
                'category' => [
                    'name',
                    'description', 
                    'depth',
                    //'parent'
                ],
            ],
            'attribute' => [
                'method' => 'post',
            ],
            'fieldsets' => [
                [
                    'spec' => [
                        'type' => 'LrnlCategoryFieldset',                                   
                    ],
                    'flags' => [
                        'name' => 'category',
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

        $form->get('category')->remove('pictureId');
        return $form;
    }
}