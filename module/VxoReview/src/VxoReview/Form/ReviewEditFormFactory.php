<?php

namespace VxoReview\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReviewEditFormFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $form = $services->get('review-create-form');
        $form->get('review')->remove('reviewedItem');
        $form->setValidationGroup([
            'csrf',
            'review' => [
                'id',
                'text', 
                'rating'
            ]
        ]);

        return $form;
    }
}