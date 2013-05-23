<?php

namespace LrnlCategory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryEditFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $form = $services->get('category-create-form');
        $form->setValidationGroup([
            'csrf',
            'category' => [
                'description', 
                'depth',
                'parent'
            ],
        ]);
        
        $form->get('category')->remove('name');

        return $form;
    }
}