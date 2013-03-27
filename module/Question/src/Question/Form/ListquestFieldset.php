<?php
namespace Question\Form;

use Question\Entity\Listquest;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;


class ListquestFieldset extends Fieldset implements InputFilterProviderInterface
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('listquest');
        $this->setObjectManager($om);        
        $this->setHydrator(new DoctrineHydrator($om, 'Question\Entity\Listquest'))
             ->setObject(new Listquest());
    
    
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
            'type'    => 'Zend\Form\Element\Collection',
            'name' => 'questions',
            'options' => [
                'count' => 1,
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
                'required' => true,
            ],
            'rules' => [
                'required' => false,
            ],
            'level' => [
                'required' => false,
            ],
            'tags' => [
                'required' => true,
            ],
            'questions' => [
                'required' => false,
            ],
        ];
    }
}