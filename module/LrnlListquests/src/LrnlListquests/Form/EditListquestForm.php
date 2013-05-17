<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class EditListquestForm extends Form implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    public function __construct($name = 'EditListquestForm',$options = NULL)
    {
        parent::__construct($name,$options);
        $this->setAttribute('method', 'post');
        
        $this->add([
            'name' => 'submit',
            'type'  => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Check',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
        
        $this->setValidationGroup([
            'listquest' => [
                'title',
                'description',
                'category',
                'level',
                'rules',
                'language',
                'questions',
            ],
        ]);
    }
    
    public function init()
    {
        $formElementManager = $this->getServiceLocator();
        
        $this->add([
            'name' => 'listquest',
            'type' => 'ListquestFieldset'
        ]);
        $listquestFieldset = $this->get('listquest');
        $this->setBaseFieldset($listquestFieldset);
        $listquestFieldset->remove('tags');
        
        //initialize hydrator
        $applicationServices = $formElementManager->getServiceLocator();        
        $om = $applicationServices->get('Doctrine\ORM\EntityManager');  
        $doctrineHydrator = new DoctrineHydrator($om); 
        $this->setHydrator($doctrineHydrator);
   
    }
}