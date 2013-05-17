<?php
namespace LrnlListquests\Form\Element;

use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;

class Category extends ObjectSelect
{
    use ProvidesObjectManager;
    
    public function __construct($name = 'category',$options = null)
    {
        parent::__construct($name,$options);
        
        $this->setAttributes([
            'id'    => 'category',
            'class' => 'chzn-select',
        ]);
    }
    
    public function init()
    {
        $this->setOptions([
            'object_manager' => $this->getObjectManager(),
            'target_class'   => 'LrnlListquests\Entity\Category',
            'property'       => 'name',
        ]);
    }
}