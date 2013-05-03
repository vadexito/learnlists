<?php

namespace LrnlSearch\Form;
use LrnlSearch\Service\SearchServiceInterface;
use LrnlSearch\Provider\ProvidesSearchService;
use LrnlSearch\Form\FilterTermCheckboxElement as Checkbox;

use Zend\Form\Fieldset;


class FilterTermFacetFieldset extends Fieldset
{
    use ProvidesSearchService;
    
    protected $_defaultValues;
    
    public function __construct($name = NULL,SearchServiceInterface $searchService)
    {
        parent::__construct($name);
        $this->setSearchService($searchService);
    }
    
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
            $this->getDefaultValues()
        );
        
        foreach ($facetValues as $facetValue){
            $filterElement = new Checkbox($facetValue['term']);
            $filterElement->setAttribute('data-hitNb',$facetValue['count']);
            $this->add($filterElement);
        }
    }
    
    public function setDefaultValues(array $values)
    {
        $this->_defaultValues = $values;
        return $this;
    }
    
    public function getDefaultValues()
    {
        return $this->_defaultValues;
    }
}