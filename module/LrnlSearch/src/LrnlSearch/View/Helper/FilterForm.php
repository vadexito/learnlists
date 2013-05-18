<?php 

namespace LrnlSearch\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlSearch\Form\FiltersForm;

class FilterForm extends AbstractHelper
{
    protected $_form;
    
    public function __invoke(FiltersForm $filterForm)
    {
        $this->_form = $filterForm;
        
        $urlQueries = $filterForm->get('filters')->getAttribute('data-urls');
        $urls = [];
        foreach ($urlQueries as $urlquery){
            $urls[] = $this->getView()->url('lrnl-search',[],['query' => $urlquery->toArray()]);
        }
        $filterForm->get('filters')->setAttribute('data-urls',\Zend\Json\Json::encode($urls));
      
        $render = '';
        foreach ($filterForm->getFieldsets() as $filter){
            $render .=  $this->getView()->partial(
                $this->_attributePartial($filter->getFilterType()),
                ['filter' => $filter]
            );
        }
        
        return $render;
        
    }
    
    protected function _attributePartial($type)
    {
        $dir = 'lrnl-search/search/searchfilters/';
        
        switch ($type){
            case FiltersForm::$CHECKBOX_FACET_SEARCH :                       
            case FiltersForm::$CHECKBOX_FACET_SELECT :                       
                $template = 'filterCheckboxes';
                break;
            case FiltersForm::$RANGE :
                $template = 'filterRange';
                break;
            case FiltersForm::$SEARCH :
                $template = 'filterSearch';
                break;
        }
        
        return $dir.$template.'.phtml';
    }
}