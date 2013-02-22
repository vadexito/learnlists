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
                'label' => _('Text')
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
                'label' => _('Answer')
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
                'label' => _('Tip')
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