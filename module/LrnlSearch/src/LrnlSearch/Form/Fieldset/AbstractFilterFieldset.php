<?php

namespace LrnlSearch\Form\Fieldset;
use LrnlSearch\Provider\ProvidesSearchService;

use Zend\Form\Fieldset;

abstract class AbstractFilterFieldset extends Fieldset
{
    use ProvidesSearchService;
    
    protected $_filterType;
    
    public function setFilterType($filterType)
    {
        $this->_filterType = $filterType;
        return $this;
    }
    
    public function getFilterType()
    {
        return $this->_filterType;
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (isset($options['filterType'])) {
            $this->setFilterType($options['filterType']);
        }

        return $this;
    }
}