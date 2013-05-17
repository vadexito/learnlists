<?php

namespace LrnlSearch\Form\Fieldset;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Form\Element\FilterTermCheckboxElement as Checkbox;



class FilterTermFacetFromSearchFieldset extends AbstractFilterFieldset
{
    use ProvidesListquestService;
    
    /**
     *values for the checkboxes in the facet field
     * 
     * @var array 
     */
    protected $_values;    
    
    
    public function populateValues($data)
    {
        $newData = [];
        foreach ($data as $term){
            $newData[$term] = 'checked';
        }
        parent::populateValues($newData);
    }
    
    public function initFacet($queryData)
    {
        $facetValues = $this->getSearchService()->getFacet(
            $this->getName(),
            $queryData,
            $this->getValues()
        );
        
        foreach ($facetValues as $key => $facetValue){
            
            $term = $facetValue['term'];
            $filterElement = new Checkbox($this->getName().'_'.$key);             
            $filterElement->setLabel((string)$term);
            $filterElement->setAttribute('data-hitNb',$facetValue['count']);
            $this->add($filterElement);
        }
        
        return $facetValues;
    }
    
    public function setValues(array $values)
    {
        $this->_values = $values;
        return $this;
    }
    
    public function getValues()
    {
        return $this->_values;
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (isset($options['values'])) {
            $this->setValues($options['values']);
        }

        return $this;
        
    }
}