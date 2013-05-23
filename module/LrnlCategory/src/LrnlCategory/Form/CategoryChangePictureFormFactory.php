<?php

namespace LrnlCategory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LrnlCategory\InputFilter\PictureInputFilter;
use Zend\InputFilter\InputFilter;

class CategoryChangePictureFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $form = $services->get('category-create-form');
        $form->remove('category');
        $categoryFieldset = $services->get('LrnlCategoryFieldset');
        $categoryFieldset->remove('name')->remove('description');
        $categoryFieldset->remove('depth')->remove('parent');
        $form->add($categoryFieldset);
        
        //input filter initialization
        $sl = $services->getServiceLocator();
        $options = $sl->get('lrnlcategory_module_options');
        $targetUpload = $options->getTmpPictureUploadDir();        
        $pictureInputFilter   = new PictureInputFilter('pictureId',$targetUpload);        
        $categoryInputFilter = (new InputFilter())->add($pictureInputFilter);        
        $form->getInputFilter()->add($categoryInputFilter,'category');
        
        
        $form->setValidationGroup([
            'csrf',
            'category' => [
                'pictureId'
            ]
        ]);

        return $form;
    }
}