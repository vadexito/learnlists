<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use LrnlListquests\Form\QuestionFieldset;
use LrnlListquests\Entity\Question;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;



class EditQuestionForm extends Form
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('questionForm');
        $this->setObjectManager($om);
        
        $this->setHydrator(new DoctrineHydrator(
                     $this->getObjectManager(),
                     get_class(new Question())
             ))
             ->setAttribute('method', 'post');
        
        $questionFieldset = new QuestionFieldset($this->getObjectManager());
        $questionFieldset->setUseAsBaseFieldset(true);
        $this->add($questionFieldset);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => _('Check'),
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],            
        ]);
    }
    
    
}