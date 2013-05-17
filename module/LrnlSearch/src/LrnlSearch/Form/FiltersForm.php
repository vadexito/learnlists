<?php

namespace LrnlSearch\Form;

use Zend\Form\Form;
use LrnlSearch\Service\SearchServiceInterface;
use LrnlListquests\Service\ListquestService;
use LrnlSearch\Form\Fieldset\FilterTermFacetFromSelectFieldset as SelectFieldset;

class FiltersForm extends Form
{
    public static $CHECKBOX_FACET_SEARCH  = 'checkbox_search';
    public static $CHECKBOX_FACET_SELECT  = 'checkbox_select';
    public static $RANGE = 'range';
    public static $SEARCH  = 'search';
    
    public function __construct(ListquestService $listquestService,
        SearchServiceInterface $searchService,
        array $filterElements = [],
        $name = null,$options = null)
    {
        parent::__construct($name,$options); 
        
        $optionsSelect = [];
        foreach ($filterElements as $elementConfig){            
            $this->add($elementConfig);
            $element = $this->get($elementConfig['name']);
            $element->setSearchService($searchService);
            if ($element instanceof SelectFieldset){
                $element->setListquestService($listquestService);
            }
            $optionsSelect[$name] = $name;
        }
        
//        foreach ($filterConfig as $name => $options){
//            $optionsSelect[$name] = $name;
//            switch ($options['type']){
//                case self::$CHECKBOX_FACET_SEARCH :
//                    $filter = new FilterTermFacetFieldset($name,$searchService);
//                    
//                    $filter->setAttribute('data-filterType',self::$CHECKBOX);
//                    $filter->setLabel($options['label']);
//                    $filter->setDefaultValues($options['values']);
//                    break;
//                case self::$CHECKBOX_FACET_DATABASE :
//                    $filter = new FilterTermFacetFieldset($name,$searchService);
//                    $filter->setAttribute('data-filterType',self::$CHECKBOX);
//                    $filter->setLabel($options['label']);
//                    $filter->setDefaultValues($options['values']);
//                    break;
//                case self::$RANGE :
//                    $filter = new Fieldset($name);
//                    $filter->setAttribute('data-filterType',self::$RANGE);
//                    $filterElement = new Slider($name);
//                    $filterElement->setLabel($options['label'])
//                                  ->setAttributes($options['attributes']);
//                    $filter->add($filterElement);
//                    break;
//                case self::$SEARCH :
//                    $filter = new Fieldset($name);
//                    $filter->setAttribute('data-filterType',self::$SEARCH);
//                    $filterElement = new Search($name);
//                    $filterElement->setLabel($options['label'])
//                                  ->setAttributes($options['attributes']);
//                    $filter->add($filterElement);
//                    break;
//                default : 
//            }
//            $this->add($filter);
//        }
        
        $this->add([
            'name' => 'filters',
            'type' => 'select',
            'attributes' => [
                'multiple' => 'true',
                'id' => 'filters',
                'class' => 'chzn-select',
                'style' => 'width:100%'
            ],
            'options' => [
                'value_options' => $optionsSelect,
            ],
        ]);
    }
    
    public function initFilters($queryData)
    {
        
        foreach ($this->getFieldsets() as $fieldset){
            if (method_exists($fieldset , 'initFacet')){
                $fieldset->initFacet(
                        $queryData
                );
            } 
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