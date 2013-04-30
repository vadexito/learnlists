<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use LrnlListquests\Form\ListquestFieldset;
use LrnlListquests\Entity\Listquest;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;



class ListquestForm extends Form
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('listquestForm');
        $this->setObjectManager($om);
        
        $this->setHydrator(new DoctrineHydrator(
                     $this->getObjectManager(),
                     get_class(new Listquest())
             ))
             ->setAttribute('method', 'post');
        
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
                'tags',
                'level',
                'rules',                
            ],
        ]);
    }
    
    
}