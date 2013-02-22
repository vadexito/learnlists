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
                'label' => _('Title')
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
                'label' => _('Level')
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
                'label' => _('Rules')
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
                'label' => _('Tags')
            ],
        ]);
        
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