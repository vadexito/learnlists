<?php
namespace Question\Form;

use Question\Entity\Tag;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;


class TagFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        
        parent::__construct('tag');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new Tag());
    
    
        $this->add([
            'name' => 'tag',
            'options' => [
                'label' => _('Tag'),
            ],                    
            'attributes' => [
                'required' => 'required',
                'type'  => 'text',
                'id' => 'tag',
                'autocomplete'    => 'off',
            ],
        ]);    
    }

    /**
     * @return array
     */    
    public function getInputFilterSpecification()    
    {
        return [
            'tag' => [
                'required' => true,
            ],
        ];
    }
}