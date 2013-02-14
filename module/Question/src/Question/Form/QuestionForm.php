<?php

namespace Question\Form;

use Zend\Form\Form;

class QuestionForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('questionForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class','form-inline');
        
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
                'id'    => 'listId',
            ],
        ]);
        $this->add([
            'name' => 'answer',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'answer',
                'placeholder'    => 'Answer',
                'autocomplete'    => 'off',
            ],
            'options' => [
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Check',
                'id' => 'submitbutton',
                'class' => 'btn btn-success button-answer',
            ],
        ]);
    }
}