<?php
namespace LrnlListquests\Form\Fieldset;

use LrnlListquests\Form\Fieldset\AbstractEntityManagerAwareFieldset;

class LanguageFieldset extends AbstractEntityManagerAwareFieldset
{
    protected $_entityClass = 'LrnlListquests\Entity\Language';
    
    public function __construct($name = 'language',$options = null)
    {
        parent::__construct($name,$options);
    }
    
    public function init()
    {
        parent::init();
        
        $this->add([
            'name' => 'id',
            'type' => 'Language',
            'options' => [
                'label' => _('Language')
            ]
        ]);
    }
}