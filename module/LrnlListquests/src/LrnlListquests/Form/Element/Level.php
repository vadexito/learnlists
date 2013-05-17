<?php
namespace LrnlListquests\Form\Element;

use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;

class Level extends ObjectSelect
{
    use ProvidesObjectManager;
    
    public function __construct($name = 'level',$options = null)
    {
        parent::__construct($name,$options);
        
        $this->setAttributes([
            'id'    => 'level',
            'class' => 'chzn-select',
        ]);
    }
    
    public function init()
    {
        $this->setOptions([
            'object_manager' => $this->getObjectManager(),
            'target_class'   => 'LrnlListquests\Entity\Level',
            'property'       => 'name',
        ]);
    }
}