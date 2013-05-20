<?php

namespace LrnlSearch\Form\Fieldset;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Form\Element\FilterTermCheckboxElement as Checkbox;

class FilterTermFacetFromSelectFieldset extends AbstractFilterFieldset
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
        if ($this->getSearchService()->isEmptyQuery($queryData)){
            $idList = 'all';
        } else {
            $idList = [];
            $hits = $this->getSearchService()->getResultsFromQuery($queryData);
            foreach ($hits as $hit){
               $idList[] = (int)$hit->listId; 
            }
        }

        $facetValues = $this->getListquestService()->getFacet(
            $this->getName(),
            $idList ,
            $this->getValues()   
        );
        
        foreach ($facetValues as $facetValue){ 
            $term = $facetValue['term'];
            if ($term){                
                $filterElement = new Checkbox($term);
                $filterElement->setLabel((string)$term);
                $filterElement->setAttribute('data-hitNb',$facetValue['count']);
                $this->add($filterElement);
            }  
        }
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