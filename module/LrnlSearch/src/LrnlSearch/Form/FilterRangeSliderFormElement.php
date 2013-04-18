<?php

namespace LrnlSearch\Form;

use Zend\Form\Element;

class FilterRangeSliderFormElement extends Element
{
    protected $_filters;
    protected $_queryUrl;
    
    public function __construct($name = NULL)
    {
        parent::__construct($name); 
        
        $this->setAttributes([
                'id'    => $name,
                'type'  => 'text',    
                'class' => 'span9',
                'data-filterType' => 'range',
                'data-slider-selection' => 'after',
                'data-slider-tooltip' => 'hide',
        ]);
            
    }
    
    public function setQueryUrl($queryData,$parent)
    {
        $queryForUrl = clone $queryData;
        $range = $queryData->get($this->getName());
        if ($range !== NULL){
            //populate the form
            $this->setAttribute('data-slider-value','['.$range['min']
                .','.$range['max'].']');
        } else {
            $queryForUrl->set($this->getName(),[
                'min' => $this->getAttribute('data-slider-min'),
                'max' => $this->getAttribute('data-slider-max')
            ]);
        }
        
        $this->_queryUrl = $queryForUrl->toArray();
    }
    
    
    public function getQueryUrl()
    {
        return $this->_queryUrl;
    }
}