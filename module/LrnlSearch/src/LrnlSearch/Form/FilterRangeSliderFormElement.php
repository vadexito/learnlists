<?php

namespace LrnlSearch\Form;

use Zend\Form\Element;

class FilterRangeSliderFormElement extends Element
{
    protected $_filters;
    protected $_nameMin;
    protected $_nameMax;
    protected $_queryUrl;
    
    public function __construct($name = NULL)
    {
        parent::__construct($name); 
        
        $this->_nameMin = $name.'Min';
        $this->_nameMax = $name.'Max';
        
        $this->setAttributes([
                'id'    => $name,
                'type'  => 'text',    
                'class' => 'span9',
                'data-filterType' => 'range',
                'data-filterNameMin' => $this->_nameMin,
                'data-filterNameMax' => $this->_nameMax,
                'data-slider-selection' => 'after',
                'data-slider-tooltip' => 'hide',
        ]);
            
    }
    
    public function setQueryUrl($queryData,$parent)
    {
        $nameMin = $this->_nameMin;
        $nameMax = $this->_nameMax;
        
        $queryForUrl = clone $queryData;
        if ($queryData->get($nameMin) !== NULL && $queryData->get($nameMax)){
            //populate the form
            $this->setAttribute('data-slider-value','['.$queryData->get($nameMin)
                .','.$queryData->get($nameMax).']');
        } else {
            $range = $this->getAttribute('data-slide-value'); 
            //to data parse value
            $queryForUrl->set($nameMin,$this->getAttribute('data-slider-min'));
            $queryForUrl->set($nameMax,$this->getAttribute('data-slider-max'));
        }
        
        $this->_queryUrl = $queryForUrl->toArray();
    }
    
    
    public function getQueryUrl()
    {
        return $this->_queryUrl;
    }
}