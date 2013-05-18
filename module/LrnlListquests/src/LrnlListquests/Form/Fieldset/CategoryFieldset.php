<?php
namespace LrnlListquests\Form\Fieldset;

use LrnlListquests\Form\Fieldset\AbstractEntityManagerAwareFieldset;

class CategoryFieldset extends AbstractEntityManagerAwareFieldset
{
    protected $_entityClass = 'LrnlListquests\Entity\Category';
    
    public function __construct($name = 'category',$options = NULL)
    {
        parent::__construct($name,$options);
    }
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'id',
            'type' => 'Category',
            'options' => [
                'label' => _('Category')
            ]
        ]);
    }
}