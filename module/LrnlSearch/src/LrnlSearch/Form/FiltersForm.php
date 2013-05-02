<?php

namespace LrnlSearch\Form;

use Zend\Form\Form;
use LrnlSearch\Form\FilterTermCheckboxesFieldset;
use LrnlSearch\Form\FilterRangeSliderFormElement as Slider;
use LrnlSearch\Form\FilterTermCheckboxElement as Checkbox;
use LrnlSearch\Form\FilterSearchFormElement as Search;
use LrnlSearch\Service\SearchServiceInterface;
use Traversable;

use Zend\Form\Element\Select;
use Zend\Form\Fieldset;

class FiltersForm extends Form
{
    public static $CHECKBOX  = 'checkbox';
    public static $RANGE = 'range';
    public static $SEARCH  = 'search';
    
    public function __construct($name = NULL,
            SearchServiceInterface $searchService,Traversable $filterConfig = NULL)
    {
        parent::__construct($name);        
        $select =  new Select('filters');
        $select->setAttribute('multiple','true');
        $select->setAttribute('id','filters');
        $select->setAttribute('class','chzn-select');
        $select->setAttribute('style','width:100%');
        
        $optionsSelect = [];
        foreach ($filterConfig as $name => $options){
            $optionsSelect[$name] = $name;
            switch ($options['type']){
                case self::$CHECKBOX :
                    $filter = new FilterTermCheckboxesFieldset($name);
                    $filter->setAttribute('data-filterType',self::$CHECKBOX);
                    $filter->setLabel($options['label']);
                    foreach ($options['values'] as $value){
                        $filterElement = new Checkbox($value,$searchService);
                        $filter->add($filterElement);
                    }
                    break;
                case self::$RANGE :
                    $filter = new Fieldset($name);
                    $filter->setAttribute('data-filterType',self::$RANGE);
                    $filterElement = new Slider($name);
                    $filterElement->setLabel($options['label'])
                                  ->setAttributes($options['attributes']);
                    $filter->add($filterElement);
                    break;
                case self::$SEARCH :
                    $filter = new Fieldset($name);
                    $filter->setAttribute('data-filterType',self::$SEARCH);
                    $filterElement = new Search($name);
                    $filterElement->setLabel($options['label'])
                                  ->setAttributes($options['attributes']);
                    $filter->add($filterElement);
                    break;
                default : 
            }
            $this->add($filter);
        }
        
        $select->setOptions([
                'value_options' => $optionsSelect,
        ]);
        $this->add($select);
        
    }
    
    public function initUrlInFilters($queryData)
    {
        foreach ($this->getFieldsets() as $fieldset){            
            foreach ($fieldset->getElements() as $element){
                if (method_exists($element, 'setQueryUrl')){
                    $element->setQueryUrl(
                            $queryData,
                            $fieldset->getName()
                    );
                }
             }
        }
        
        //init filters select element
        $select = $this->get('filters');
        $queryUrls = [];
        foreach ($select->getOption('value_options') as $filter => $value){
            $query = clone $queryData;
            if (isset($query[$filter])) {
                unset($query[$filter]);
            }
            $queryUrls[] = $query;
        }
        $select->setAttribute('data-urls',$queryUrls);
    }
    
    public function populateValues($data)
    {       
        parent::populateValues(array_merge(
                $data,
                ['filters' => array_keys($data)]
        ));
    }
}