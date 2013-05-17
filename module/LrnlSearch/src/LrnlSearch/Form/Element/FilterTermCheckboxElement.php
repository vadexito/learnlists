<?php

namespace LrnlSearch\Form\Element;

use Zend\Form\Element\Checkbox;


class FilterTermCheckboxElement extends Checkbox
{
    protected $_queryUrl = NULL;
   
    
    public function __construct($name = null,$options = null)
    {
        parent::__construct($name,$options); 
        
        
        $this->setAttributes([
                'data-filter-type' => 'checkbox',                    
                'class' => 'checkbox-filter'
            ])
            ->setOptions([
                'use_hidden_element' => true,
                'checked_value' => 'checked',
                'unchecked_value' => 'unchecked'
            ]);
    }
    
    public function setQueryUrl($queryData,$parent)
    {
        $filteredQueryforUrl = clone $queryData; 
        $filterValueInCurrentUrl = $queryData->get($parent);
        $filter = $parent;
        $value = $this->getName();

        if ($filterValueInCurrentUrl === NULL){ //checkbox not checked, no other box checked
            $filteredQueryforUrl->set($filter,[$value]);
        } else {
            //convert string into array
            if (!is_array($filterValueInCurrentUrl)){
                $filteredQueryforUrl->set($filter,[$filterValueInCurrentUrl]);
            }
            //find if value is already in crossed checkbox            
            $keyValue = array_search($value,$filterValueInCurrentUrl);
            if ($filterValueInCurrentUrl && is_int($keyValue)){
                unset($filterValueInCurrentUrl[$keyValue]); //remove value if already ckecked
            } else {          // checkbox not checked and other crossed values, we add the filter
                $filterValueInCurrentUrl[] = $value;                        
            }
            $filteredQueryforUrl->set($filter,$filterValueInCurrentUrl);
        }
        
        $this->_queryUrl = $filteredQueryforUrl->toArray();
    }
    
    public function getQueryUrl()
    {
        return $this->_queryUrl;
    }
    
    
    
    
    
}