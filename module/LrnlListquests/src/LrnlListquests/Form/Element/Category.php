<?php
namespace LrnlListquests\Form\Element;

use Zend\Form\Element\Select;

class Category extends Select
{
    public function __construct($name = 'category',Array $categories)
    {
        parent::__construct($name);
        
        $categories = array_merge(['' => _('Categories')],$categories);
        
        $this->setAttributes([
            'id'    => 'category',
            'class' => 'chzn-select',
        ]);
        
        $this->setOptions([
                'value_options' => $categories,
        ]);
    }
}