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
            ]);
        $this->setOptions([
                'value_options' => [
                    '' => _('All categories'),
                    //'English' => _('Foreign Languages - English'),
                    'German' => _('Foreign Languages - German'),
                    //'French' => _('Foreign Languages - French'),
                ],
        ]);
    }
}