<?php
namespace LrnlListquests\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use LrnlListquests\Form\Fieldset\CategoryFieldset;
use LrnlListquests\Form\Fieldset\LevelFieldset;
use LrnlListquests\Form\Fieldset\LanguageFieldset;

class ListquestFieldset extends Fieldset implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    public function __construct($name = 'listquest')
    {
        parent::__construct($name);
    
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
        $applicationServices = $formElementManager->getServiceLocator();
        $om = $applicationServices->get('Doctrine\ORM\EntityManager');  
        
        $this->add(new CategoryFieldset($om,'category'));
        $this->add(new LevelFieldset($om,'level'));
        $this->add(new LanguageFieldset($om,'language'));
        
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
        
        
        
        $options = $applicationServices->get('lrnllistquests_module_options');
        $listquestEntityClass = $options->getListquestEntityClass();               
        $doctrineHydrator = new DoctrineHydrator(
            $om,
            $listquestEntityClass
        ); 

        $this->setHydrator($doctrineHydrator);
        $this->setObject(new $listquestEntityClass); 
    }
}