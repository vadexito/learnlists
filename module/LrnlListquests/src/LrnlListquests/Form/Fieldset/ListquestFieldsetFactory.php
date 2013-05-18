<?php

namespace LrnlListquests\Form\Fieldset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ListquestFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $listquestFieldset = $services->get('LrnlListquests\Form\Fieldset\ListquestFieldset'); 
        return $listquestFieldset;
    }
}