<?php
namespace LrnlListquests\Form\Element;

use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;

class Language extends ObjectSelect
{
    use ProvidesObjectManager;
    
    public function __construct($name = 'language',$options = null)
    {
        parent::__construct($name,$options);
        
        $this->setAttributes([
            'id'    => 'language',
            'class' => 'chzn-select',
        ]);
    }
    
    public function init()
    {
        $this->setOptions([
            'object_manager' => $this->getObjectManager(),
            'target_class'   => 'LrnlListquests\Entity\Language',
            'property'       => 'name',
        ]);
    }
}