<?php

namespace LrnlListquests\Form\Element;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('lrnllistquests_module_options');
        $service = new Category('category');        
        $categories = $options->getCategories()['list'];        
        $service->setOptions([
                'value_options' => array_merge(['' => _('Categories')],$categories),
        ]);
        return $service;
    }
}