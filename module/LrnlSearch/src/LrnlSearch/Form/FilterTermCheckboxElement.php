<?php

namespace LrnlSearch\Form;

use Zend\Form\Element\Checkbox;
use LrnlSearch\Provider\ProvidesSearchService;
use LrnlSearch\Service\SearchServiceInterface;

class FilterTermCheckboxElement extends Checkbox
{
    use ProvidesSearchService;
    
    protected $_queryUrl;
    
    public function __construct($name = NULL,SearchServiceInterface $searchService)
    {
        parent::__construct($name); 
        $this->setSearchService($searchService);
        
        $this->setAttributes([
                'id'    => $name,
                'data-filter-type' => 'checkbox',                    
                'class' => 'checkbox-filter'
            ])
            ->setOptions([
                'label' => $name,
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
        
        //calculate the hit number of each option
        $filteredQuery = clone $queryData; 
        $filteredQuery->set($filter,[$value]);                
        $hitNb = $this->getSearchService()->getCountNumberFromQuery($filteredQuery);
        $this->setAttribute('data-hitNb',$hitNb);
                
        
    }
    
    public function getQueryUrl()
    {
        return $this->_queryUrl;
    }
}