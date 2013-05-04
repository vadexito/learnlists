<?php
namespace LrnlUser\Form\Element;

use Zend\Form\Element\Text;

class FullName extends Text
{
    public function __construct($name = 'full_name')
    {
        parent::__construct($name);
    
        $this->setOptions([
            'label' => _('Full Name'),
        ]);
        
        $this->setAttributes([
            'id'    => 'full_name',
        ]);
    }
    
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => false,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
            ],
            'validators' => [
                [
                    'name'      => 'StringLength',
                    'options'   => [
                        'min'   => 2,
                        'max'   => 50,
                    ],
                ],
            ],
        ];
    }
}