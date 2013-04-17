<?php

namespace LrnlSearch\Form;

use Zend\Form\Fieldset;

class FilterTermCheckboxesFieldset extends Fieldset
{
    public function __construct($name = NULL)
    {
        parent::__construct($name); 
        
        $this->setAttributes([
            'data-filterType' => 'checkboxes',
        ]);
    }
}