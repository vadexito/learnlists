<?php

namespace LrnlListquests\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ListquestCreateFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');

        $factory = new FormFactory($services);
        $form = $factory->createForm([
            'type' => 'Zend\Form\Form',
            'name'       => 'listquest-create-form', 
            'hydrator' => new DoctrineHydrator($om),
            'validation_group' => [
                'csrf',
                'listquest' => [
                    'title',
                    'description',
                    'category',
                    'language',
                    'level',
                    'tags',
                    'pictureId'
                ], 
            ],
            'attribute' => [
                'method' => 'post',
            ],
            'fieldsets' => [
                [
                    'spec' => [
                        'type' => 'ListquestFieldset',                                   
                    ],
                    'flags' => [
                        'name' => 'listquest',
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
                            'value' => _('Save and add questions'),
                            'id' => 'submitbutton',
                            'class' => 'btn btn-primary',
                        ], 
                    ],
                ],
            ],
        ]);

        //input filter initialization
        $pictureInputFilter   = $sl->get('listquest_picture_inputfilter'); 
        $form->getInputFilter()->get('listquest')->add($pictureInputFilter);
        
        
        $form->get('listquest')->remove('questions');
        return $form;
    }
}