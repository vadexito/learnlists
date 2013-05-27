<?php

namespace LrnlListquests\Form\Fieldset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Factory as FormFactory;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


class TagFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $sl = $services->getServiceLocator();
        $om = $sl->get('Doctrine\ORM\EntityManager');
        $entityClass = 'LrnlListquests\Entity\Tag';

        $factory = new FormFactory($services);
        $fieldset = $factory->createFieldset([
            'type' => 'Zend\Form\Fieldset',
            'name' => 'tag',                    
            'hydrator' => new DoctrineHydrator($om,$entityClass),
            'object' => $entityClass,
            'elements' => [
                [
                    'flags' => [
                        'name' => 'tag',
                    ],
                    'spec' => [
                        'type' => 'text',
                        'attributes' => [ 
                            'id'    => 'tag',
                            'autocomplete'    => 'off',
                            'required' => 'required'
                         ],
                        'options' => [
                            //'label' => _('Tag')
                        ],
                    ],
                ],
            ],
        ]);
        
        
        return $fieldset;
    }
}