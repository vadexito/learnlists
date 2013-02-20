<?php

namespace Question\Form;

use Zend\Form\Form;

class QuestionForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('questionForm');
        
        $this->setAttribute('method', 'post');
        
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
            ],
        ]);
        
        $this->add([
            'name'      => 'listId',
            'attributes'=> [
                'type'  => 'hidden',
            ],
        ]);
        
        $this->add([
            'name' => 'text',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'text',
                'autocomplete'    => 'off',
                'class' => 'input-xxlarge',
            ],
            'options' => [
                'label' => 'Text (<a href="#" data-toggle="tooltip" data-placement="right" 
                    title="Enter the text for the question. 
                    If this is a sentence to fill in, please use two % signs in order to specify answers. 
                    Example : He %was% not there %either%.">?</a>)'
            ],
        ]);
        
        $this->add([
            'name' => 'answer',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'answer',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Answer (<a href="#" data-toggle="tooltip" data-placement="right" 
                    title="Nothing to enter if the answer was already entered directly in the text (using % signs).">?</a>)'
            ],
        ]);
        
        $this->add([
            'name' => 'tip',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'tip',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Tip'
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Check',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}