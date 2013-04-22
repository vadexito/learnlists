<?php

namespace LrnlSearch\Form;

use Zend\Form\Form;
use LrnlListquests\Form\Element\Category;

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
                'placeholder' => _('Which quizz are you looking for?')
            ],
        ]);
        
        $category = new Category('category');
        $category->setAttribute(
                'class',
                $category->getAttribute('class').' '.'chzn-select'
        );
        $this->add($category);        
        
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