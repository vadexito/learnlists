<?php
namespace LrnlListquests\Form;

use LrnlListquests\Entity\Listquest;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Stdlib\Hydrator\Strategy\DisallowRemoveByValue;


class ListquestFieldset extends Fieldset implements InputFilterProviderInterface
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('listquest');
        $this->setObjectManager($om);        
        $this->setObject(new Listquest());
        
        $doctrineHydrator = new DoctrineHydrator(
                $this->getObjectManager(),
                get_class(new Listquest())
        );
        $this->setHydrator($doctrineHydrator);
    
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
            ],
            
        ]);
        $this->add([
            'name' => 'title',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'title',
                'autocomplete'    => 'off',
                'required' => 'required',
            ],
            'options' => [
                'label' => _('Title')
            ],
        ]);
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type'  => 'textarea',
                'id'    => 'description',
                'autocomplete'    => 'off'
            ],
            'options' => [
                'label' => _('Description')
            ],
        ]);
        $this->add([
            'name' => 'language',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'language',
                'autocomplete'    => 'off'
            ],
            'options' => [
                'label' => _('Language')
            ],
        ]);
        $this->add([
            'name' => 'level',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'level',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Level')
            ],
        ]);
        
        $this->add([
            'name' => 'rules',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'rules',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Rules')
            ],
        ]);
        
        $tagFieldset = new TagFieldset($this->getObjectManager());
        $this->add([     
            'name' => 'tags',
            'type' => 'Zend\Form\Element\Collection',            
            'options' => [
                'count' => 1,
                'template_placeholder' => '__index__',
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $tagFieldset,
            ],
        ]);
        
        $questionFieldset = new QuestionFieldset($this->getObjectManager());
        $this->add([
            'name' => 'questions',
            'type'    => 'Zend\Form\Element\Collection',
            'options' => [
                'count' => 0,
                'template_placeholder' => '__index__',
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $questionFieldset,
                
            ],
        ]);
    }

   /**
     * @return array
     */    
    public function getInputFilterSpecification()    
    {
        return [
            'id' => [
                'required' => false,
            ],
            'title' => [
                'name'     => 'title',
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
                            'max'      => 50,
                        ],
                    ],
                ],
            ],
            'description' => [
                'name'     => 'description',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 255,
                        ],
                    ],
                ],
            ],
            'language' => [
                'name'     => 'language',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 20,
                        ],
                    ],
                ],
            ],
            'tags' => [
                'required' => true,
            ],
            'questions' => [
                'required' => false,
            ],

            'rules' => [
                'required' => false,
            ],
            'level' => [
                'required' => false,
            ],
        ];
    }
}