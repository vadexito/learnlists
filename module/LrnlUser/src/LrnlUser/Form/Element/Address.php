<?php
namespace LrnlUser\Form\Element;

use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;

class Address extends Text implements InputProviderInterface
{
    public function __construct($name = 'address')
    {
        parent::__construct($name);
    
        $this->setOptions([
            'label' => _('Address'),
        ]);
        
        $this->setAttributes([
            'id'    => 'address',
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
                        'min'   => 3,
                        'max'   => 255,
                    ],
                ],
            ],
        ];
    }
}