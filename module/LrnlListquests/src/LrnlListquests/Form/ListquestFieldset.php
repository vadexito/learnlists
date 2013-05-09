<?php
namespace LrnlListquests\Form;

use LrnlListquests\Entity\Listquest;
use Zend\Form\Fieldset;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;

use LrnlListquests\Form\Element\Picture;

class ListquestFieldset extends Fieldset
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
            'type' => 'select',
            'attributes' => [
                'id'    => 'language',
            ],
            'options' => [
                'label' => _('Language'),
                'value_options' => [
                    'English' => _('English'),
                    'German' => _('German'),
                    'French' => _('French'),
                    'Polish' => _('Polish'),
                ],
            ],
        ]);
        
        $this->add([
            'name' => 'level',
            'type' => 'select',
            'attributes' => [
                'id'    => 'level'
            ],
            'options' => [
                'label' => _('Level'),
                'value_options' => [
                    _('Top level') => _('Top level'),
                    _('Very high level') => _('Very high level'),
                    _('High level') => _('High level'),
                    _('Good level') => _('Good level'),
                    _('Average plus level') => _('Average plus level'),
                    _('Average level') => _('Average level'),
                    _('Basic level') => _('Basic level'),
                    _('Elementary level') => _('Elementary level'),
                ],
            ],
        ]);
        
        $pictureCategory = new Picture('category_picture');
        $this->add($pictureCategory);
        
        
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
}