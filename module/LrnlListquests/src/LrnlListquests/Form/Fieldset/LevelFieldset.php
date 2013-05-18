<?php
namespace LrnlListquests\Form\Fieldset;

use LrnlListquests\Form\Fieldset\AbstractEntityManagerAwareFieldset;

class LevelFieldset extends AbstractEntityManagerAwareFieldset
{
    protected $_entityClass = 'LrnlListquests\Entity\Level';
    
    public function __construct($name = 'level',$options = NULL)
    {
        parent::__construct($name,$options);
        
    }
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'id',
            'type' => 'Level',
            'options' => [
                'label' => _('Level')
            ]
        ]);
    }
}