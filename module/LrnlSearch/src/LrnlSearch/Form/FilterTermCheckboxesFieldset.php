<?php

namespace LrnlSearch\Form;

use Zend\Form\Fieldset;

class FilterTermCheckboxesFieldset extends Fieldset
{
    public function populateValues($data)
    {
        $newData = [];
        foreach ($data as $term){
            $newData[$term] = 'checked';
        }
        parent::populateValues($newData);
    }
}