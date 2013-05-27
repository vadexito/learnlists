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
        //check if the filter is already in the query so that the number of
        // the facet gives the result for this value OR for an another value
        // in the same filter
        $filteredQuery = clone $queryData;
        if($queryData->get($this->getName())){                
            $filteredQuery->offsetUnset($this->getName());
        }
        if ($this->getSearchService()->isEmptyQuery($filteredQuery)){
            $idList = 'all';
        } else {
            $idList = [];
            $hits = $this->getSearchService()->getResultsFromQuery($filteredQuery);
            
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
            $term = (string)$facetValue['term'];
            $name = $term;
            if (isset($facetValue['termId'])){
                $termId = (string)$facetValue['termId'];
                $name = $termId.'-'.$term;
            }
            if ($term){                
                $filterElement = new Checkbox($name);
                $filterElement->setLabel($term);
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