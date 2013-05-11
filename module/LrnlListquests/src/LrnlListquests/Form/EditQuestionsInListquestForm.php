<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;

class EditQuestionsInListquestForm extends Form
{
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
}