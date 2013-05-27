<?php

namespace LrnlListquests\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListquestChangePictureFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        
        $form = $services->get('listquest-create-form');
        $form->remove('listquest');
        $listquestFieldset = $services->get('ListquestFieldset');
        $listquestFieldset->remove('title')->remove('language');
        $listquestFieldset->remove('level')->remove('tags');
        $listquestFieldset->remove('category')->remove('questions');
        $form->add($listquestFieldset);
        
        //input filter initialization
        $sl = $services->getServiceLocator();
        $pictureInputFilter   = $sl->get('listquest_picture_inputfilter');      
        $form->getInputFilter()->get('listquest')->add($pictureInputFilter);
        
        
        $form->setValidationGroup([
            'csrf',
            'listquest' => [
                'pictureId'
            ]
        ]);

        return $form;
    }
}