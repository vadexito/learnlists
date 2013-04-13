<?php
namespace Question\Form;

use Question\Entity\Tag;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;


class TagFieldset extends Fieldset implements InputFilterProviderInterface
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('tag');
        $this->setObjectManager($om);        
        $this->setHydrator(new DoctrineHydrator($om, 'Question\Entity\Tag'))
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
                'name' => 'tag',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 20,
                        ],
                    ],
                ],
            ],
        ];
    }
}