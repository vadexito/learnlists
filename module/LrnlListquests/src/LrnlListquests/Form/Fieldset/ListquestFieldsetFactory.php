<?php

namespace LrnlListquests\Form\Fieldset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LrnlListquests\Form\Fieldset\ListquestFieldset;


class ListquestFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $listquestFieldset = new ListquestFieldset(); 
        return $listquestFieldset;
    }
}