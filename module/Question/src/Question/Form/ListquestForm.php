<?php

namespace Question\Form;

use Zend\Form\Form;
use Question\Form\ListquestFieldset;
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
                     'Question\Entity\Listquest'
             ))
             ->setAttribute('method', 'post');
        
        $listquestFieldset = new ListquestFieldset($this->getObjectManager());
        $listquestFieldset->setUseAsBaseFieldset(true);
        $listquestFieldset->remove('questions');
        $this->add($listquestFieldset);
        
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
            'listquest' => [
                'title',
                'tags',
                'level',
                'rules'
            ],
        ]);
    }
    
    
}