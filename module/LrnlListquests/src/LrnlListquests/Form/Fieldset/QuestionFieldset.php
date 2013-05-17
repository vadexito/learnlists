<?php
namespace LrnlListquests\Form\Fieldset;

use LrnlListquests\Entity\Question;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class QuestionFieldset extends Fieldset implements 
    InputFilterProviderInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    public function __construct($name='question',$options=NULL)
    {
        parent::__construct($name,$options);
    
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
            ],
        ]);
        
        $this->add([
            'name' => 'text',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'text',
                'autocomplete'    => 'off',
                'class' => 'input-xxlarge',
                'required' => 'required'
            ],
            'options' => [
                'label' => _('Text')
            ],
        ]);
        
        $this->add([
            'name' => 'answer',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'answer',
                'class' => 'input-medium',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Answer')
            ],
        ]);
        
        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'comment',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Comment')
            ],
        ]);
    }

    public function init()
    {
        if (!$this->getServiceLocator()){
            throw new ServiceNotFoundException('The form element manager was not initialized. Use the form element manager to initiate the fieldset');
        }
        
        $formElementManager = $this->getServiceLocator();
        $applicationServices = $formElementManager->getServiceLocator();
        $om = $applicationServices->get('Doctrine\ORM\EntityManager');
        $object = new Question();
        $this->setHydrator(new DoctrineHydrator($om, get_class($object)))
             ->setObject($object);
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
            'text' => [
                'required' => true,
            ],
            'answer' => [
                'required' => false,
            ],
            'comment' => [
                'required' => false,
            ],
        ];
    }
}