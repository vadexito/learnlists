<?php 

namespace LrnlSearch\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Form;
use DluTwBootstrap\Form\FormUtil;

class ListSearchBar extends AbstractHelper
{
    protected $_form;
    
    public function __invoke(Form $searchForm)
    {
        $this->_form = $searchForm;
        $searchForm->setAttribute('action', $this->getView()->url('lrnl-search'));
        $searchForm->prepare();
       
        $category = $searchForm->get('category');
        $initialOptions = $category->getValueOptions();
        $valueOptions = [];
        foreach ($initialOptions as $value){
            $valueOptions[] = [
                'label' => $value['label'],
                'value' => $value['label'],
            ];
        }
        $title = [
            'label' => '',
            'value' => ''
        ];        
        array_unshift($valueOptions,$title);
        $category->setValueOptions($valueOptions)
                 ->setAttribute('data-placeholder', $this->getView()->translate('Categories'))
                 ->setAttribute('data-noresultstext', $this->getView()->translate('No results for '));
        
        return $this;
        
    }
    
    public function __toString()
    {
        $searchForm = $this->_form;
        $render = '';
        foreach ($searchForm as $element){
            if ($element->getName() != 'submit') {
                $render .= $this->getView()->formRowTwb($element, FormUtil::FORM_TYPE_INLINE);
            }
        }
        $render .= '<button type="submit" class="btn btn-large btn-primary">
            <i class="icon-white icon-search"></i> '
            .$this->getView()->translate('Search')
            .'</button>';
        
        $openTag = $this->getView()->formTwb()->openTag($searchForm);
        $closeTag = $this->getView()->formTwb()->closeTag($searchForm);
        
        return $openTag.$render.$closeTag;
    }
}