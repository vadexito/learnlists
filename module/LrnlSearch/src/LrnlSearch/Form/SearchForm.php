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
            'method' => 'post'
        ]);
        
        $this->add([
            'name' => 'search',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'search',
                'autocomplete'    => 'off',
                'class' => 'input-xxlarge',
                'placeholder' => _('Which quizz are you looking for?')
            ],
        ]);
        
        $this->add([
            'name' => 'language',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'language',
                'autocomplete'    => 'off',
                'class' => 'input-medium',
                'placeholder' => _('Language')
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
}