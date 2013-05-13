<?php
namespace LrnlListquests\Form\Element;

use Zend\Form\Element\Select;

class Category extends Select
{
    public function __construct($name = 'category')
    {
        parent::__construct($name);
        
        $this->setAttributes([
            'id'    => 'category',
            'class' => 'chzn-select',
        ]);
    }
}