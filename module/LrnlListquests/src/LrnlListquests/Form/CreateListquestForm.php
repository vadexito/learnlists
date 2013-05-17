<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CreateListquestForm extends Form implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function __construct()
    {
        parent::__construct('listquestForm');
        $this->setAttribute('method', 'post');
        
        $this->add(new Csrf('csrf'));
        
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => _('Check'),
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],            
        ]);
        
        $this->setValidationGroup([
            'csrf',
            'listquest' => [
                'title',
                'description',
                'category',
                'language',
                'level',
                'tags',
                'rules',                
            ],            
            'picture' => ['pictureId'],
        ]);
    }
    
    public function init()
    {
        $formElementManager = $this->getServiceLocator();
        if (!$formElementManager){
            throw new ServiceNotFoundException('The form element manager was not initialized. Use the form element manager to initiate the fieldset');
        }
        
        $this->add([
            'type' => 'ListquestFieldset'
        ]);
        $listquestFieldset = $this->get('listquest');
        $this->setBaseFieldset($listquestFieldset);
        $listquestFieldset->remove('questions');
        
        //initialize hydrator
        $applicationServices = $formElementManager->getServiceLocator();        
        $om = $applicationServices->get('Doctrine\ORM\EntityManager');  
        $doctrineHydrator = new DoctrineHydrator($om); 
        $this->setHydrator($doctrineHydrator);
        
    }
          
    
    
}