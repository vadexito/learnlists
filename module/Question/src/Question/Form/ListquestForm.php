<?php

namespace Question\Form;

use Zend\Form\Form;

class ListquestForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('listquestForm');
        
        $this->setAttribute('method', 'post');
        
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
            ],
        ]);
        $this->add([
            'name' => 'title',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'title',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Title'
            ],
        ]);
        $this->add([
            'name' => 'level',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'level',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Level (<a href="#" data-toggle="tooltip" data-placement="right" 
                    title="Advanced, average, beginner for example">?</a>)'
            ],
        ]);
        
        $this->add([
            'name' => 'rules',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'rules',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Rules'
            ],
        ]);
        
        $this->add([
            'name' => 'tags',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'tags',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => 'Tags'
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