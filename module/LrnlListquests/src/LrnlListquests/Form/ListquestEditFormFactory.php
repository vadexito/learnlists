<?php

namespace LrnlListquests\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListquestEditFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $form = $services->get('listquest-create-form');
        
        $form->remove('listquest');
        $listquestFieldset = $services->get('ListquestFieldset');
        $listquestFieldset->remove('pictureId')->remove('tags');
        $form->add($listquestFieldset);
        
        $form->setValidationGroup([
            'csrf',
            'listquest' => [
                'title',
                'description',
                'category',
                'level',
                'questions',
                'language'
            ]
        ]);
        return $form;
    }
}