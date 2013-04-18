<?php

namespace LrnlSearch\Form;

use Zend\Form\Element;

class FilterSearchFormElement extends Element
{
    protected $_filters;
    protected $_queryUrl;
    
    public function setQueryUrl($queryData,$parent)
    {
        $filteredQueryforUrl = clone $queryData; 
        $filteredQueryforUrl->set($this->getName(),[$parent => 'var']);     
        $this->_queryUrl = $filteredQueryforUrl->toArray();
    }
    
    
    public function getQueryUrl()
    {
        return $this->_queryUrl;
    }
}