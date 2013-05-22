<?php
namespace LrnlListquests\Form\Fieldset;

use Application\Form\Fieldset\AbstractEntityManagerAwareFieldset;

class ListquestFieldset extends AbstractEntityManagerAwareFieldset
{
    public function __construct($name = 'listquest',$options = null)
    {
        parent::__construct($name,$options);
    
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
    }
    
    public function init()
    {
        $formElementManager = $this->getServiceLocator();
        if (!$formElementManager){
            throw new ServiceNotFoundException('The form element manager was not initialized. Use the form element manager to initiate the fieldset');
        }
        
        $this->add([            
            'type' => 'CategoryFieldset',
        ]); 
        
        $this->add([
            'type' => 'LevelFieldset',
        ]); 
        
        $this->add([
            'type' => 'LanguageFieldset',
        ]);
        
        $this->add([     
            'name' => 'tags',
            'type' => 'Zend\Form\Element\Collection',            
            'options' => [
                'count' => 1,
                'template_placeholder' => '__index__',
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $formElementManager->get('TagFieldset'),
            ],
        ]);
        
        $this->add([
            'name' => 'questions',
            'type'    => 'Zend\Form\Element\Collection',
            'options' => [
                'count' => 0,
                'template_placeholder' => '__index__',
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => $formElementManager->get('QuestionFieldset'),
                
            ],
        ]);
        
        
        //initialize the entity class
        $applicationServices = $formElementManager->getServiceLocator();
        $options = $applicationServices->get('lrnllistquests_module_options');
        $this->setEntityClass($options->getListquestEntityClass()); 
        
        //the entity class has to be first initialized before the init
        parent::init();
    }
}