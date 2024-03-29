<?php

namespace LrnlSearch\Form;

use Zend\Form\Form;

class SearchForm extends Form
{
    public function __construct()
    {
        parent::__construct('searchForm');
        
        $this->setAttributes([
            'class' => 'form-inline',
            'method' => 'get'
        ]);
        
        $this->add([
            'name' => 'search',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'search',
                'autocomplete'    => 'off',
                'class' => 'input-xxlarge',
                'placeholder' => _('What would you like to learn?')
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => _('Search'),
                'id' => 'submitbutton',
                'class' => 'btn btn-large btn-primary',
            ],            
        ]);
    }
    
    public function init()
    {
        $this->add([
            'name' => 'category',
            'type' => 'Category'
        ]);
    }
}