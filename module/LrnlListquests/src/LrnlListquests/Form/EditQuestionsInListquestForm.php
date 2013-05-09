<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use LrnlListquests\Entity\Listquest;
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
                get_class(new Listquest)
        );
        $this->setHydrator($doctrineHydrator);
        
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
                'questions'//=> ['question'],
            ],
        ]);
    }
}