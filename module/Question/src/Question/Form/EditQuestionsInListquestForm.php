<?php

namespace Question\Form;

use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Stdlib\Hydrator\Strategy\DisallowRemoveByValue;


class EditQuestionsInListquestForm extends Form
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('EditListquestForm');
        $this->setObjectManager($om);        
        $this->setAttribute('method', 'post');
        
        $doctrineHydrator = new DoctrineHydrator(
                $this->getObjectManager(),
                'Question\Entity\Listquest'
        );
        $this->setHydrator($doctrineHydrator);
             
        
        $listquestFieldset = new ListquestFieldset($this->getObjectManager());
        $listquestFieldset->setUseAsBaseFieldset(true);
        $listquestFieldset->remove('title');
        $listquestFieldset->remove('level');
        $listquestFieldset->remove('rules');
        $listquestFieldset->remove('tags');
        $this->add($listquestFieldset);
        
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Check',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
        
        $this->setValidationGroup([
            'listquest' => [
                'questions'//=> ['question'],
            ],
        ]);
    }
}